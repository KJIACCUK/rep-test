<?php

class PromoController extends WebController
{
    /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl',
                array('ProfileIsFilledFilter'),
                array('ProfileIsVerifiedFilter')
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
            $model = new PromoForm();
            /* @var $model PromoForm */
            
            if (isset($_POST['PromoForm'])) {
                $model->attributes = $_POST['PromoForm'];
                
                if ($model->validate() && $model->activateCode($currentUser)) {
                    Web::flashSuccess(Yii::t('application', 'Код активирован. Вам добавлено ').Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($model->getPromoCode()->pointsActivated)));
                    $this->refresh();
                }
            }
            
            $this->render('index', array('model' => $model));
        }
}