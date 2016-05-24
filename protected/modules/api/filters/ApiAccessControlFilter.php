<?php

    class ApiAccessControlFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $token = Api::getToken();
            $controller = $filterChain->controller;
            /* @var $controller ApiController */
            
            if($token)
            {
                $apiToken = UserApiToken::model()->findByAttributes(array('token' => $token));
                /* @var $apiToken UserApiToken */  
                if($apiToken)
                {
                    $user = User::model()->with('account', 'friendsCount', 'pushTokens', 'socials')->findByPk($apiToken->userId);
                    /* @var $user User */
                    
                    if($user && $user->account && $user->account->type == Account::TYPE_USER && $user->account->isActive)
                    {
                        $controller->setUser($user);
                        $controller->setToken($token);
                        return true;
                    }
                }
            }

            throw new ApiException(Api::CODE_UNAUTHORIZED);
        }

    }
    