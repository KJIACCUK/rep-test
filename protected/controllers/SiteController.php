<?php

    class SiteController extends WebController
    {
        public function actionIndex()
        {
            if(Yii::app()->user->getIsGuest() || !$this->getUser())
            {
                $this->redirect(array('site/login'));
            }
            else
            {
                if($this->getUser()->isFilled)
                {
                    $this->redirect(array('event/index'));
                }
                else
                {
                    $this->redirect(array('user/profileComplete'));
                }
            }
        }

        public function actionError()
        {
            if(($error = Yii::app()->errorHandler->error))
            {
                if(Yii::app()->request->isAjaxRequest)
                {
                    print $error['message'];
                }
                else
                {
                    $this->render('error', $error);
                }
            }
        }

        public function actionLogin()
        {
            $model = new UserLoginForm();

            if(isset($_POST['UserLoginForm']))
            {
                $model->attributes = $_POST['UserLoginForm'];
                if($model->validate() && $model->signIn())
                {
                    $this->redirect(array('event/index'));
                }
            }
            
            $model->password = null;

            $this->render('login', array('model' => $model));
        }

        public function actionRegistration()
        {
            $user = new User('registration');
            $user->birthdayYear = '1985';

            if(isset($_POST['User']))
            {
                $transaction = Yii::app()->db->beginTransaction();

                if(!($account = UserHelper::createAccount()))
                {
                    $transaction->rollback();
                    throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                }

                $user->attributes = $_POST['User'];
                $user->accountId = $account->accountId;

                $password = $user->password;
                
                if($user->save())
                {
                    if(!UserNotificationsHelper::createSettings($user->userId))
                    {
                        $transaction->rollback();
                        throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                    }

                    EmailHelper::send($user->email, EmailHelper::TYPE_REGISTRATION, array('user' => $user, 'password' => $password));

                    // Send SMS Login
                    //$this->sendSMSLogin($user->login);

                    $transaction->commit();

                    $loginform = new UserLoginForm();
                    $loginform->login = $user->login;
                    $loginform->password = $password;
                    if($loginform->validate() && $loginform->signIn())
                    {
                        Yii::app()->user->returnUrl = null;
                        $this->redirect(array('user/askVerification'));
                    }
                    else
                    {
                        $this->redirect(array('site/login'));
                    }
                }
                else
                {
                    $transaction->rollback();
                }
            }

            $user->password = null;
            $user->passwordConfirm = null;
            
            Yii::app()->user->returnUrl = $this->createAbsoluteUrl('site/registration');

            $this->render('registration', array('model' => $user));
        }

        function sendSMSLogin($login)
        {
            // Send login to SMS service
            $url = "http://anketa.clab.by/firstapplogin.php";

            $post_data['login'] = CommonHelper::encodeLogin($login);

            // Initialize cURL
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            // Pass TRUE or 1 if you want to wait for and catch the response against the request made
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // For Debug mode; shows up any error encountered during the operation
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // Execute the request
            $response = curl_exec($ch);
        }

        public function actionForgot_password()
        {
            $model = new ForgotPasswordForm();
            
            if(isset($_POST['ForgotPasswordForm']))
            {
                $model->attributes = $_POST['ForgotPasswordForm'];
                if($model->validate())
                {
                    $user = $model->getUser();
                    $password = CommonHelper::randomString();
                    $user->password = CommonHelper::md5($password);
                    $user->save(false);
                    
                    EmailHelper::send($model->email, EmailHelper::TYPE_RESET_PASSWORD, array('user' => $user, 'password' => $password));
                    
                    Web::flashSuccess(Yii::t('application', 'Новый пароль отправлен на указанный E-mail'));
                    $this->redirect(array('site/login'));
                }
            }
            
            $this->render('forgot_password', array('model' => $model));
        }
        
        public function actionTerms()
        {
            $returnUrl = Yii::app()->user->returnUrl;
            $this->render('terms', array('returnUrl' => $returnUrl));
        }

        public function actionLogout()
        {
            Yii::app()->user->logout();
            $this->redirect(array('site/login'));
        }

        public function actionSendLogin()
        {
            // URL on which we have to post data
            $url = "http://anketa.clab.by/firstapplogin.php";


            $post_data['login'] = CommonHelper::encodeLogin("Z15682");

            // Initialize cURL
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            // Pass TRUE or 1 if you want to wait for and catch the response against the request made
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // For Debug mode; shows up any error encountered during the operation
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // Execute the request
            $response = curl_exec($ch);

            // Just for debug: to see response
            echo $response;
        }

    }
    