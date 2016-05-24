<?php

    class NotificationController extends WebController
    {

        public $notificationsLimit = 50;

        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl',
                array('ProfileIsFilledFilter')
            );
        }

        /**
         * Specifies the access control rules.
         * This method is used by the 'accessControl' filter.
         * @return array access control rules
         */
        public function accessRules()
        {
            return array(
                array('allow',
                    'actions' => array('index'),
                    'roles' => array('user'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $currentUser = $this->getUser();

            $criteria = new CDbCriteria();
            $criteria->index = 'userNotificationId';
            $criteria->addColumnCondition(array('userId' => $currentUser->userId));
            $criteria->offset = Web::getParam('offset', 0);
            $criteria->limit = Web::getParam('limit', $this->notificationsLimit);
            $criteria->order = 'dateCreated DESC';

            $notifications = UserNotification::model()->findAll($criteria);
            $notificationIds = array_keys($notifications);

            $readedCriteria = new CDbCriteria();
            $readedCriteria->addInCondition('userNotificationId', $notificationIds);
            UserNotification::model()->updateAll(array('isReaded' => 1, 'isPushed' => 1), $readedCriteria);

            if(Yii::app()->request->isAjaxRequest)
            {
                $this->renderPartial('_notification_items', array('notifications' => $notifications));
            }
            else
            {
                $this->render('index', array('notifications' => $notifications));
            }
        }

    }
    