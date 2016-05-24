<?php

    class ApiUserLoginForm extends CFormModel
    {

        public $login;
        public $password;
        protected $_identity;

        public function rules()
        {
            return array(
                ApiValidatorHelper::required('login, password'),
                array('password', 'authenticate'),
            );
        }

        public function attributeLabels()
        {
            return array(
                'login' => Yii::t('application', 'Логин'),
                'password' => Yii::t('application', 'Пароль'),
            );
        }

        protected function getIdentity()
        {
            if($this->_identity === null)
            {
                $this->_identity = new UserIdentity($this->login, $this->password);
            }

            return $this->_identity;
        }

        /**
         * Authenticates the password.
         * This is the 'authenticate' validator as declared in rules().
         */
        public function authenticate($attribute, $params)
        {
            if(!$this->hasErrors())
            {
                if(!$this->getIdentity()->authenticate())
                {
                    switch($this->getIdentity()->errorCode)
                    {
                        case UserIdentity::ERROR_USERNAME_INVALID:
                            $this->addError('login', ValidationMessageHelper::USER_NOT_EXISTS);
                            break;

                        case UserIdentity::ERROR_PASSWORD_INVALID:
                            $this->addError('password', ValidationMessageHelper::INVALID_PASSWORD);
                            break;
                    }
                    return false;
                }
                return true;
            }
        }

        /**
         * Logs in the user using the given login and password in the model.
         * @return User
         */
        public function signIn()
        {
            $identity = $this->getIdentity();
            if($identity->errorCode === UserIdentity::ERROR_NONE)
            {
                return User::model()->findByAttributes(array('accountId' => $identity->getId()));
            }

            return false;
        }

    }
    