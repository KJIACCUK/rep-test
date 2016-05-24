<?php

    class ProfileIsVerifiedFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $controller = $filterChain->controller;
            /* @var $controller WebController */
            
            $currentUser = $controller->getUser();
            /* @var $currentUser User */
            
            if(!$currentUser->isVerified)
            {
                $controller->redirect(array('user/askVerification'));
            }
            
            return true;
        }

    }
    