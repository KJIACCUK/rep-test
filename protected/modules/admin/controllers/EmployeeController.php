<?php

    class EmployeeController extends AdminController
    {

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

        /**
         * Specifies the access control rules.
         * This method is used by the 'accessControl' filter.
         * @return array access control rules
         */
        public function accessRules()
        {
            return array(
                array('allow',
                    'actions' => array('index', 'create', 'update', 'delete'),
                    'roles' => array('admin'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $model = new Employee('search');
            $model->unsetAttributes();
            $account = new Account('search');
            $account->unsetAttributes();
            if(isset($_GET['Employee']))
            {
                $model->attributes = $_GET['Employee'];
            }
            $model->searchAccount = $account;

            $this->render('index', array(
                'model' => $model,
                'type' => Web::getParam('type', 'administrators')
            ));
        }

        /**
         * Creates a new model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         */
        public function actionCreate()
        {
            $model = new Employee();

            if(isset($_POST['Employee']))
            {
                $model->attributes = $_POST['Employee'];
                $transaction = Yii::app()->db->beginTransaction();
                if(!($account = UserHelper::createAccount($model->type)))
                {
                    $transaction->rollback();
                    throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                }
                $model->accountId = $account->accountId;
                if($model->save())
                {
                    $transaction->commit();
                    switch($account->type)
                    {
                        case Account::TYPE_ADMIN:
                            Web::flashSuccess(Yii::t('application', 'Администратор добавлен'));
                            $this->redirect(array('index', 'type' => 'administrators'));
                            break;

                        case Account::TYPE_MODERATOR:
                            Web::flashSuccess(Yii::t('application', 'Модератор добавлен'));
                            $this->redirect(array('index', 'type' => 'moderators'));
                            break;

                        case Account::TYPE_OPERATOR:
                            Web::flashSuccess(Yii::t('application', 'Оператор добавлен'));
                            $this->redirect(array('index', 'type' => 'operators'));
                            break;
                    }
                }
                else
                {
                    $transaction->rollback();
                }
            }

            $this->render('create', array(
                'model' => $model
            ));
        }

        /**
         * Updates a particular model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id the ID of the model to be updated
         */
        public function actionUpdate($id)
        {
            $model = $this->loadModel($id);

            if(isset($_POST['Employee']))
            {
                if($_POST['Employee']['isChangePassword'])
                {
                    $model->setScenario('update_with_password');
                }
                $model->attributes = $_POST['Employee'];
                if($model->save())
                {
                    $model->account->isActive = $model->isActive?1:0;
                    $model->account->save();
                    switch($model->account->type)
                    {
                        case Account::TYPE_ADMIN:
                            Web::flashSuccess(Yii::t('application', 'Администратор обновлен'));
                            $this->redirect(array('index', 'type' => 'administrators'));
                            break;

                        case Account::TYPE_MODERATOR:
                            Web::flashSuccess(Yii::t('application', 'Модератор обновлен'));
                            $this->redirect(array('index', 'type' => 'moderators'));
                            break;

                        case Account::TYPE_OPERATOR:
                            Web::flashSuccess(Yii::t('application', 'Оператор обновлен'));
                            $this->redirect(array('index', 'type' => 'operators'));
                            break;
                    }
                }
            }

            $this->render('update', array(
                'model' => $model,
            ));
        }

        /**
         * Deletes a particular model.
         * If deletion is successful, the browser will be redirected to the 'admin' page.
         * @param integer $id the ID of the model to be deleted
         */
        public function actionDelete($id)
        {
            if(Yii::app()->request->isPostRequest)
            {
                // we only allow deletion via POST request
                $model = $this->loadModel($id);
                $model->delete();
                
                Yii::app()->authManager->revoke($model->account->type, $model->account->accountId);

                switch($model->account->type)
                {
                    case Account::TYPE_ADMIN:
                        Web::flashSuccess(Yii::t('application', 'Администратор удален'));
                        break;

                    case Account::TYPE_MODERATOR:
                        Web::flashSuccess(Yii::t('application', 'Модератор удален'));
                        break;

                    case Account::TYPE_OPERATOR:
                        Web::flashSuccess(Yii::t('application', 'Оператор удален'));
                        break;
                }

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

        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer $id the ID of the model to be loaded
         * @return Employee the loaded model
         * @throws CHttpException
         */
        public function loadModel($id)
        {
            $model = Employee::model()->with('account')->findByPk($id);

            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Сотрудник не найден'));
            }
            return $model;
        }

    }
    