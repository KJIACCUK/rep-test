<?php

    class ForgotPasswordForm extends CFormModel
    {

        public $email;
        /**
         * @var User 
         */
        private $_user;

        public function rules()
        {
            return array(
                array('email', 'required'),
                array('email', 'email'),
                array('email', 'validateExists'),
            );
        }
        
        public function validateExists()
        {
            $this->_user = User::model()->with('account')->findByAttributes(array('email' => $this->email));
            if(!$this->_user || $this->_user->account->type != Account::TYPE_USER || !$this->_user->account->isActive)
            {
                $this->addError('email', Yii::t('application', 'Пользователь с таким E-mail не найден'));
            }
        }

        public function attributeLabels()
        {
            return array(
                'email' => Yii::t('application', 'E-mail'),
            );
        }
        
        /**
         * 
         * @return User
         */
        public function getUser()
        {
            return $this->_user;
        }

    }
    