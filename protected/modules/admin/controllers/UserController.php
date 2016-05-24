<?php

    class UserController extends AdminController
    {

        const NOTIFICATION_ALL_USER = 1;
        const NOTIFICATION_USER_WEEK = 2;
        const NOTIFICATION_USER_REGISTERED = 3;
        const NOTIFICATION_USER_GUEST = 4;

        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl', // perform access control for CRUD operations
                'postOnly + delete', // we only allow deletion via POST request
            );
        }

        public function accessRules()
        {
            return array(
                array('allow',
                    'actions' => array('index', 'view', 'delete', 'enable', 'disable', 'export', 'history', 'notifications'),
                    'roles' => array('admin', 'moderator', 'operator'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $model = new User('search');
            $model->unsetAttributes();  // clear any default values
            $account = new Account('search');
            $account->unsetAttributes();
            if(isset($_GET['User']))
            {
                $model->attributes = $_GET['User'];
            }
            $model->searchAccount = $account;

            $this->render('index', array(
                'model' => $model,
            ));
        }

        public function actionView($id)
        {
            $model = $this->loadModel($id);
            $verification = new UserVerificationForm();

            if(!$model->isVerified && isset($_POST['UserVerificationForm']))
            {
                $verification->attributes = $_POST['UserVerificationForm'];
                if($verification->validate())
                {
                    $userVerification = new UserVerification();
                    $userVerification->userId = $model->userId;
                    $userVerification->employeeId = $this->getEmployee()->employeeId;
                    $userVerification->comment = $verification->comment;
                    $userVerification->attachmentFilePath = $verification->saveAttachment();

                    if($userVerification->save())
                    {
                        $model->isVerified = 1;
                        $model->save(false);

                        PointHelper::addPoints(Point::KEY_VERIFICATION, $model->userId);
                        Web::flashSuccess(Yii::t('application', 'Пользователь верифицирован'));
                        $this->refresh();
                    }
                }
            }

            $this->render('view', array(
                'model' => $model,
                'verification' => $verification
            ));
        }

        public function actionEnable($id)
        {
            $model = $this->loadModel($id);

            $model->account->isActive = 1;
            $model->account->save();
            $this->redirect(array('view', 'id' => $model->userId));
        }

        public function actionDisable($id)
        {
            $model = $this->loadModel($id);

            $model->account->isActive = 0;
            $model->account->save();
            $this->redirect(array('view', 'id' => $model->userId));
        }

        public function actionDelete($id)
        {
            if(Yii::app()->request->isPostRequest)
            {
                $model = $this->loadModel($id);
                $model->delete();

                Yii::app()->authManager->revoke(Account::TYPE_USER, $model->account->accountId);

                Web::flashSuccess(Yii::t('application', 'Пользователь удален'));

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                {
                    $this->redirect(array('index'));
                }
            }
            else
            {
                throw new CHttpException(400, Yii::t('application', 'Невалидный запрос'));
            }
        }
        
        public function actionExport()
        {
            $model = new ExportUserForm();
            
            if(isset($_POST['ExportUserForm']))
            {
                $model->attributes = $_POST['ExportUserForm'];
                if($model->validate())
                {
                    $model->export();
                }
            }
            
            $this->render('export', array(
                'model' => $model
            ));
        }
        
        public function actionHistory($id)
        {
            $model = new User('history');
            $model->userId = $id;
            $user = $this->loadModel($id);
            $userPoints = $user->points;
            $pointsetter = new AddPointsForm();

            if(isset($_POST['AddPointsForm']))
            {
                $pointsetter->attributes = $_POST['AddPointsForm'];
                if($pointsetter->validate())
                {
                    PointHelper::addPoints(Point::KEY_FROM_ADMIN, $model->userId, array('reason' => ($pointsetter->comment), 'count' => $pointsetter->points));
                    Web::flashSuccess(Yii::t('application', 'Баллы добавлены'));
                    $this->refresh();
                }
            }

            $this->render('history', array(
                'model' => $model,
                'userId' => $id,
                'userPoints' => $userPoints,
                'pointsetter' => $pointsetter
            ));
        }

        public function actionNotifications()
        {
            if (isset($_POST['Notification'])) {
                $notification = $_POST['Notification'];
                $notification_text = $notification['text'];

                $gcm = Yii::app()->googlegcm;
                $params = array(
                  'type' => 'call_user_to_action',
                  'title' => $notification_text
                );

                $tokensCriteria = new CDbCriteria();
                $tokensCriteria->index = 'pushToken';

                if($notification['user_type'] == self::NOTIFICATION_USER_WEEK){
                    $tokensCriteria->addCondition("dateCreated < NOW() - INTERVAL 1 WEEK");
                }
                if($notification['user_type']==self::NOTIFICATION_USER_GUEST){
                    $tokensCriteria->with = array('user');
                    $tokensCriteria->compare('user.isVerified', 0);
                }
                if($notification['user_type']==self::NOTIFICATION_USER_REGISTERED){
                    $tokensCriteria->with = array('user');
                    $tokensCriteria->compare('user.isVerified', 1);
                }

                // FOR TESTING!
                //$tokensCriteria->addInCondition('userId', array(
                //    333549, // Ivanchenko
                //    335331 // Polina
                //));

                $tokens = UserPushToken::model()->findAll($tokensCriteria);
                

                if($tokens)
                {
                    //Yii::log(print_r($tokens, TRUE), CLogger::LEVEL_WARNING);
                    $tokens = array_keys($tokens);
                    $gcm->send($tokens, $params);
                }

                $this->redirect(array('notifications'));
            }
            $this->render('notifications');
        }

        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer $id the ID of the model to be loaded
         * @return User the loaded model
         * @throws CHttpException
         */
        public function loadModel($id)
        {
            $model = User::model()->with('account', 'verification')->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Пользователь не найден'));
            }
            return $model;
        }

    }
    