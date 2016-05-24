<?php

    class UserVerificationForm extends CFormModel
    {

        public $comment;
        public $attachmentFile;

        public function rules()
        {
            return array(
                array('attachmentFile', 'file', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => true),
                array('comment', 'safe'),
            );
        }

        public function attributeLabels()
        {
            return array(
                'comment' => Yii::t('application', 'Комментарий'),
                'attachmentFile' => Yii::t('application', 'Приложение'),
            );
        }

        public function saveAttachment()
        {
            $attachment = null;
            $this->attachmentFile = CUploadedFile::getInstance($this, 'attachmentFile');
            if($this->attachmentFile)
            {
                $attachment = Yii::getPathOfAlias('webroot.content.validation_attachments').DIRECTORY_SEPARATOR.md5(time().$this->attachmentFile->getName()).'.'.$this->attachmentFile->getExtensionName();
                $attachment = str_replace(Yii::getPathOfAlias('webroot'), '', $attachment);
                $attachment = str_replace('\\', '/', $attachment);
                $this->attachmentFile->saveAs(Yii::getPathOfAlias('webroot').$attachment);
            }

            return $attachment;
        }

    }
    