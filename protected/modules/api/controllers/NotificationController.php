<?php

    class NotificationController extends ApiController
    {
        public function filters()
        {
            return array(
                array('ApiAccessControlFilter'),
                array('ApiProfileIsFilledFilter')
            );
        }
        
        public function actionGetNotifications()
        {
            $currentUser = $this->getUser();
            
            $criteria = new CDbCriteria();
            $criteria->index = 'userNotificationId';
            $criteria->addColumnCondition(array('userId' => $currentUser->userId));
            $criteria->offset = Api::getParam('offset', 0);
            $criteria->limit = Api::getParam('limit', 50);
            $criteria->order = 'dateCreated DESC';

            $notifications = UserNotification::model()->findAll($criteria);
            $notificationIds = array_keys($notifications);

            $readedCriteria = new CDbCriteria();
            $readedCriteria->addInCondition('userNotificationId', $notificationIds);
            UserNotification::model()->updateAll(array('isReaded' => 1, 'isPushed' => 1), $readedCriteria);
            
            $response = array();
            $response['notifications'] = array();
            
            foreach($notifications as $item)
            {
                /* @var $item UserNotification */
                $response['notifications'][] = UserNotificationsHelper::exportNotification($item);
            }
            
            Api::respondSuccess($response);
        }
    }