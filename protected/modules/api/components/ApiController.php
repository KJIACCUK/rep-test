<?php

    class ApiController extends CController
    {
        /**
         *
         * @var User
         */
        protected $_user;
        
        /**
         *
         * @var string
         */
        protected $_token;
        
        public function getUser()
        {
            return $this->_user;
        }
        
        public function setUser(User $user)
        {
            $this->_user = $user;
        }
        
        public function getToken()
        {
            return $this->_token;
        }
        
        public function setToken($token)
        {
            $this->_token = $token;
        }
    }
    