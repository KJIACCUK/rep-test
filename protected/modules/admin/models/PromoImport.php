<?php

    class PromoImport extends CFormModel
    {

        public $importFile;

        public function rules()
        {
            return array(
                array('importFile', 'file', 'types' => 'csv', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false)
            );
        }

        public function attributeLabels()
        {
            return array(
                'importFile' => Yii::t('application', 'CSV файл'),
            );
        }
    }
    