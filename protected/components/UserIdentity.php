<?php

    /**
     * UserIdentity represents the data needed to identity a user.
     * It contains the authentication method that checks if the provided
     * data can identity the user.
     */
    class UserIdentity extends CUserIdentity
    {

        private $_id;
        private $_name;

        public function getId()
        {
            return $this->_id;
        }

        public function getName()
        {
            return $this->_name;
        }

        /**
         * Authenticates a user by login and password
         * @return boolean whether authentication succeeds.
         */
        public function authenticate()
        {
            $this->errorCode = self::ERROR_NONE;
            $criteria = new CDbCriteria();
            $criteria->addCondition('email = :email OR login = :email');
            $criteria->params[':email'] = $this->username;
            $user = User::model()->with('account')->find($criteria);
            /* @var $user User */
            if($user && $user->account && $user->account->isActive)
            {
                if($user->password == CommonHelper::md5($this->password))
                {
                    $this->_id = $user->accountId;
                    $this->_name = $user->name;
                }
                else
                {
                    $this->errorCode = self::ERROR_PASSWORD_INVALID;
                }
            }
            else
            {
                $this->errorCode = self::ERROR_USERNAME_INVALID;
            }

            return !$this->errorCode;
        }

    }
    