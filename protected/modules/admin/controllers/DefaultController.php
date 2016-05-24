<?php

    class DefaultController extends AdminController
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
                    'actions' => array('index', 'logout'),
                    'roles' => array('admin', 'moderator', 'operator'),
                ),
                array('allow',
                    'actions' => array('login'),
                    'users' => array('*'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $this->render('index');
        }

        public function actionLogin()
        {
            $model = new AdminLoginForm();

            if(isset($_POST['AdminLoginForm']))
            {
                $model->attributes = $_POST['AdminLoginForm'];
                if($model->validate() && $model->signIn())
                {
                    $returnURL = Yii::app()->user->returnURL;  
                    //$this->redirect($returnURL?$returnURL:array('admin/default/index'));
                    $this->redirect(array('index'));
                }
            }

            $this->render('login', array('model' => $model));
        }

        public function actionLogout()
        {
            Yii::app()->user->logout(false);
            $this->redirect(Yii::app()->getModule('admin')->user->loginUrl);
        }

    }
    