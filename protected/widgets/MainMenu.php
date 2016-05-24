<?php

    class MainMenu extends Widget
    {

        public function run()
        {
            $controller = $this->getController();
            $actionId = $controller->getAction()->getId();
            $activeMenu = '';
            switch($controller->getId())
            {
                case 'user':
                    if($actionId == 'settings' || $actionId == 'feedback' || $actionId == 'help')
                    {
                        $activeMenu = 'settings';
                    }
                    else
                    {
                        $activeMenu = 'my_page';
                    }
                    break;

                case 'store':
                case 'marketingResearch':
                case 'promo':
                    $activeMenu = 'pro';
                    break;

                case 'event':
                    if($actionId == 'calendar')
                    {
                        $activeMenu = 'calendar';
                    }
                    elseif($actionId == 'map')
                    {
                        $activeMenu = 'map';
                    }
                    else
                    {
                        $activeMenu = 'events';
                    }
                    break;
            }
            $this->render('main_menu', array('activeMenu' => $activeMenu, 'controller' => $controller));
        }

    }
    