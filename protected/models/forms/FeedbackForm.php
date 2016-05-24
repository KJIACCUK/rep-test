<?php

    class FeedbackForm extends CFormModel
    {

        public $title;
        public $description;

        public function rules()
        {
            return array(
                array('title, description', 'required'),
                array('title', 'length', 'max' => 255),
                array('description', 'length', 'max' => 5000),
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
    