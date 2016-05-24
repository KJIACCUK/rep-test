<?php

    class ProfileIsFilledFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $controller = $filterChain->controller;
            /* @var $controller WebController */
            
            $currentUser = $controller->getUser();
            /* @var $currentUser User */
            
            if(!$currentUser->isFilled)
            {
                $controller->redirect(array('user/profileComplete'));
            }
            
            return true;
        }

    }
    