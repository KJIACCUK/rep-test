<?php

    class ApiEventExistsFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $controller = $filterChain->controller;
            /* @var $controller ApiController */

            $currentUser = $controller->getUser();
            $event = Event::model()->findByPk(Api::getParam('eventId'));
            /* @var $event Event */

            if(!$event)
            {
                throw new ApiException(Api::CODE_NOT_FOUND);
            }

            if($event->userId != $currentUser->userId)
            {
                if($event->status == Event::STATUS_DECLINED)
                {
                    throw new ApiException(Api::CODE_NOT_FOUND);
                }

                if(!$event->isPublic && !(EventUserInvite::model()->countByAttributes(array('eventId' => $event->eventId, 'userId' => $currentUser->userId))))
                {
                    throw new ApiException(Api::CODE_NOT_FOUND);
                }
            }

            return true;
        }

    }
    