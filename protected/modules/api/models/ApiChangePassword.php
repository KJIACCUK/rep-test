<?php

    class ApiChangePassword extends CFormModel
    {

        public $oldPassword;
        public $newPassword;
        private $_oldPasswordCrypted;

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                ApiValidatorHelper::required('oldPassword, newPassword'),
                ApiValidatorHelper::length('newPassword', 6, 255),
                array('oldPassword', 'checkPassword')
            );
        }

        public function checkPassword()
        {
            if($this->_oldPasswordCrypted != CommonHelper::md5($this->oldPassword))
            {
                $this->addError('oldPassword', ValidationMessageHelper::INVALID_OLD_PASSWORD);
            }
        }
        
        public function setOldPasswordCrypted($oldPasswordCrypted)
        {
            $this->_oldPasswordCrypted = $oldPasswordCrypted;
        }

    }
    