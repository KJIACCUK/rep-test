<?php

    class UserVerificationController extends AdminController
    {

        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl'
            );
        }

        public function accessRules()
        {
            return array(
                array('allow',
                    'actions' => array('index', 'view', 'history', 'historyView'),
                    'roles' => array('admin', 'moderator', 'operator'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $model = new UserVerificationRequest('search');
            $model->unsetAttributes();  // clear any default values
            $user = new User('search');
            $user->unsetAttributes();
            if(isset($_GET['UserVerificationRequest']))
            {
                $model->attributes = $_GET['UserVerificationRequest'];
            }
            $model->searchUser = $user;

            $this->render('index', array(
                'model' => $model,
            ));
        }

        public function actionView($id)
        {
            $model = $this->loadRequest($id);

            if(isset($_POST['UserVerificationRequest']))
            {
                $model->attributes = $_POST['UserVerificationRequest'];
                $model->employeeId = $this->getEmployee()->employeeId;
                $model->status = UserVerificationRequest::STATUS_CLOSED;
                $model->dateClosed = time();

                $transaction = Yii::app()->db->beginTransaction();

                if($model->save())
                {
                    if($model->isVerified)
                    {
                        $verification = new UserVerification();
                        $verification->userId = $model->user->userId;
                        $verification->employeeId = $this->getEmployee()->employeeId;
                        $verification->comment = $model->comment;
                        $verification->attachmentFilePath = $model->attachment;

                        if($verification->save())
                        {
                            $model->user->isVerified = 1;
                            $model->user->save(false);

                            PointHelper::addPoints(Point::KEY_VERIFICATION, $model->userId);
                            Web::flashSuccess(Yii::t('application', 'Пользователь верифицирован'));
                            EmailHelper::send($model->user->email, EmailHelper::TYPE_VERIFICATION_APPROVED, array('user' => $model->user));
                            $transaction->commit();
                            $this->redirect(array('index'));
                        }
                    }
                    else
                    {
                        Web::flashSuccess(Yii::t('application', 'Заявка о верификации отклонена'));
                        EmailHelper::send($model->user->email, EmailHelper::TYPE_VERIFICATION_DECLINED, array('user' => $model->user));
                        $transaction->commit();
                        $this->redirect(array('index'));
                    }
                }
                $transaction->rollback();
            }

            $this->render('view', array(
                'model' => $model
            ));
        }
        
        public function actionHistory()
        {
            $model = new UserVerificationRequest('search_history');
            $model->unsetAttributes();  // clear any default values
            $user = new User('search');
            $user->unsetAttributes();
            if(isset($_GET['UserVerificationRequest']))
            {
                $model->attributes = $_GET['UserVerificationRequest'];
            }
            $model->searchUser = $user;

            $this->render('history', array(
                'model' => $model,
            ));
        }
        
        public function actionHistoryView($id)
        {
            $model = $this->loadRequest($id);
            $this->render('history_view', array(
                'model' => $model
            ));
        }

        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer $id the ID of the model to be loaded
         * @return UserVerificationRequest the loaded model
         * @throws CHttpException
         */
        public function loadRequest($id)
        {
            $model = UserVerificationRequest::model()->with('user')->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, 'The requested page does not exist.');
            }
            return $model;
        }

    }
    