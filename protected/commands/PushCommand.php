<?php

    class PushCommand extends CConsoleCommand
    {

        public function actionIndex()
        {
            print 'Budutam Push Command';
        }

        public function actionNotifications()
        {
            set_time_limit(0);
            $db = Yii::app()->db;

            $offset = 0;
            $limit = 100;
            $hasRecords = true;

            $gcm = Yii::app()->googlegcm;
            $params = array(
                'type' => 'has_unreaded_notifications',
                'title' => Yii::t('application', 'У вас есть непрочитанные уведомления')
            );

            while($hasRecords)
            {
                $users = $db->createCommand()
                        ->select('userId, GROUP_CONCAT(DISTINCT settingKey SEPARATOR \',\') AS settingKeys')
                        ->from('user_notification')
                        ->where('isPushed = 0')
                        ->group('userId')
                        ->order('userId ASC')
                        ->offset($offset)
                        ->limit($limit)
                        ->queryAll();

                if(count($users))
                {
                    $userIds = array();
                    $userPushes = array();
                    $pushList = array();

                    foreach($users as $item)
                    {
                        $userIds[] = $item['userId'];
                        $userPushes[$item['userId']] = explode(',', $item['settingKeys']);
                    }

                    $settings = $this->getPositiveSettingsMultiple($userIds);

                    foreach($userPushes as $userId => $settingKeys)
                    {
                        if(!isset($settings[$userId]))
                        {
                            $pushList[] = $userId;
                        }
                        else
                        {
                            $intersect = array_intersect($settingKeys, $settings[$userId]);
                            if(count($intersect))
                            {
                                $pushList[] = $userId;
                            }
                        }
                    }

                    $updateCriteria = new CDbCriteria();
                    $updateCriteria->addInCondition('userId', $userIds);
                    UserNotification::model()->updateAll(array('isPushed' => 1), $updateCriteria);

                    $tokensCriteria = new CDbCriteria();
                    $tokensCriteria->index = 'pushToken';
                    $tokensCriteria->addInCondition('userId', $userIds);
                    $tokens = UserPushToken::model()->findAll($tokensCriteria);

                    if($tokens)
                    {
                        $tokens = array_keys($tokens);
                        $gcm->send($tokens, $params);
                    }

                    $offset += $limit;
                }
                else
                {
                    $hasRecords = false;
                }
            }
            //Yii::log('Pushes were sent', CLogger::LEVEL_WARNING, 'PushCommand.notifications');
        }

        public function actionNew_global_events()
        {
            set_time_limit(0);

            $time = time();
            $gcm = Yii::app()->googlegcm;
            $gcmParams = array(
                'type' => 'new_global_event',
                'title' => Yii::t('application', 'Новое мероприятие')
            );
            $userNotificationsTableName = UserNotification::model()->tableName();
            $commandBuilder = Yii::app()->db->schema->commandBuilder;

            while(($event = Event::model()->findByAttributes(array('isPushed' => 0, 'isGlobal' => 1))))
            {
                /* @var $event Event */

                $criteria = new CDbCriteria();
                $criteria->offset = 0;
                $criteria->limit = 100;
                $criteria->order = 'userId ASC';
                $criteria->with = array('pushTokens', 'settings' => array('index' => 'settingKey'));
                $androidTokens = array();

                while(($users = User::model()->findAll($criteria)))
                {
                    $notifications = array();

                    /* @var $users User[] */
                    foreach($users as $item)
                    {
                        $params = array('eventId' => $event->eventId);
                        $isSettingChecked = !isset($item->settings[UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE]) || ($item->settings[UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE]->isChecked == 1);

                        $notifications[] = array(
                            'userId' => $item->userId,
                            'settingKey' => UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE,
                            'params' => CJSON::encode($params),
                            'notificationText' => UserNotificationsHelper::getNotificationText(UserNotificationSetting::SETTING_EVENT_GLOBAL_INVITE, $params),
                            'isReaded' => 0,
                            'isPushed' => $isSettingChecked?1:0,
                            'dateCreated' => $time
                        );

                        if($isSettingChecked)
                        {
                            foreach($item->pushTokens as $token)
                            {
                                if($token->platform == 'android')
                                {
                                    $androidTokens[] = $token->pushToken;
                                    if(count($androidTokens) == 1000)
                                    {
                                        $gcm->send($androidTokens, $gcmParams);
                                        $androidTokens = array();
                                    }
                                }
                            }
                        }
                    }

                    $command = $commandBuilder->createMultipleInsertCommand($userNotificationsTableName, $notifications);
                    try
                    {
                        $command->execute();
                    }
                    catch(Exception $ex)
                    {
                        
                    }

                    $criteria->offset += $criteria->limit;
                }

                $event->isPushed = 1;
                $event->save(false);
            }
        }

        public function actionNew_marketing_researches()
        {
            set_time_limit(0);

            $time = time();
            $gcm = Yii::app()->googlegcm;
            $gcmParams = array(
                'type' => 'new_marketing_research',
                'title' => Yii::t('application', 'Новое маркетинговое исследование')
            );
            $userNotificationsTableName = UserNotification::model()->tableName();
            $commandBuilder = Yii::app()->db->schema->commandBuilder;

            while(($research = MarketingResearch::model()->findByAttributes(array('isPushed' => 0, 'isEnabled' => 1))))
            {
                /* @var $research MarketingResearch */

                $criteria = new CDbCriteria();
                $criteria->offset = 0;
                $criteria->limit = 100;
                $criteria->order = 'userId ASC';
                $criteria->with = array('pushTokens');
                $androidTokens = array();

                while(($users = User::model()->findAll($criteria)))
                {
                    /* @var $users User[] */
                    $notifications = array();
                    foreach($users as $item)
                    {
                        $params = array();

                        $notifications[] = array(
                            'userId' => $item->userId,
                            'settingKey' => UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH,
                            'params' => CJSON::encode($params),
                            'notificationText' => UserNotificationsHelper::getNotificationText(UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH, $params),
                            'isReaded' => 0,
                            'isPushed' => 1,
                            'dateCreated' => $time
                        );

                        foreach($item->pushTokens as $token)
                        {
                            if($token->platform == 'android')
                            {
                                $androidTokens[] = $token->pushToken;
                                if(count($androidTokens) == 1000)
                                {
                                    $gcm->send($androidTokens, $gcmParams);
                                    $androidTokens = array();
                                }
                            }
                        }
                    }

                    $command = $commandBuilder->createMultipleInsertCommand($userNotificationsTableName, $notifications);
                    try
                    {
                        $command->execute();
                    }
                    catch(Exception $ex)
                    {

                    }

                    $criteria->offset += $criteria->limit;
                }

                $research->isPushed = 1;
                $research->save(false);
            }
        }

        private function getPositiveSettingsMultiple($userIds)
        {
            $settings = array();
            $notificationSettings = UserNotificationSetting::model()->findAllByAttributes(array('userId' => $userIds));
            foreach($notificationSettings as $item)
            {
                /* @var $item UserNotificationSetting */
                if(!isset($settings[$item->userId]))
                {
                    $settings[$item->userId] = UserNotificationsHelper::getDefaultSettins();
                }
                if(!$item->isChecked)
                {
                    unset($settings[$item->userId][$item->settingKey]);
                }
            }
            return $settings;
        }

    }
    