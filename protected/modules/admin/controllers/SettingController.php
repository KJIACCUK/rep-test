<?php

    class SettingController extends AdminController
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
                    'roles' => array('admin'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $model = new SettingForm();
            $model->loadSettings();
            
            if(isset($_POST['SettingForm']))
            {
                $model->attributes = $_POST['SettingForm'];
                if($model->validate())
                {
                    $model->saveSettings();
                    Web::flashSuccess(Yii::t('application', 'Настройки сохранены'));
                    $this->refresh();
                }
            }
            
            $this->render('index', array(
                'model' => $model
            ));
        }

    }
    