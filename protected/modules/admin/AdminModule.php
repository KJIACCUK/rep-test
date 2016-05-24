<?php

    class AdminModule extends CWebModule
    {

        public function init()
        {
            $this->setImport(array(
                'admin.models.*',
                'admin.components.*',
            ));

            $this->setComponents(array(
                'errorHandler' => array(
                    'errorAction' => 'admin/default/error'),
                'user' => array(
                    'class' => 'CWebUser',
                    'loginUrl' => Yii::app()->createUrl('admin/default/login'),
                //'stateKeyPrefix' => '_admin'
                )
            ));
        }

        public function beforeControllerAction($controller, $action)
        {
            if(parent::beforeControllerAction($controller, $action))
            {
                $route = $controller->id.'/'.$action->id;
                $publicPages = array(
                    'default/login',
                    'default/error',
                );

                if(Yii::app()->user->isGuest && !in_array($route, $publicPages))
                {
                    /* set the return URL */
                    $request = Yii::app()->request->getUrl();
                    Yii::app()->user->returnURL = $request;
                    Yii::app()->getModule('admin')->user->loginRequired();
                }
                else
                {
                    return true;
                }
            }
            return false;
        }

    }
    