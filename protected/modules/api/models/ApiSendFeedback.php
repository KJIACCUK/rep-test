<?php

    class ApiSendFeedback extends CFormModel
    {

        public $title;
        public $description;

        public function rules()
        {
            return array(
                ApiValidatorHelper::required('title, description'),
                ApiValidatorHelper::length('title', null, 255),
                ApiValidatorHelper::length('description', null, 5000)
            );
        }

        public function attributeLabels()
        {
            return array(
                'title' => Yii::t('application', 'Тема'),
                'description' => Yii::t('application', 'Описание'),
            );
        }

    }
    