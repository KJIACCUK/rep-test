<?php
require Yii::getPathOfAlias('application.vendor').'/autoload.php';

class UserController extends ApiController
{

    public function filters()
    {
        return array(
            array('ApiAccessControlFilter'),
            array(
                'ApiParamFilter - addVerificationRequest, addFriendshipRequest, inviteSocialFriends, getSettings, saveSettings, saveMessenger, sendFeedback',
                'param' => 'image',
                'function' => 'FilterHelper::checkImage'
            ),
            array(
                'ApiParamFilter + setPushToken, deletePushToken',
                'param' => 'pushToken',
                'function' => 'FilterHelper::checkNotEmpty',
            ),
            array(
                'ApiParamFilter + profileComplete, setPushToken, deletePushToken, getProfile, saveProfile, saveMessenger, saveAvatar, changePassword, connectSocial, disconnectSocial',
                'param' => 'platform',
                'function' => 'FilterHelper::checkPlatform'
            ),
            array(
                'ApiParamFilter + connectSocial, disconnectSocial, sendInvites',
                'param' => 'social',
                'function' => 'FilterHelper::checkSocial'
            ),
            array(
                'ApiParamFilter + connectSocial, sendInvites',
                'param' => 'accessToken',
                'function' => 'FilterHelper::checkNotEmpty',
            ),
            array(
                'ApiParamFilter + sendInvites',
                'param' => 'to',
                'function' => 'FilterHelper::checkNotEmpty',
            ),
            array(
                'ApiParamFilter + addVerificationRequest, saveMessenger',
                'param' => 'messenger',
                'function' => 'FilterHelper::checkMessenger'
            ),
            array(
                'ApiParamFilter + saveSettings',
                'param' => 'settings',
                'function' => 'FilterHelper::checkNotEmpty',
            ),
            array(
                'ApiParamFilter + sendFeedback',
                'param' => 'title',
                'function' => 'FilterHelper::checkNotEmpty',
            ),
            array(
                'ApiParamFilter + sendFeedback',
                'param' => 'description',
                'function' => 'FilterHelper::checkNotEmpty',
            ),
            array('ApiProfileIsFilledFilter - profileComplete, setPushToken, deletePushToken, getProfile')
        );
    }

    public function actionProfileComplete()
    {
        $currentUser = $this->getUser();
        if (!$currentUser->isFilled) {
            if (!$currentUser->login) {
                $currentUser->login = $currentUser->email;
            }
            $currentUser->oldLogin = $currentUser->login;
            $currentUser->needChangeLogin = ($currentUser->login && $currentUser->email && $currentUser->login == $currentUser->email);
            $currentUser->setScenario('api_profile_complete');
            $currentUser->oldPassword = $currentUser->password;
            $currentUser->attributes = Api::getParams(array('firstname', 'lastname', 'email', 'birthday', 'phone', 'phoneCode', 'password'));

            if (!$currentUser->save()) {
                Api::respondValidationError($currentUser);
            }
        }

        if (filter_var($currentUser->login, FILTER_VALIDATE_EMAIL) === false && $currentUser->login != "") { //&& !UserHelper::isLogged($currentUser->userId)) { // Send SMS Login
            $response = UserHelper::sendSMSLogin($currentUser->login);
            Yii::log("SMS for User Login ".$currentUser->login." #".$currentUser->userId, CLogger::LEVEL_WARNING, "API");
            Yii::log("SMS system answer: " . print_r($response, TRUE), CLogger::LEVEL_WARNING);
        } else {
            Yii::log("User #" . $currentUser->userId . " was logged earlier", CLogger::LEVEL_WARNING, 'API');
        }
        
        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionSetPushToken()
    {
        list($pushToken, $platform) = array_values(Api::getParams(array('pushToken', 'platform')));
        $currentUser = $this->getUser();

        UserPushToken::model()->deleteAllByAttributes(array('userId' => $currentUser->userId, 'apiToken' => $this->getToken(), 'platform' => $platform));

        $userPushToken = new UserPushToken();
        $userPushToken->userId = $currentUser->userId;
        $userPushToken->apiToken = $this->getToken();
        $userPushToken->platform = $platform;
        $userPushToken->pushToken = $pushToken;
        $userPushToken->dateCreated = time();

        if (!$userPushToken->save()) {
            throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
        }

        $currentUser->refresh();

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), $platform);
        Api::respondSuccess($response);
    }

    public function actionDeletePushToken()
    {
        $currentUser = $this->getUser();
        $platform = Api::getParam('platform');

        UserPushToken::model()->deleteAllByAttributes(array(
            'userId' => $currentUser->userId,
            'apiToken' => $this->getToken(),
            'pushToken' => Api::getParam('pushToken'),
            'platform' => $platform
        ));

        $currentUser->refresh();

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), $platform);
        Api::respondSuccess($response);
    }

    public function actionGetProfile()
    {
        $currentUser = $this->getUser();
        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionSaveProfile()
    {
        $currentUser = $this->getUser();
        $currentUser->setScenario('api_update');
        $currentUser->oldLogin = $currentUser->login;
        $currentUser->needChangeLogin = ($currentUser->login && $currentUser->email && $currentUser->login == $currentUser->email);
        $currentUser->attributes = Api::getParams(array('email', 'firstname', 'lastname', 'phone', 'birthday', 'phoneCode', 'messenger', 'messengerLogin', 'favoriteMusicGenre', 'favoriteCigaretteBrand'));

        if (!$currentUser->save()) {
            Api::respondValidationError($currentUser);
        }

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionSaveAvatar()
    {
        $currentUser = $this->getUser();

        $currentUser->setScenario('api_update_image');
        $currentUser->imageFile = CUploadedFile::getInstanceByName('imageFile');
        if (!$currentUser->save()) {
            Api::respondValidationError($currentUser);
        }

        $currentUser->refresh();

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionSaveMessenger()
    {
        $currentUser = $this->getUser();

        $currentUser->setScenario('api_update_messenger');
        $currentUser->attributes = Api::getParams(array('messenger', 'messengerLogin'));
        if (!$currentUser->save()) {
            Api::respondValidationError($currentUser);
        }

        $currentUser->refresh();

        if (!VerificationHelper::isOperatorWork()) {
            if (!$currentUser->isVerified && $currentUser->messenger && $currentUser->messengerLogin) {
                $verificationRequest = new UserVerificationRequest('missing');
                $verificationRequest->messenger = $currentUser->messenger;
                $verificationRequest->messengerLogin = $currentUser->messengerLogin;
                $verificationRequest->callDate = date('d.n.Y');
                $verificationRequest->callTime = date('H:i');
                $verificationRequest->userId = $currentUser->userId;
                $verificationRequest->status = UserVerificationRequest::STATUS_OPENED;
                $verificationRequest->dateCreated = time();
                $verificationRequest->isMissed = 1;
                $verificationRequest->save();
            }
        }

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionChangePassword()
    {
        $currentUser = $this->getUser();

        $model = new ApiChangePassword();
        $model->attributes = Api::getParams(array('oldPassword', 'newPassword'));
        $model->setOldPasswordCrypted($currentUser->password);
        if (!$model->validate()) {
            Api::respondValidationError($model);
        }

        $currentUser->password = CommonHelper::md5($model->newPassword);
        $currentUser->save(false);

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionAddVerificationRequest()
    {
        $currentUser = $this->getUser();

        if ($currentUser->isVerified) {
            Api::respondSuccess();
        }

        $verificationRequest = new UserVerificationRequest('api_insert');
        $verificationRequest->attributes = Api::getParams(array('messenger', 'messengerLogin', 'callDate', 'callTime'));
        $verificationRequest->userId = $currentUser->userId;
        $verificationRequest->status = UserVerificationRequest::STATUS_OPENED;
        $verificationRequest->dateCreated = time();
        $verificationRequest->isMissed = 0;

        if (!$verificationRequest->save()) {
            Api::respondValidationError($verificationRequest);
        }

        Api::respondSuccess();
    }

    public function actionAddPhotoVerificationRequest()
    {
        $currentUser = $this->getUser();
        
        if ($currentUser->isVerified) {
            Api::respondSuccess();
        }

        $verificationRequest = new UserVerificationRequest('api_photo_verification');
        $verificationRequest->attachmentFile = CUploadedFile::getInstanceByName('imageFile');
        $verificationRequest->messenger = 'pv';
        $verificationRequest->messengerLogin = 'photoverification';
        $verificationRequest->callDate = date('d.n.Y');
        $verificationRequest->callTime = date('H:i');
        $verificationRequest->userId = $currentUser->userId;
        $verificationRequest->status = UserVerificationRequest::STATUS_OPENED;
        $verificationRequest->favoriteCigaretteBrand = Api::getParam('favoriteCigaretteBrand');
        $verificationRequest->dateCreated = time();
        $verificationRequest->isPhotoVerification = 1;
        
        if (!$verificationRequest->save()) {
            Api::respondValidationError($verificationRequest);
        }
        
        $currentUser->favoriteCigaretteBrand = $verificationRequest->favoriteCigaretteBrand;
        $currentUser->save(false);

        Api::respondSuccess();
    }

    public function actionConnectSocial()
    {
        $social = Api::getParam('social');
        $socialId = null;

        if ($social == UserSocial::TYPE_FACEBOOK) {
            $facebook = new Facebook(array(
                'appId' => Yii::app()->params['facebook']['appId'],
                'secret' => Yii::app()->params['facebook']['secret']
            ));

            $facebook->setAccessToken(Api::getParam('accessToken'));

            $socialId = $facebook->getUser();

            if (!$socialId) {
                Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
            }
        } elseif ($social == UserSocial::TYPE_VKONTAKTE) {
            $vk = new \BW\Vkontakte(array(
                'app_id' => Yii::app()->params['vkontakte']['appId'],
                'secret' => Yii::app()->params['vkontakte']['secret'],
                'redirect_uri' => Yii::app()->request->getBaseUrl(true),
            ));

            $accessTokenString = Api::getParam('accessToken');
            $accessToken = new stdClass();
            $accessToken->access_token = $accessTokenString;

            $vk->setAccessToken(CJSON::encode($accessToken));

            $vkUserProfile = $vk->api('users.get', array(
                'access_token' => $accessTokenString,
                '.uids' => Api::getParam('uid'),
                '&api_id' => Yii::app()->params['vkontakte']['appId'],
                '&fields' => array('bdate', 'photo_big')
            ));

            if ($vkUserProfile) {
                if (isset($vkUserProfile['response']) && isset($vkUserProfile['response'][0])) {
                    $vkUserProfile = $vkUserProfile['response'][0];
                    $socialId = $vkUserProfile['uid'];
                } else {
                    Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                }
            } else {
                Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
            }
        } elseif ($social == UserSocial::TYPE_TWITTER) {
            $settings = array(
                'oauth_access_token' => Api::getParam('accessToken'),
                'oauth_access_token_secret' => Api::getParam('accessTokenSecret'),
                'consumer_key' => Yii::app()->params['twitter']['key'],
                'consumer_secret' => Yii::app()->params['twitter']['secret']
            );

            $url = 'https://api.twitter.com/1.1/account/verify_credentials.json';
            $requestMethod = 'GET';

            try {
                $twitter = new TwitterAPIExchange($settings);
                $twitterUserProfile = $twitter->buildOauth($url, $requestMethod)->performRequest();

                if ($twitterUserProfile && ($twitterUserProfile = CJSON::decode($twitterUserProfile))) {
                    $socialId = $twitterUserProfile['id'];
                }
            } catch (Exception $ex) {
                Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
            }
        }

        $currentUser = $this->getUser();

        $userSocial = UserSocial::model()->findByAttributes(array('userId' => $currentUser->userId, 'type' => $social, 'socialId' => $socialId));

        if (!$userSocial) {
            $userSocial = new UserSocial();
            $userSocial->userId = $currentUser->userId;
            $userSocial->type = $social;
            $userSocial->socialId = $socialId;
            $userSocial->dateCreated = time();
            if (!$userSocial->save()) {
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }
        }

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionDisconnectSocial()
    {
        $currentUser = $this->getUser();

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('userId' => $currentUser->userId, 'type' => Api::getParam('social')));
        $criteria->order = 'dateCreated DESC';

        $userSocial = UserSocial::model()->find($criteria);

        if ($userSocial) {
            if (!$userSocial->delete()) {
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }
        }

        $response = array();
        $response['user'] = UserHelper::export($currentUser, 'detail, mine');
        $response['user']['image'] = CommonHelper::getImageLink($currentUser->image, Api::getParam('image'));
        $response['user']['token'] = $this->getToken();
        $response['user']['pushToken'] = (string)UserHelper::getPushToken($currentUser, $this->getToken(), Api::getParam('platform'));
        Api::respondSuccess($response);
    }

    public function actionGetFriends()
    {
        $currentUser = $this->getUser();
        $image = Api::getParam('image');
        $onlyOnline = Api::getParam('onlyOnline', false);
        $response = array();
        $response['users'] = array();

        foreach ($currentUser->friends as $user) {
            $data = UserHelper::export($user, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($user->image, $image);
            if ($onlyOnline) {
                if ($data['isOnline']) {
                    $response['users'][] = $data;
                }
            } else {
                $response['users'][] = $data;
            }
        }
        Api::respondSuccess($response);
    }

    public function actionGetUsers()
    {
        $currentUser = $this->getUser();
        $image = Api::getParam('image');
        $response = array();
        $response['users'] = array();

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';

        $criteria->addNotInCondition('u.userId', array($currentUser->userId));

        if (($search = Api::getParam('search'))) {
            $criteria->addSearchCondition('u.name', $search, true);
        }

        $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE userId = u.userId) AS subscriptionsCount';

        $criteria->addCondition('u.userId NOT IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');
        $criteria->params[':currentUserId'] = $currentUser->userId;

        $criteria->offset = Api::getParam('offset', 0);
        $criteria->limit = Api::getParam('limit', 50);
        $criteria->order = 'u.name ASC';

        $users = User::model()->findAll($criteria);

        foreach ($users as $user) {
            $data = UserHelper::export($user, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($user->image, $image);
            $response['users'][] = $data;
        }

        shuffle($response['users']);

        $response['total'] = (int)User::model()->count($criteria);
        Api::respondSuccess($response);
    }

    public function actionGetUser()
    {
        $currentUser = $this->getUser();
        $userId = Api::getParam('userId');

        if ($currentUser->userId == $userId) {
            throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'User ID cannot be equals ID of current user'));
        }

        $user = User::model()->findByPk($userId);
        /* @var $user User */

        if (!$user) {
            throw new ApiException(Api::CODE_NOT_FOUND);
        }

        $response = array();
        $response['user'] = UserHelper::export($user, 'online, detail, friends');
        $response['user']['image'] = CommonHelper::getImageLink($user->image, Api::getParam('image'));
        Api::respondSuccess($response);
    }

    public function actionGetUserFriends()
    {
        $currentUser = $this->getUser();
        $userId = Api::getParam('userId');
        $image = Api::getParam('image');
        $response = array();
        $response['users'] = array();

        if ($currentUser->userId == $userId) {
            throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'User ID cannot be equals ID of current user'));
        }

        $user = User::model()->with('friends', 'friendsCount')->findByPk($userId);
        /* @var $user User */

        if (!$user) {
            throw new ApiException(Api::CODE_NOT_FOUND);
        }

        foreach ($user->friends as $friend) {
            $data = UserHelper::export($friend, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($friend->image, $image);
            $response['users'][] = $data;
        }
        Api::respondSuccess($response);
    }

    public function actionAddFriendshipRequest()
    {
        $currentUser = $this->getUser();
        $userId = Api::getParam('userId');

        if ($currentUser->userId == $userId) {
            throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Cannot add me to my friends.'));
        }

        $user = User::model()->findByPk($userId);
        /* @var $user User */

        if (!$user) {
            throw new ApiException(Api::CODE_NOT_FOUND);
        }

        if (UserFriend::model()->countByAttributes(array('userId' => $currentUser->userId, 'friendId' => $user->userId))) {
            Api::respondSuccess();
        }

        if (UserFriendRequest::model()->countByAttributes(array('userId' => $currentUser->userId, 'recipientId' => $user->userId))) {
            Api::respondSuccess();
        }

        if (($requestToMe = UserFriendRequest::model()->findByAttributes(array('userId' => $user->userId, 'recipientId' => $currentUser->userId)))) {
            $transaction = Yii::app()->db->beginTransaction();
            $time = time();

            if (!$requestToMe->delete()) {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $friend = new UserFriend();
            $friend->userId = $currentUser->userId;
            $friend->friendId = $user->userId;
            $friend->dateCreated = $time;

            if (!$friend->save()) {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $friend2 = new UserFriend();
            $friend2->userId = $user->userId;
            $friend2->friendId = $currentUser->userId;
            $friend2->dateCreated = $time;

            if (!$friend2->save()) {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            if (!UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST_ADDED, $user->userId, array('userId' => $currentUser->userId))) {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $transaction->commit();
        } else {
            $transaction = Yii::app()->db->beginTransaction();

            $request = new UserFriendRequest();
            $request->userId = $currentUser->userId;
            $request->recipientId = $user->userId;
            $request->dateCreated = time();

            if (!$request->save()) {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            if (!UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST, $user->userId, array('userId' => $currentUser->userId))) {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $transaction->commit();
        }

        Api::respondSuccess();
    }

    public function actionGetSettings()
    {
        $currentUser = $this->getUser();
        $response['settings'] = UserNotificationsHelper::getSettings($currentUser->userId);
        Api::respondSuccess($response);
    }

    public function actionSaveSettings()
    {
        $currentUser = $this->getUser();
        $settings = Api::getParam('settings');
        if (UserNotificationsHelper::saveSettings($currentUser->userId, $settings)) {
            $response['settings'] = $settings;
            Api::respondSuccess($response);
        } else {
            Api::respondError(Api::CODE_BAD_REQUEST, Yii::t('application', 'Неверное значение настроек'));
        }
    }

    public function actionSendFeedback()
    {
        $currentUser = $this->getUser();
        $model = new ApiSendFeedback();
        $model->attributes = Api::getParams(array('title', 'description'));
        if (!$model->validate()) {
            Api::respondValidationError($model);
        }

        EmailHelper::send(Yii::app()->params['feedbackEmail'], EmailHelper::TYPE_FEEDBACK, array('title' => $model->title, 'description' => $model->description, 'user' => $currentUser));
        Api::respondSuccess();
    }

    public function actionSendInvites()
    {
        $currentUser = $this->getUser();
        $social = Api::getParam('social');
        $to = Api::getParam('to');
        $socialId = null;

        $point = Point::model()->findByAttributes(array('pointKey' => Point::KEY_SOCIAL_INVITE));
        /* @var $point Point */

        $pointsCount = 0;
        $pointsVk = PointUser::model()->findAllByAttributes(array('pointId' => $point->pointId, 'userId' => $currentUser->userId));
        foreach ($pointsVk as $item) {
            $p = CJSON::decode($item->params);
            if (isset($p['vkId'])) {
                $pointsCount++;
            }
        }

        if ($social == UserSocial::TYPE_FACEBOOK) {
            $facebook = new Facebook(array(
                'appId' => Yii::app()->params['facebook']['appId'],
                'secret' => Yii::app()->params['facebook']['secret']
            ));

            $facebook->setAccessToken(Api::getParam('accessToken'));

            $socialId = $facebook->getUser();

            if ($socialId) {
                try {
                    foreach ($to as $id) {
                        $params = array('facebookId' => $id);
                        if (!PointHelper::hasPoint(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params)) {
                            $inviteRequest = $facebook->api('/'.$id.'/apprequests', 'GET');
                            if (isset($inviteRequest['data']) && is_array($inviteRequest['data'])) {
                                foreach ($inviteRequest['data'] as $data) {
                                    if ($data['application']['id'] == Yii::app()->params['facebook']['appId'] && $data['from']['id'] == $socialId) {
                                        PointHelper::addPoints(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (FacebookApiException $ex) {
                    Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Validation error.'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                }
            } else {
                Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Validation error.'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
            }
        } elseif ($social == UserSocial::TYPE_VKONTAKTE) {
            $vk = new \BW\Vkontakte(array(
                'app_id' => Yii::app()->params['vkontakte']['appId'],
                'secret' => Yii::app()->params['vkontakte']['secret'],
                'redirect_uri' => Yii::app()->request->getBaseUrl(true),
            ));

            $accessTokenString = Api::getParam('accessToken');
            $accessToken = new stdClass();
            $accessToken->access_token = $accessTokenString;

            $vk->setAccessToken(CJSON::encode($accessToken));

            foreach ($to as $id) {
                $params = array('vkId' => $id);
                if (!PointHelper::hasPoint(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params)) {
                    $message = Yii::t('application', 'Будь в курсе всех тусовок! Первым узнавай какие вечеринки выбирают твои друзья! Приложение БУДУТАМ поможет словить ритм твоего окружения и откроет для тебя самые популярные мероприятия твоего города. Скачивай Android – версию: ({androidLink}) или используй Facebook версию: ({facebookLink})', array('{androidLink}' => Yii::app()->params['androidStoreLink'], '{facebookLink}' => Yii::app()->params['facebookLink']));

                    $vkPostWall = $vk->api('wall.post', array(
                        'access_token' => $accessTokenString,
                        '&message' => $message,
                        '&attachments' => 'photo7095013_336794242',
                        '&owner_id' => $id
                    ));

                    if ($vkPostWall) {
                        if (isset($vkPostWall['response']) && isset($vkPostWall['response']['post_id'])) {
                            if ($pointsCount < 300) {
                                PointHelper::addPoints(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params);
                            }
                        } else {
                            Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                        }
                    } else {
                        Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                    }
                }
            }
        } elseif ($social == UserSocial::TYPE_TWITTER) {
            $settings = array(
                'oauth_access_token' => Api::getParam('accessToken'),
                'oauth_access_token_secret' => Api::getParam('accessTokenSecret'),
                'consumer_key' => Yii::app()->params['twitter']['key'],
                'consumer_secret' => Yii::app()->params['twitter']['secret']
            );

            $url = 'https://api.twitter.com/1.1/direct_messages/new.json';
            $requestMethod = 'POST';

            foreach ($to as $id) {
                $params = array('twitterId' => $id);
                if (!PointHelper::hasPoint(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params)) {
                    $message = Yii::t('application', 'Как быть в курсе тусовок? Как узнать планы друзей? Качай “БУДУТАМ”.').
                    Yii::t('application', 'Android').' - '.Yii::app()->params['androidStoreLink'].', '.Yii::t('application', 'Facebook').' - '.Yii::app()->params['facebookLink'];

                    try {
                        $twitter = new TwitterAPIExchange($settings);
                        $twitterMessage = $twitter
                        ->setPostfields(array('user_id' => $id, 'text' => $message))
                        ->buildOauth($url, $requestMethod)
                        ->performRequest();

                        if ($twitterMessage && ($twitterMessage = CJSON::decode($twitterMessage)) && isset($twitterMessage['id'])) {
                            if ($pointsCount < 300) {
                                PointHelper::addPoints(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params);
                            }
                        }
                    } catch (Exception $ex) {
                        Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                    }
                }

                sleep(1);
            }
        }

        Api::respondSuccess();
    }

    public function actionSocialShare()
    {
        $currentUser = $this->getUser();
        $eventId = Api::getParam('eventId');
        $social = Api::getParam('social');

        PointHelper::addPoints(Point::KEY_SOCIAL_SHARE, $currentUser->userId, array('social' => $social, 'event_id' => $eventId));
        Api::respondSuccess();
    }
}
