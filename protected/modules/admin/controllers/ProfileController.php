<?php

    class ProfileController extends AdminController
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

        /**
         * Specifies the access control rules.
         * This method is used by the 'accessControl' filter.
         * @return array access control rules
         */
        public function accessRules()
        {
            return array(
                array('allow',
                    'actions' => array('index'),
                    'roles' => array('admin', 'moderator', 'operator'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $model = $this->getEmployee();

            if(isset($_POST['Employee']))
            {
                if($_POST['Employee']['isChangePassword'])
                {
                    $model->setScenario('update_with_password');
                }
                $model->attributes = $_POST['Employee'];
                if($model->save())
                {
                    Yii::app()->user->setName($model->name);
                    Web::flashSuccess(Yii::t('application', 'Профиль сохранен'));
                    $this->refresh();
                }
            }

            $this->render('index', array(
                'model' => $model
            ));
        }

    }
    