<?php
require Yii::getPathOfAlias('application.vendor').'/autoload.php';

class UserController extends WebController
{
    public $usersLimit = 10;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
            array(
                'ExistsFilter + detail, addFriendshipRequest, userFriends',
                'param' => 'userId',
                'function' => 'FilterHelper::checkUserExists',
                'errorMessage' => Yii::t('application', 'Пользователь не найден')
            ),
            array(
                'ParamFilter + addVerificationRequest, verificationCall',
                'param' => 'messenger',
                'function' => 'FilterHelper::checkMessenger'
            ),
            array('ProfileIsFilledFilter - profileComplete')
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'edit', 'saveAvatar', 'profileComplete', 'friends', 'search', 'detail',
                    'addFriendshipRequest', 'userFriends', 'askVerification', 'verification', 'verificationCall', 'addVerificationRequest',
                    'verificationUploadPhoto', 'verificationMakePhoto', 'verificationMakePhotoSave', 'verificationSetFavoriteBrand', 'settings', 'feedback', 'help', 'saveInvitation'),
                'roles' => array('user'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $currentUser = $this->getUser();
        $user = UserHelper::export($currentUser, 'detail, mine');
        $user['image'] = CommonHelper::getImageLink($currentUser->image, '194x194');
        $this->render('index', array('user' => $user));
    }

    public function actionEdit()
    {
        $currentUser = $this->getUser();
        $currentUser->setScenario('update');
        $user = UserHelper::export($currentUser, 'detail, mine');
        $user['image'] = CommonHelper::getImageLink($currentUser->image, '194x194');

        $currentUser->firstname = $user['firstname'];
        $currentUser->lastname = $user['lastname'];
        $currentUser->birthdayYear = date('Y', $currentUser->birthday);
        $currentUser->birthdayMonth = date('n', $currentUser->birthday);
        $currentUser->birthdayDay = date('j', $currentUser->birthday);
        $currentUser->oldLogin = $currentUser->login;
        $currentUser->needChangeLogin = ($currentUser->login && $currentUser->email && $currentUser->login == $currentUser->email);

        if (isset($_POST['User'])) {
            $currentUser->attributes = $_POST['User'];
            if ($currentUser->save()) {
                Web::flashSuccess(Yii::t('application', 'Профиль сохранен.'));
                $this->redirect(array('user/index'));
            }
        }

        $currentUser->oldPassword = null;
        $currentUser->newPassword = null;

        $this->render('edit', array('user' => $user, 'model' => $currentUser));
    }

    public function actionSaveAvatar()
    {
        $currentUser = $this->getUser();
        $currentUser->setScenario('update_image');
        $currentUser->imageFile = CUploadedFile::getInstance($currentUser, 'imageFile');

        if ($currentUser->save()) {
            Web::jsonSuccess(array('image' => CommonHelper::getImageLink($currentUser->image, '194x194')));
        }

        Web::jsonError($currentUser->getError('imageFile'));
    }

    public function actionProfileComplete()
    {
        $currentUser = $this->getUser();

        if ($currentUser->isFilled) {
            $this->redirect(array('user/index'));
        }

        $currentUser->setScenario('profile_complete');
        $currentUser->oldPassword = $currentUser->password;
        $currentUser->oldLogin = $currentUser->login;
        $currentUser->password = null;
        $currentUser->passwordConfirm = null;
        $currentUser->birthdayYear = date('Y', $currentUser->birthday);
        $currentUser->birthdayMonth = date('n', $currentUser->birthday);
        $currentUser->birthdayDay = date('j', $currentUser->birthday);

        if ($currentUser->name) {
            $name = explode(' ', $currentUser->name);
            if (isset($name[0])) {
                $currentUser->lastname = $name[0];
            }

            if (isset($name[1])) {
                $currentUser->firstname = $name[1];
            }
        }

        $currentUser->needChangeLogin = ($currentUser->login && $currentUser->email && $currentUser->login == $currentUser->email);

        if (isset($_POST['User'])) {
            $currentUser->attributes = $_POST['User'];
            if ($currentUser->save()) {
                Web::flashSuccess(Yii::t('application', 'Профиль сохранен.'));

                if (filter_var($currentUser->login, FILTER_VALIDATE_EMAIL) === false && $currentUser->login != "") { //&& !UserHelper::isLogged($currentUser->userId)) { // Send SMS Login
                    $response = UserHelper::sendSMSLogin($currentUser->login);
                    Yii::log("SMS for User Login ".$currentUser->login." #".$currentUser->userId, CLogger::LEVEL_WARNING, "WEB");
                    Yii::log("SMS system answer: " . print_r($response, TRUE), CLogger::LEVEL_WARNING);
                } else {
                    Yii::log("User #" . $currentUser->userId . " was logged earlier", CLogger::LEVEL_WARNING, 'WEB');
                }

                $this->redirect(array('event/index'));
            }
        }

        $currentUser->password = null;
        $currentUser->passwordConfirm = null;

        Yii::app()->user->returnUrl = $this->createAbsoluteUrl('user/profileComplete');

        $this->render('profile_complete', array('model' => $currentUser));
    }

    public function actionFriends()
    {
        $currentUser = $this->getUser();
        $friends = array();

        foreach ($currentUser->friends as $user) {
            $data = UserHelper::export($user, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($user->image, '82x80');
            $friends[] = $data;
        }

        $this->render('friends', array('friends' => $friends));
    }

    public function actionSearch()
    {
        $currentUser = $this->getUser();
        $users = array();

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';

        $criteria->addNotInCondition('u.userId', array($currentUser->userId));

        if (($search = Web::getParam('search'))) {
            $criteria->addSearchCondition('u.name', $search, true);
        }

        $criteria->addCondition('u.userId NOT IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');
        $criteria->params[':currentUserId'] = $currentUser->userId;

        $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE userId = u.userId) AS subscriptionsCount';

        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->usersLimit);
        $criteria->order = 'subscriptionsCount DESC, u.name ASC';

        $rows = User::model()->findAll($criteria);

        foreach ($rows as $user) {
            $data = UserHelper::export($user, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($user->image, '80x82');
            $users[] = $data;
        }

        shuffle($users);

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_search_items', array('users' => $users));
        } else {
            $this->render('search', array('users' => $users));
        }
    }

    public function actionDetail()
    {
        $currentUser = $this->getUser();
        $userId = Web::getParam('userId');

        if ($currentUser->userId == $userId) {
            $this->redirect(array('user/index'));
        }

        $row = User::model()->findByPk($userId);
        /* @var $row User */

        $user = UserHelper::export($row, 'online, detail, friends');
        $user['image'] = CommonHelper::getImageLink($row->image, '194x194');

        $this->render('detail', array('user' => $user));
    }

    public function actionAddFriendshipRequest()
    {
        $currentUser = $this->getUser();
        $userId = Web::getParam('userId');

        if ($currentUser->userId == $userId) {
            Web::jsonError(Yii::t('application', 'Нельзя добавить себя в друзья'));
        }

        $user = User::model()->findByPk($userId);
        /* @var $user User */

        if (UserFriend::model()->countByAttributes(array('userId' => $currentUser->userId, 'friendId' => $user->userId))) {
            Web::jsonSuccess();
        }

        if (UserFriendRequest::model()->countByAttributes(array('userId' => $currentUser->userId, 'recipientId' => $user->userId))) {
            Web::jsonSuccess();
        }

        if (($requestToMe = UserFriendRequest::model()->findByAttributes(array('userId' => $user->userId, 'recipientId' => $currentUser->userId)))) {
            $transaction = Yii::app()->db->beginTransaction();
            $time = time();

            if (!$requestToMe->delete()) {
                $transaction->rollback();
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз.'));
            }

            $friend = new UserFriend();
            $friend->userId = $currentUser->userId;
            $friend->friendId = $user->userId;
            $friend->dateCreated = $time;

            if (!$friend->save()) {
                $transaction->rollback();
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз.'));
            }

            $friend2 = new UserFriend();
            $friend2->userId = $user->userId;
            $friend2->friendId = $currentUser->userId;
            $friend2->dateCreated = $time;

            if (!$friend2->save()) {
                $transaction->rollback();
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз.'));
            }

            if (!UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST_ADDED, $user->userId, array('userId' => $currentUser->userId))) {
                $transaction->rollback();
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз.'));
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
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз.'));
            }

            if (!UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST, $user->userId, array('userId' => $currentUser->userId))) {
                $transaction->rollback();
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз.'));
            }

            $transaction->commit();
        }

        Web::jsonSuccess();
    }

    public function actionUserFriends()
    {
        $currentUser = $this->getUser();
        $users = array();

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';

        $criteria->addCondition('u.userId IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :selectedUserId)');

        $criteria->params[':selectedUserId'] = Web::getParam('userId');

        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->usersLimit);
        $criteria->order = 'u.name ASC';

        $rows = User::model()->findAll($criteria);

        foreach ($rows as $item) {
            /* @var $item User */
            $data = UserHelper::export($item, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($item->image, '80x82');
            $users[] = $data;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_user_friends_items', array('users' => $users));
        } else {
            $this->render('user_friends', array('users' => $users));
        }
    }

//    public function actionVerificationCall()
//    {
//        $currentUser = $this->getUser();
//        $messenger = Web::getParam('messenger');
//        $messengerLogin = '';
//
//        if ($currentUser->messenger == $messenger) {
//            $messengerLogin = $currentUser->messengerLogin;
//        }
//
//        if (Yii::app()->request->isAjaxRequest) {
//            $currentUser->setScenario('update_messenger');
//            $currentUser->messenger = $messenger;
//            $currentUser->messengerLogin = Web::getParam('messengerLogin');
//            if ($currentUser->save()) {
//                if (!VerificationHelper::isOperatorWork()) {
//                    if (!$currentUser->isVerified && $currentUser->messenger && $currentUser->messengerLogin) {
//                        $model = new UserVerificationRequest('missing');
//                        $model->messenger = $currentUser->messenger;
//                        $model->messengerLogin = $currentUser->messengerLogin;
//                        $model->userId = $currentUser->userId;
//                        $model->callDate = date('d.n.Y');
//                        $model->callTime = date('H:i');
//                        $model->status = UserVerificationRequest::STATUS_OPENED;
//                        $model->dateCreated = time();
//                        $model->isMissed = 1;
//                        $model->save();
//                    }
//                }
//                Web::jsonSuccess();
//            } else {
//                Web::jsonError($currentUser->getError('messengerLogin'));
//            }
//        } else {
//            $this->render('verification_call', array('messenger' => $messenger, 'messengerLogin' => $messengerLogin));
//        }
//    }

    public function actionAskVerification()
    {
        $currentUser = $this->getUser();
        if ($currentUser->isVerified) {
            $this->redirect(array('user/index'));
        }
        $this->render('ask_verification');
    }

    public function actionVerification()
    {
        $currentUser = $this->getUser();
        if ($currentUser->isVerified) {
            $this->redirect(array('user/index'));
        }
        $this->render('verification');
    }

//    public function actionAddVerificationRequest()
//    {
//        $currentUser = $this->getUser();
//        $messenger = Web::getParam('messenger');
//
//        if ($currentUser->isVerified) {
//            $this->redirect(array('user/index'));
//        }
//
//        $model = new UserVerificationRequest('insert');
//        $model->callDateDay = date('d');
//        $model->callDateMonth = date('n');
//        $model->callDateYear = date('Y');
//        $model->callTimeHours = date('H', strtotime('+1 hour'));
//        $model->callTimeMinutes = date('i');
//
//        if ($currentUser->messenger == $messenger && $currentUser->messengerLogin) {
//            $model->messengerLogin = $currentUser->messengerLogin;
//        }
//
//        if (isset($_POST['UserVerificationRequest'])) {
//            $model->attributes = $_POST['UserVerificationRequest'];
//            $model->userId = $currentUser->userId;
//            $model->messenger = $messenger;
//            $model->status = UserVerificationRequest::STATUS_OPENED;
//            $model->dateCreated = time();
//            $model->isMissed = 0;
//            if ($model->save()) {
//                Web::flashSuccess(Yii::t('application', 'Запрос на верификацию по {messenger} добавлен.', array('{messenger}' => $messenger)));
//                $this->redirect(array('user/index'));
//            }
//        }
//
//        $this->render('add_verification_request', array('model' => $model, 'messenger' => $messenger));
//    }

    public function actionVerificationUploadPhoto()
    {
        $currentUser = $this->getUser();
        $errors = array();

        $favoriteBrand = Yii::app()->user->getState('favoriteCigaretteBrand');
        if (!$favoriteBrand) {
            $this->redirect(array('user/verification'));
        }

        if (Yii::app()->request->isPostRequest) {
            $verificationRequest = new UserVerificationRequest('photo_verification');
            $verificationRequest->attachmentFile = CUploadedFile::getInstanceByName('imageFile');
            $verificationRequest->messenger = 'pv';
            $verificationRequest->messengerLogin = 'photoverification';
            $verificationRequest->callDate = date('d.n.Y');
            $verificationRequest->callTime = date('H:i');
            $verificationRequest->userId = $currentUser->userId;
            $verificationRequest->status = UserVerificationRequest::STATUS_OPENED;
            $verificationRequest->dateCreated = time();
            $verificationRequest->isPhotoVerification = 1;

            if ($verificationRequest->save()) {
                $currentUser->favoriteCigaretteBrand = $favoriteBrand;
                $currentUser->save(false);
                Yii::app()->user->setState('favoriteCigaretteBrand', $favoriteBrand, $favoriteBrand);
                Web::flashSuccess(Yii::t('application', 'При успешной верификации, полный доступ к приложению Вы получите в течение 24 часов -  Вам придёт подтверждение на e-mail!'));
                $this->redirect(array('user/index'));
            } else {
                $errors = $verificationRequest->getErrors('attachmentFile');
            }
        }
        $this->render('verification_upload_photo', array(
            'errors' => $errors
        ));
    }

    public function actionVerificationMakePhoto()
    {
        $favoriteBrand = Yii::app()->user->getState('favoriteCigaretteBrand');
        if (!$favoriteBrand) {
            $this->redirect(array('user/verification'));
        }
        $this->render('verification_make_photo');
    }

    public function actionVerificationMakePhotoSave()
    {
        $currentUser = $this->getUser();

        if (!Yii::app()->request->isPostRequest) {
            Yii::app()->end();
        }

        $favoriteBrand = Yii::app()->user->getState('favoriteCigaretteBrand');
        if (!$favoriteBrand) {
            Yii::app()->end();
        }

        $fileContent = file_get_contents('php://input');
        if (md5($fileContent) == '7d4df9cc423720b7f1f3d672b89362be') {
            // Blank image. We don't need this one.
            Yii::app()->end();
        }

        $folder = Yii::getPathOfAlias('webroot.content.validation_attachments');
        $filename = md5($currentUser->userId.rand()).'.jpg';
        $original = $folder.DIRECTORY_SEPARATOR.$filename;

        $result = file_put_contents($original, $fileContent);
        if (!$result) {
            Web::jsonError(Yii::t('application', 'Произошел сбой при отправке Вашего фото. Пожалуйста, повторите попытку.'));
        }

        $info = getimagesize($original);
        if ($info['mime'] != 'image/jpeg') {
            unlink($original);
            Web::jsonError(Yii::t('application', 'Неправильный формат файла. Разрешены файлы "jpeg"'));
        }

        $size = filesize($original);
        if (!$size || $size > (5 * 1024 * 1024)) {
            unlink($original);
            Web::jsonError(Yii::t('application', 'Файл слишком большой. Разшеренный размер файла - 5MB'));
        }

        $relative = str_replace(Yii::getPathOfAlias('webroot'), '', $original);
        $relative = str_replace('\\', '/', $relative);

        $verificationRequest = new UserVerificationRequest('photo_verification_with_photo_string');
        $verificationRequest->photoAttachment = $relative;
        $verificationRequest->messenger = 'pv';
        $verificationRequest->messengerLogin = 'photoverification';
        $verificationRequest->callDate = date('d.n.Y');
        $verificationRequest->callTime = date('H:i');
        $verificationRequest->userId = $currentUser->userId;
        $verificationRequest->status = UserVerificationRequest::STATUS_OPENED;
        $verificationRequest->dateCreated = time();
        $verificationRequest->isPhotoVerification = 1;

        if ($verificationRequest->save()) {
            $currentUser->favoriteCigaretteBrand = $favoriteBrand;
            $currentUser->save(false);
            Yii::app()->user->setState('favoriteCigaretteBrand', $favoriteBrand, $favoriteBrand);
            Web::flashSuccess(Yii::t('application', 'При успешной верификации, полный доступ к приложению Вы получите в течение 24 часов -  Вам придёт подтверждение на e-mail!'));
            Web::jsonSuccess();
        } else {
            unlink($original);
            Web::jsonError(Yii::t('application', 'Произошел сбой при отправке Вашего фото. Пожалуйста, повторите попытку.'));
        }
    }

    public function actionVerificationSetFavoriteBrand()
    {
        $favoriteBrand = Web::getParam('favoriteCigaretteBrand');
        if (!in_array($favoriteBrand, Yii::app()->params['cigaretteBrands'])) {
            Web::jsonError(Yii::t('application', 'Неправильный бренд'));
        }

        Yii::app()->user->setState('favoriteCigaretteBrand', $favoriteBrand);
        Web::jsonSuccess();
    }

    public function actionSettings()
    {
        $currentUser = $this->getUser();
        $settings = UserNotificationsHelper::getSettings($currentUser->userId);

        if (Web::getParam('reset')) {
            $defaultSettings = UserNotificationsHelper::getDefaultSettins();
            UserNotificationsHelper::saveSettings($currentUser->userId, $defaultSettings);
            Web::flashSuccess(Yii::t('application', 'Настройки уведомлений сброшены'));
            $this->redirect(array('user/settings'));
        }

        if (isset($_POST['UserNotificationSetting'])) {
            if (UserNotificationsHelper::saveSettings($currentUser->userId, $_POST['UserNotificationSetting'])) {
                Web::flashSuccess(Yii::t('application', 'Настройки уведомлений сохранены'));
            } else {
                Web::flashError(Yii::t('application', 'Ошибка при сохранении настроек уведомлений. Обновите страницу и попробуйте еще раз'));
            }
            $this->refresh();
        }

        $this->render('settings', array('settings' => $settings));
    }

    public function actionFeedback()
    {
        $model = new FeedbackForm();

        if (isset($_POST['FeedbackForm'])) {
            $model->attributes = $_POST['FeedbackForm'];
            if ($model->validate()) {
                EmailHelper::send(Yii::app()->params['feedbackEmail'], EmailHelper::TYPE_FEEDBACK, array('title' => $model->title, 'description' => $model->description, 'user' => $this->getUser()));

                Web::flashSuccess(Yii::t('application', 'Ваше сообщение принято и отправлено модераторам'));
                $this->redirect(array('user/settings'));
            }
        }

        $this->render('feedback', array('model' => $model));
    }

    public function actionHelp()
    {
        $this->render('help');
    }

    public function actionSaveInvitation()
    {
        $currentUser = $this->getUser();
        $to = Web::getParam('to');

        $facebook = new Facebook(array(
            'appId' => Yii::app()->params['facebook']['appId'],
            'secret' => Yii::app()->params['facebook']['secret']
        ));

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

        $facebook->setAccessToken(Web::getParam('accessToken'));
        $currentFacebookId = $facebook->getUser();
        if ($currentFacebookId) {
            try {
                foreach ($to as $id) {
                    $params = array('facebookId' => $id);
                    if (!PointHelper::hasPoint(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params)) {
                        $inviteRequest = $facebook->api('/'.$id.'/apprequests', 'GET');
                        if (isset($inviteRequest['data']) && is_array($inviteRequest['data'])) {
                            foreach ($inviteRequest['data'] as $data) {
                                if ($data['application']['id'] == Yii::app()->params['facebook']['appId'] && $data['from']['id'] == $currentFacebookId) {
                                    if ($pointsCount < 300) {
                                        PointHelper::addPoints(Point::KEY_SOCIAL_INVITE, $currentUser->userId, $params);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            } catch (FacebookApiException $ex) {
                Web::jsonError(Yii::t('application', 'Пользователь не авторизован в социальной сети'));
            }
        }

        Web::jsonSuccess();
    }
}