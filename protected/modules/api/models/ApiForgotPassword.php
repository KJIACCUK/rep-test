<?php

    class ApiForgotPassword extends CFormModel
    {

        public $email;

        public function rules()
        {
            return array(
                ApiValidatorHelper::required('email'),
                ApiValidatorHelper::email('email'),
                ApiValidatorHelper::length('email', null, 255),
                ApiValidatorHelper::exists('email')+array('attributeName' => 'email', 'className' => 'User')
            );
        }

    }
    