<?php

    require Yii::getPathOfAlias('application.vendor').'/autoload.php';

    class DefaultController extends ApiController
    {

        public function filters()
        {
            return array(
                array('ApiAccessControlFilter + logout'),
                array(
                    'ApiParamFilter + login, registration, loginViaSocial',
                    'param' => 'image',
                    'function' => 'FilterHelper::checkImage'
                ),
                array(
                    'ApiParamFilter + login, registration, loginViaSocial',
                    'param' => 'platform',
                    'function' => 'FilterHelper::checkPlatform'
                ),
                array(
                    'ApiParamFilter + registrationValidate',
                    'param' => 'step',
                    'function' => 'FilterHelper::checkRegistrationStep'
                ),
                array(
                    'ApiParamFilter + loginViaSocial',
                    'param' => 'accessToken',
                    'function' => 'FilterHelper::checkNotEmpty',
                ),
                array(
                    'ApiParamFilter + loginViaSocial',
                    'param' => 'social',
                    'function' => 'FilterHelper::checkSocial'
                )
            );
        }

        /**
         * Get Budutam API Info
         *
         * return Budutam API Info
         */
        public function actionIndex()
        {
            $response = array('name' => 'Budutam API', 'version' => '1.2');
            Api::respondSuccess($response);
        }

        public function actionError()
        {
            if(($error = Yii::app()->errorHandler->error))
            {
                if($error['type'] == 'CHttpException')
                {
                    if($error['code'] == 404)
                    {
                        Api::respondError(Api::CODE_METHOD_NOT_ALLOWED, Yii::t('application', 'Method not allowed or not exists.'));
                    }
                }
                elseif($error['type'] == 'ApiException')
                {
                    switch($error['code'])
                    {
                        case Api::CODE_BAD_REQUEST:
                            Api::respondError(Api::CODE_BAD_REQUEST, $error['message']?$error['message']:Yii::t('application', 'Bad request.'));
                            break;

                        case Api::CODE_UNAUTHORIZED:
                            Api::respondError(Api::CODE_UNAUTHORIZED, $error['message']?$error['message']:Yii::t('application', 'User not authorized.'));
                            break;

                        case Api::CODE_FORBIDDEN:
                            Api::respondError(Api::CODE_FORBIDDEN, $error['message']?$error['message']:Yii::t('application', 'Access forbidden.'));
                            break;

                        case Api::CODE_NOT_FOUND:
                            Api::respondError(Api::CODE_NOT_FOUND, $error['message']?$error['message']:Yii::t('application', 'Object not found.'));
                            break;

                        case Api::CODE_METHOD_NOT_ALLOWED:
                            Api::respondError(Api::CODE_METHOD_NOT_ALLOWED, $error['message']?$error['message']:Yii::t('application', 'Method not allowed or not exists.'));
                            break;

                        case Api::CODE_INTERNAL_SERVER_ERROR:
                            Api::respondError(Api::CODE_INTERNAL_SERVER_ERROR, $error['message']?$error['message']:Yii::t('application', 'Internal server error.'));
                            break;

                        case Api::CODE_NOT_IMPLEMENTED:
                            Api::respondError(Api::CODE_NOT_IMPLEMENTED, $error['message']?$error['message']:Yii::t('application', 'Method not implemented.'));
                            break;

                        case Api::CODE_VALIDATION_ERROR:
                            Api::respondError(Api::CODE_VALIDATION_ERROR, $error['message']?$error['message']:Yii::t('application', 'Validation error.'));
                            break;

                        default:
                            Api::respondError($error['code'], $error['message']);
                            break;
                    }
                }

                Api::respondError($error['code'], $error['message']);
            }
            Api::respondError(Api::CODE_INTERNAL_SERVER_ERROR, Yii::t('application', 'Unknown error.'));
        }

        /**
         * GET App Settings
         *
         * Returns phone codes, messengers, music genres, event categories, cigarette brands, Skype login and Hangouts login
         *
         * return JSON object
         */
        public function actionAppSettings()
        {
            $response = array();
            $response['settings'] = array(
                'phoneCodes' => Yii::app()->params['phoneCodes'],
                'messengers' => Yii::app()->params['messengers'],
                'musicGenres' => Yii::app()->params['musicGenres'],
                'eventCategories' => Yii::app()->params['eventCategories'],
                'cigaretteBrands' => Yii::app()->params['cigaretteBrands'],
                'skypeLogin' => Yii::app()->params['skypeLogin'],
                'hangoutsLogin' => Yii::app()->params['hangoutsLogin']
            );

            Api::respondSuccess($response);
        }

        /**
         * GET Cities
         *
         * Returns cities array
         *
         * return JSON object
         */
        public function actionGetCities()
        {
            $response = array();
            $response['cities'] = CityHelper::getCities();
            Api::respondSuccess($response);
        }

        /**
         * POST Login
         *
         * @param login String
         * @param password String
         * @param image String "100x100"
         *
         * return JSON User data, user image, api token, push token
         *
         * @throws \ApiException
         */
        public function actionLogin()
        {
            $platform = Api::getParam('platform');
            $model = new ApiUserLoginForm();
            $model->attributes = Api::getParams(array('login', 'password'));

            if(!$model->validate())
            {
                Api::respondValidationError($model);
            }

            if(!($user = $model->signIn()))
            {
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

//            if (filter_var($user->login, FILTER_VALIDATE_EMAIL) === false && !UserHelper::isLogged($user->userId)) { // Send SMS Login
//                $response = UserHelper::sendSMSLogin($user->login);
//                Yii::log("SMS for User Login ".$user->login." #".$user->userId, CLogger::LEVEL_WARNING, "API");
//                Yii::log("SMS system answer: " . print_r($response, TRUE), CLogger::LEVEL_WARNING);
//            } else {
//                Yii::log("User #" . $user->userId . " was logged earlier", CLogger::LEVEL_WARNING, 'API');
//            }
            
            if(!($apiToken = UserHelper::createAuthToken($user->userId, $platform)))
            {
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $response = array();
            $response['user'] = UserHelper::export($user, 'detail, mine');
            $response['user']['image'] = CommonHelper::getImageLink($user->image, Api::getParam('image'));
            $response['user']['token'] = $apiToken->token;
            $response['user']['pushToken'] = (string)UserHelper::getPushToken($user, $apiToken->token, $platform);
            Api::respondSuccess($response);
        }

        /**
         * POST Registration
         *
         * @param step
         * @param firstname
         * @param lastname
         * @param email
         * @param birthday
         * @param phone
         * @param phoneCode
         * @param password
         * @param verificationPhotoFile
         *
         * return Success if all OK
         *
         */
        public function actionRegistrationValidate()
        {
            $step = Api::getParam('step');
            $model = new ApiRegistration('step'.$step);
            $model->attributes = Api::getParams(array('firstname', 'lastname', 'email', 'birthday', 'phone', 'phoneCode', 'password'));
            $model->verificationPhotoFile = CUploadedFile::getInstanceByName('verificationPhotoFile');
            if (!$model->validate()) {
                Api::respondValidationError($model);
            }
            Api::respondSuccess();
        }

        /**
         * POST Registration
         *
         * @param step
         * @param firstname
         * @param lastname
         * @param email
         * @param birthday
         * @param phone
         * @param phoneCode
         * @param password
         * @param verificationPhotoFile
         *
         * return JSON User data, user image, api token
         *
         * @throws \ApiException
         */
        public function actionRegistration()
        {
            $platform = Api::getParam('platform');
            $transaction = Yii::app()->db->beginTransaction();

            if(!($account = UserHelper::createAccount()))
            {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $user = new User('api_registration');
            $user->attributes = Api::getParams(array('firstname', 'lastname', 'email', 'birthday', 'phone', 'phoneCode', 'password'));
            $user->accountId = $account->accountId;

            $password = $user->password;

            if(!$user->save())
            {
                $transaction->rollback();
                Api::respondValidationError($user);
            }

            if(!UserNotificationsHelper::createSettings($user->userId))
            {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            if(!($apiToken = UserHelper::createAuthToken($user->userId, $platform)))
            {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $transaction->commit();

            EmailHelper::send($user->email, EmailHelper::TYPE_REGISTRATION, array('user' => $user, 'password' => $password));

            $response = array();
            $response['user'] = UserHelper::export($user, 'detail, mine');
            $response['user']['image'] = CommonHelper::getImageLink($user->image, Api::getParam('image'));
            $response['user']['token'] = $apiToken->token;

            Api::respondSuccess($response);
        }

        /**
         * POST Login vial Social
         *
         * @param platform: android,ios
         * @param social: facebook,vkontakte,twitter
         * @param accessToken
         * @param accessTokenSecret: Twitter only
         *
         * return JSON User data, user image, api token, push token
         *
         * @throws \ApiException
         */
        public function actionLoginViaSocial()
        {
            $platform = Api::getParam('platform');
            $social = Api::getParam('social');

            $socialId = null;
            $socialData = array(
                'name' => '',
                'email' => '',
                'birthday' => '',
                'image' => '',
            );

            if($social == UserSocial::TYPE_FACEBOOK)
            {
                $facebook = new Facebook(array(
                    'appId' => Yii::app()->params['facebook']['appId'],
                    'secret' => Yii::app()->params['facebook']['secret']
                ));

                $facebook->setAccessToken(Api::getParam('accessToken'));

                $socialId = $facebook->getUser();

                if($socialId)
                {
                    try
                    {
                        $facebookUserProfile = $facebook->api('/me', 'GET', array(
                            'fields' => 'id,name,picture.height(540).width(540),birthday,email'
                        ));

                        $socialData['name'] = $facebookUserProfile['name'];
                        if(isset($facebookUserProfile['email']))
                        {
                            $socialData['email'] = $facebookUserProfile['email'];
                        }
                        if(isset($facebookUserProfile['birthday']))
                        {
                            $socialData['birthday'] = $facebookUserProfile['birthday'];
                        }
                        if(isset($facebookUserProfile['picture']) && isset($facebookUserProfile['picture']['data']) && isset($facebookUserProfile['picture']['data']['url']))
                        {
                            $socialData['image'] = $facebookUserProfile['picture']['data']['url'];
                        }
                    }
                    catch(FacebookApiException $ex)
                    {
                        Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                    }
                }
                else
                {
                    Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                }
            }
            elseif($social == UserSocial::TYPE_VKONTAKTE)
            {
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
                    '&uids' => Api::getParam('uid'),
                    '&fields' => array('bdate', 'photo_big')
                ));
                
                if($vkUserProfile)
                {
                    if(isset($vkUserProfile['response']) && isset($vkUserProfile['response'][0]))
                    {
                        $vkUserProfile = $vkUserProfile['response'][0];
                        $socialId = $vkUserProfile['uid'];
                        $socialData['name'] = $vkUserProfile['first_name'].' '.$vkUserProfile['last_name'];
                        $socialData['image'] = $vkUserProfile['photo_big'];
                        $socialData['birthday'] = $vkUserProfile['bdate'];
                    }
                    else
                    {
                        Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                    }
                }
                else
                {
                    Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                }
            }
            elseif($social == UserSocial::TYPE_TWITTER)
            {
                $settings = array(
                    'oauth_access_token' => Api::getParam('accessToken'),
                    'oauth_access_token_secret' => Api::getParam('accessTokenSecret'),
                    'consumer_key' => Yii::app()->params['twitter']['key'],
                    'consumer_secret' => Yii::app()->params['twitter']['secret']
                );

                $url = 'https://api.twitter.com/1.1/account/verify_credentials.json';
                $requestMethod = 'GET';

                try
                {
                    $twitter = new TwitterAPIExchange($settings);
                    $twitterUserProfile = $twitter->buildOauth($url, $requestMethod)->performRequest();

                    if($twitterUserProfile && ($twitterUserProfile = CJSON::decode($twitterUserProfile)))
                    {
                        $socialId = $twitterUserProfile['id'];
                        $socialData['name'] = $twitterUserProfile['name'];
                    }
                }
                catch(Exception $ex)
                {
                    Api::respondError(Api::CODE_VALIDATION_ERROR, Yii::t('application', 'Пользователь не авторизован в социальной сети'), array('accessToken' => array(ValidationMessageHelper::INVALID_SOCIAL_TOKEN)));
                }
            }

            $transaction = Yii::app()->db->beginTransaction();
            $response = array();
            $isNewAccount = false;
            $userSocial = UserSocial::model()->with('user')->findByAttributes(array('type' => $social, 'socialId' => $socialId));
            if(!$userSocial)
            {
                $isNewAccount = true;
                if(!($account = UserHelper::createAccount()))
                {
                    $transaction->rollback();
                    throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
                }

                $user = new User('api_social_registration');
                $user->attributes = $socialData;
                $user->accountId = $account->accountId;

                if(!$user->save())
                {
                    $transaction->rollback();
                    throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
                }

                $userSocial = new UserSocial();
                $userSocial->userId = $user->userId;
                $userSocial->type = $social;
                $userSocial->socialId = $socialId;
                $userSocial->dateCreated = time();

                if(!$userSocial->save())
                {
                    $transaction->rollback();
                    throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
                }

                if(!UserNotificationsHelper::createSettings($user->userId))
                {
                    $transaction->rollback();
                    throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
                }

                // Send SMS Login
                //$this->sendSMSLogin($user->login);
            }
            else
            {
                $user = $userSocial->user;
            }

            if(!($apiToken = UserHelper::createAuthToken($user->userId, $platform)))
            {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $transaction->commit();
            
            if($user->email && $isNewAccount)
            {
                EmailHelper::send($user->email, EmailHelper::TYPE_SOCIAL_REGISTRATION, array('user' => $user));
            }
            
            $response['user'] = UserHelper::export($user, 'detail, mine');
            $response['user']['image'] = CommonHelper::getImageLink($user->image, Api::getParam('image'));
            $response['user']['token'] = $apiToken->token;
            $response['user']['pushToken'] = (string)UserHelper::getPushToken($user, $apiToken->token, $platform);
            Api::respondSuccess($response);
        }

        /**
         * POST Reset Password
         *
         * @param email
         *
         * return Success if user exists
         *
         * @throws \ApiException
         */
        public function actionForgotPassword()
        {
            $model = new ApiForgotPassword();
            $model->email = Api::getParam('email');

            if(!$model->validate())
            {
                Api::respondValidationError($model);
            }

            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('email' => $model->email));
            $criteria->addCondition('password IS NOT NULL');

            $user = User::model()->find($criteria);
            /* @var $user User */

            if(!$user)
            {
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $password = CommonHelper::randomString();

            $user->password = CommonHelper::md5($password);
            $user->save(false);

            EmailHelper::send($model->email, EmailHelper::TYPE_RESET_PASSWORD, array('user' => $user, 'password' => $password));

            Api::respondSuccess();
        }

        /**
         * POST Logout
         *
         * @param pushToken
         *
         * return Success if pushToken exists
         */
        public function actionLogout()
        {
            $token = $this->getToken();
            $pushToken = Api::getParam('pushToken');
            if($pushToken)
            {
                UserPushToken::model()->deleteAllByAttributes(array('pushToken' => $pushToken, 'apiToken' => $token));
            }

            UserApiToken::model()->deleteAllByAttributes(array('token' => $token));
            Api::respondSuccess();
        }

    }
    