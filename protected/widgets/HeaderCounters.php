<?php

    class HeaderCounters extends Widget
    {

        public function run()
        {
            $controller = $this->getController();
            $user = $controller->getUser();
            /* @var $user User */
            $this->render('header_counters', array(
                'controller' => $controller,
                'unreadedMessagesCount' => $user->unreadedMessagesCount,
                'unreadedNotificationsCount' => $user->unreadedNotificationsCount
            ));
        }

    }
    