<?php

    class UserNotificationsHelper
    {

        public static function getDefaultSettins()
        {
            return array(
                UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST => 1,
                UserNotificationSetting::SETTING_EVENT_INVITE => 1,
                UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE => 1,
                UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED => 1,
                UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED => 1,
                UserNotificationSetting::SETTING_EVENT_NEW_COMMENT => 1,
                UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED => 1,
                UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER => 1,
                UserNotificationSetting::SETTING_MY_EVENT_NEW_COMMENT => 1,
                UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH => 1,
                UserNotificationSetting::SETTING_SEND_TO_EMAIL => 0,
                UserNotificationSetting::SETTING_MONTLY_DIGEST => 1,
                UserNotificationSetting::SETTING_ANDROID_ENABLE_VIBRATION => 1
            );
        }
        
        public static function getSettings($userId)
        {
            $notificationSettings = UserNotificationSetting::model()->findAllByAttributes(array('userId' => $userId));
            $settings = self::getDefaultSettins();
            foreach($notificationSettings as $item)
            {
                /* @var $item UserNotificationSetting */
                $settings[$item->settingKey] = (int)$item->isChecked;
            }
            
            return $settings;
        }
        
        public static function getSettingLabels()
        {
            return array(
                UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST => Yii::t('application', 'Уведомления о дружбе'),
                UserNotificationSetting::SETTING_EVENT_INVITE => Yii::t('application', 'Уведомления о приглашении на мероприятия друзей'),
                UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE => Yii::t('application', 'Уведомления о глобальных мероприятиях'),
                UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED => Yii::t('application', 'Уведомления о подписке друга на определенное мероприятие'),
                UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED => Yii::t('application', 'Уведомления об обновлении галереи мероприятия'),
                UserNotificationSetting::SETTING_EVENT_NEW_COMMENT => Yii::t('application', 'Уведомления об записи в чате мероприятия'),
                UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED => Yii::t('application', 'Уведомления прошло ли мое мероприятие модерацию, удаление мероприятия'),
                UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER => Yii::t('application', 'Уведомления о вступлении в мое мероприятие'),
                UserNotificationSetting::SETTING_MY_EVENT_NEW_COMMENT => Yii::t('application', 'Уведомления о записи в чате моего мероприятия'),
                UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH => Yii::t('application', 'Уведомление о появлении новых вопросов в разделе опросы'),
                UserNotificationSetting::SETTING_SEND_TO_EMAIL => Yii::t('application', 'Все уведомления могут приходить на e-mail'),
                UserNotificationSetting::SETTING_MONTLY_DIGEST => Yii::t('application', 'Дайджест новостей раз в месяц на почту'),
                UserNotificationSetting::SETTING_ANDROID_ENABLE_VIBRATION => Yii::t('application', 'Включение/отключение звука, вибрации в Android приложении'),
            );
        }

        public static function createSettings($userId)
        {
            $defaultSettings = self::getDefaultSettins();
            foreach($defaultSettings as $settingKey => $isChecked)
            {
                $notificationSetting = new UserNotificationSetting();
                $notificationSetting->userId = $userId;
                $notificationSetting->settingKey = $settingKey;
                $notificationSetting->isChecked = $isChecked;
                if(!$notificationSetting->save())
                {
                    return false;
                }
            }
            return true;
        }

        public static function saveSettings($userId, $settings)
        {
            $defaultSettings = self::getDefaultSettins();
            foreach($defaultSettings as $settingKey => $isChecked)
            {
                $notificationSetting = UserNotificationSetting::model()->findByAttributes(array('userId' => $userId, 'settingKey' => $settingKey));
                if(!$notificationSetting)
                {
                    $notificationSetting = new UserNotificationSetting();
                    $notificationSetting->userId = $userId;
                    $notificationSetting->settingKey = $settingKey;
                }
                if(UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH == $settingKey)
                {
                    $notificationSetting->isChecked = 1;
                }
                else
                {
                    $notificationSetting->isChecked = (isset($settings[$settingKey]) && $settings[$settingKey])?1:0;
                }
                
                if(!$notificationSetting->save())
                {
                    return false;
                }
            }
            return true;
        }
        
        public static function exportNotification(UserNotification $notification)
        {
            return array(
                'userNotificationId' => (integer)$notification->userNotificationId,
                'settingKey' => (string)$notification->settingKey,
                'params' => CJSON::decode($notification->params),
                'notification' => (string)$notification->notificationText,
                'isReaded' => (bool)$notification->isReaded,
                'dateCreated' => date(Yii::app()->params['dateTimeFormat'], $notification->dateCreated)
            );
        }
        
        public static function addNotification($settingKey, $userId, $data = array())
        {
            $params = CJSON::encode($data);
            if(UserNotification::model()->findByAttributes(array('settingKey' => $settingKey, 'userId' => $userId, 'params' => $params)))
            {
                return true;
            }
            
            $userNotification = new UserNotification();
            $userNotification->userId = $userId;
            $userNotification->settingKey = $settingKey;
            $userNotification->params = $params;
            $userNotification->notificationText = self::getNotificationText($settingKey, $data);
            $userNotification->isReaded = 0;
            $userNotification->isPushed = 0;
            $userNotification->dateCreated = time();
            
            if(!$userNotification->save())
            {
                return false;
            }
            
            return true;
        }
        
        public static function getNotificationText($settingKey, $data)
        {
            $text = '';
            
            if(in_array($settingKey, array(UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST, UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST_ADDED, UserNotificationSetting::SETTING_EVENT_INVITE,
                UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED, UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER)))
            {
                $user = User::model()->findByPk($data['userId']);
                /* @var $user User */
            }
            
            if(in_array($settingKey, array(UserNotificationSetting::SETTING_EVENT_INVITE, UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE,
                UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED, UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED, 
                UserNotificationSetting::SETTING_EVENT_NEW_COMMENT, UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED,
                UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER, UserNotificationSetting::SETTING_MY_EVENT_NEW_COMMENT)))
            {
                $event = Event::model()->findByPk($data['eventId']);
                /* @var $event Event */
            }
            
            switch($settingKey)
            {
                case UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST:
                    $text = Yii::t('application', '<b>{username}</b> хочет добавить вас в друзья', array('{username}' => $user->name));
                    break;
                
                case UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST_ADDED:
                    $text = Yii::t('application', '<b>{username}</b> добавил вас в друзья', array('{username}' => $user->name));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_INVITE:
                    
                    $text = Yii::t('application', '<b>{username}</b> приглашает вас на мероприятие <b>{event}</b>', array('{username}' => $user->name, '{event}' => $event->name));
                    break;

                case UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE:
                    $text = Yii::t('application', 'Глобальное мероприятие <b>{event}</b>', array('{event}' => $event->name));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED:
                    $text = Yii::t('application', 'Ваш друг <b>{username}</b> идет на мероприятие <b>{event}</b>', array('{username}' => $user->name, '{event}' => $event->name));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED:
                    $text = Yii::t('application', 'В мероприятие <b>{event}</b> добавлен фотоотчет', array('{event}' => $event->name));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_NEW_COMMENT:
                    $text = Yii::t('application', 'В мероприятии <b>{event}</b> новый комментарий', array('{event}' => $event->name));
                    break;
                
                case UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED:
                    if($event->status == Event::STATUS_APPROVED)
                    {
                        $text = Yii::t('application', 'Ваше мероприятие <b>{event}</b> подтверждено модераторами', array('{event}' => $event->name));
                    }
                    else
                    {
                        $text = Yii::t('application', 'Ваше мероприятие <b>{event}</b> отклонено модераторами', array('{event}' => $event->name));
                    }
                    break;
                    
                case UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER:
                    $text = Yii::t('application', '<b>{username}</b> подписался на ваше мероприятие <b>{event}</b>', array('{username}' => $user->name, '{event}' => $event->name));
                    break;
                
                case UserNotificationSetting::SETTING_MY_EVENT_NEW_COMMENT:
                    $text = Yii::t('application', 'В вашем мероприятии <b>{event}</b> новый комментарий', array('{event}' => $event->name));
                    break;
                
                case UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH:
                    $text = Yii::t('application', 'Доступно новое маркетинговое исследование');
                    break;
            }
            
            return $text;
        }
        
        public static function getNotificationLink($settingKey, $data)
        {
            $link = '';
            $controller = Yii::app()->getController();
            /* @var $controller WebController */
            
            switch($settingKey)
            {
                case UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST:
                    $link = $controller->createUrl('user/detail', array('userId' => $data['userId']));
                    break;
                
                case UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST_ADDED:
                    $link = $controller->createUrl('user/detail', array('userId' => $data['userId']));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_INVITE:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;

                case UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                
                case UserNotificationSetting::SETTING_EVENT_NEW_COMMENT:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                
                case UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                    
                case UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                
                case UserNotificationSetting::SETTING_MY_EVENT_NEW_COMMENT:
                    $link = $controller->createUrl('event/detail', array('eventId' => $data['eventId']));
                    break;
                
                case UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH:
                    $link = $controller->createUrl('pro/marketing_research', array('researchId' => 1));
                    break;
            }
            
            return $link;
        }

    }
    