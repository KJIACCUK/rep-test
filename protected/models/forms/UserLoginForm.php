<?php

    class UserLoginForm extends CFormModel
    {

        public $login;
        public $password;
        protected $_identity;

        public function rules()
        {
            return array(
                array('login, password', 'required'),
                array('password', 'authenticate'),
            );
        }

        public function attributeLabels()
        {
            return array(
                'login' => Yii::t('application', 'Логин или E-mail'),
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
                            $this->addError('login', Yii::t('application', 'Пользователя с таким логином не существует'));
                            break;

                        case UserIdentity::ERROR_PASSWORD_INVALID:
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
            if($this->getIdentity()->errorCode === UserIdentity::ERROR_NONE)
            {
                Yii::app()->user->login($this->getIdentity(), Yii::app()->params['loginRememberTime']);

//                $user = User::model()->findByAttributes(array('login' => $this->login));
//                if ($user && filter_var($this->login, FILTER_VALIDATE_EMAIL) === false && !UserHelper::isLogged($user->userId)) { // Send SMS Login
//                    $response = $this->sendSMSLogin($this->login);
//                    Yii::log("SMS for User Login ".$this->login." #".$user->userId, CLogger::LEVEL_WARNING, "WEB");
//                    Yii::log("SMS system answer: " . print_r($response, TRUE), CLogger::LEVEL_WARNING);
//                }

                return true;
            }

            return false;
        }
    }
    