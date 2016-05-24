<?php

    class ApiEventIsOwnerFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $controller = $filterChain->controller;
            /* @var $controller ApiController */

            $currentUser = $controller->getUser();
            $event = Event::model()->findByPk(Api::getParam('eventId'));
            /* @var $event Event */

            if($event->userId != $currentUser->userId)
            {
                throw new ApiException(Api::CODE_FORBIDDEN);
            }

            return true;
        }

    }
    