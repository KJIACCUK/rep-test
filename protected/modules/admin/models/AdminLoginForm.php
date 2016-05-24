<?php

    class AdminLoginForm extends CFormModel
    {

        public $login;
        public $password;
        public $rememberMe;
        protected $_identity;

        public function rules()
        {
            return array(
                array('login, password', 'required'),
                array('rememberMe', 'boolean'),
                array('password', 'authenticate'),
            );
        }

        public function attributeLabels()
        {
            return array(
                'login' => Yii::t('application', 'Логин'),
                'password' => Yii::t('application', 'Пароль'),
                'rememberMe' => Yii::t('application', 'Запомнить меня'),
            );
        }

        protected function getIdentity()
        {
            if($this->_identity === null)
            {
                $this->_identity = new EmployeeUserIdentity($this->login, $this->password);
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
                        case EmployeeUserIdentity::ERROR_USERNAME_INVALID:
                            $this->addError('login', Yii::t('application', 'Пользователя с таким логином не существует'));
                            break;

                        case EmployeeUserIdentity::ERROR_PASSWORD_INVALID:
                            $this->addError('password', Yii::t('application', 'Неправильный пароль.'));
                            break;
                    }
                    return false;
                }
                return true;
            }
        }

        /**
         * Logs in the user using the given login and password in the model.
         * @return boolean whether login is successful
         */
        public function signIn()
        {
            if($this->getIdentity()->errorCode === EmployeeUserIdentity::ERROR_NONE)
            {
                $duration = $this->rememberMe?Yii::app()->params['loginRememberTime']:0;
                Yii::app()->user->login($this->getIdentity(), $duration);
                return true;
            }

            return false;
        }

    }
    