<?php

    class UserHelper
    {

        const SCHEMA_ONLINE = 'online';
        const SCHEMA_DETAIL = 'detail';
        const SCHEMA_MINE = 'mine';
        const SCHEMA_FRIENDS = 'friends';

        public static function getDefaultImage()
        {
            return '/content/images/users/user_default.png';
        }

        public static function generateToken()
        {
            return md5(time().Yii::app()->params['salt'].uniqid());
        }

        public static function getIsFilled(User $user)
        {
            return (int)($user->name && $user->email && $user->birthday && $user->phone && $user->login && $user->password);
        }

        /**
         * 
         * @param integer $userId
         * @param string $platform
         * @return UserApiToken|null
         */
        public static function createAuthToken($userId, $platform)
        {
            $apiToken = new UserApiToken();
            $apiToken->userId = $userId;
            $apiToken->platform = $platform;
            $apiToken->token = self::generateToken();
            $apiToken->dateCreated = time();

            if($apiToken->save())
            {
                return $apiToken;
            }

            return null;
        }

        public static function sendSMSLogin($login)
        {
            // Send login to SMS service
            $url = "http://anketa.clab.by/firstapplogin.php";

            // Initialize cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_POSTFIELDS, array('login' => CommonHelper::encodeLogin($login)));
            // Pass TRUE or 1 if you want to wait for and catch the response against the request made
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            // For Debug mode; shows up any error encountered during the operation
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            // Execute the request
            $response = curl_exec($ch);
            return $response;
        }

        /**
         *
         * @param integer $userId
         * @return UserApiToken|null
         */
        public static function isLogged($userId)
        {
            $userToken = UserApiToken::model()->findByAttributes(array('userId' =>  $userId));

            $userOnline = UserOnline::model()->findByAttributes(array('userId' =>  $userId));
            return ($userToken || $userOnline) ? TRUE : FALSE;
        }

        /**
         * 
         * @return Account|null
         */
        public static function createAccount($type = null)
        {
            if(!$type)
            {
                $type = Account::TYPE_USER;
            }
            $account = new Account();
            $account->type = $type;
            $account->isActive = 1;
            $account->dateCreated = time();

            if($account->save())
            {
                Yii::app()->authManager->assign($type, $account->accountId);
                return $account;
            }
            return null;
        }

        public static function export(User $user, $schema = '')
        {
            $schema = CommonHelper::parseSchema($schema);
            // base schema
            $name = explode(' ', $user->name);
            if (count($name) == 1) {
                $firstname = $name[0];
                $lastname = '';
            } elseif(count($name) == 2) {
                $firstname = $name[1];
                $lastname = $name[0];
            } else {
                $firstname = array_pop($name);
                $lastname = implode(' ', $name);
            }
            $data = array(
                'userId' => (int)$user->userId,
                'name' => (string)$user->name,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'birthday' => $user->birthday?date(Yii::app()->params['dateFormat'], $user->birthday):'',
            );

            if(in_array(self::SCHEMA_ONLINE, $schema))
            {
                $data['isOnline'] = (bool)$user->isOnline;
            }

            if(in_array(self::SCHEMA_DETAIL, $schema))
            {
                $data += array(
                    'favoriteMusicGenre' => (string)$user->favoriteMusicGenre,
                    'favoriteCigaretteBrand' => (string)$user->favoriteCigaretteBrand,
                    'counters' => array(
                        'friends' => (int)$user->friendsCount,
                        'events' => self::getComingEventsCount($user->userId),
                        'pastEvents' => self::getpastEventsCount($user->userId)
                    )
                );
            }

            if(in_array(self::SCHEMA_MINE, $schema))
            {
                $data += array(
                    'email' => (string)$user->email,
                    'phone' => (int)$user->phone,
                    'phoneCode' => (int)$user->phoneCode,
                    'messenger' => (string)$user->messenger,
                    'messengerLogin' => (string)$user->messengerLogin,
                    'login' => (string)$user->login,
                    'isFilled' => (bool)$user->isFilled,
                    'isVerified' => (bool)$user->isVerified,
                    'token' => '',
                    'pushToken' => '',
                    'socials' => array(
                        'facebook' => (string)self::getSocialId('facebook', $user),
                        'vkontakte' => (string)self::getSocialId('vkontakte', $user),
                        'twitter' => (string)self::getSocialId('twitter', $user),
                    ),
                    'points' => (int)$user->points
                );
                $data['counters']['notifications'] = 0;
                $data['settings'] = UserNotificationsHelper::getSettings($user->userId);
            }

            if(in_array(self::SCHEMA_FRIENDS, $schema))
            {
                $currentUser = Yii::app()->getController()->getUser();
                /* @var $currentUser User */

                if($currentUser->userId != $user->userId)
                {
                    $data['isFriend'] = self::isFriend($user->userId, $currentUser);
                    if(!$data['isFriend'])
                    {
                        $data['friendshipRequest'] = self::hasFriendshipRequest($currentUser->userId, $user->userId);
                        $data['friendshipRequestToMe'] = self::hasFriendshipRequestToMe($currentUser->userId, $user->userId);
                    }
                }
            }

            return $data;
        }

        public static function isFriend($friendId, User $user)
        {
            foreach($user->friends as $friend)
            {
                if($friend->userId == $friendId)
                {
                    return true;
                }
            }
            return false;
        }

        public static function getSocialId($type, User $user)
        {
            foreach($user->socials as $social)
            {
                if($social->type == $type)
                {
                    return $social->socialId;
                }
            }

            return null;
        }

        public static function getPushToken(User $user, $token, $platform)
        {
            foreach($user->pushTokens as $pushToken)
            {
                if($pushToken->apiToken == $token && $pushToken->platform == $platform)
                {
                    return $pushToken->pushToken;
                }
            }

            return null;
        }

        public static function hasFriendshipRequest($currentUserId, $userId)
        {
            return (bool)UserFriendRequest::model()->countByAttributes(array('userId' => $currentUserId, 'recipientId' => $userId));
        }

        public static function hasFriendshipRequestToMe($currentUserId, $userId)
        {
            return (bool)UserFriendRequest::model()->countByAttributes(array('userId' => $userId, 'recipientId' => $currentUserId));
        }

        public static function getAge($birthday)
        {
            if($birthday)
            {
                $tz = new DateTimeZone(date_default_timezone_get());
                $age = DateTime::createFromFormat(Yii::app()->params['dateFormat'], $birthday, $tz)
                                ->diff(new DateTime('now', $tz))
                        ->y;

                if($age)
                {
                    return Yii::t('application', '{age} лет', array('{age}' => $age));
                }
            }

            return '';
        }

        public static function getComingEventsCount($userId)
        {
            $criteria = new CDbCriteria();
            $criteria->alias = 'e';
            $criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
            $criteria->addCondition('e.eventId IN (SELECT eu.eventId FROM '.EventUser::model()->tableName().' eu WHERE eu.userId = :selectedUser)');
            $criteria->addCondition('e.dateStart >= '.time());
            $criteria->params[':selectedUser'] = $userId;

            return (int)Event::model()->count($criteria);
        }

        public static function getpastEventsCount($userId)
        {
            $criteria = new CDbCriteria();
            $criteria->alias = 'e';
            $criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
            $criteria->addCondition('e.eventId IN (SELECT eu.eventId FROM '.EventUser::model()->tableName().' eu WHERE eu.userId = :selectedUser)');
            $criteria->addCondition('e.dateStart < '.time());
            $criteria->params[':selectedUser'] = $userId;

            return (int)Event::model()->count($criteria);
        }

        public static function getAdminTypes()
        {
            return array(
                Account::TYPE_ADMIN => Yii::t('application', 'Администратор'),
                Account::TYPE_MODERATOR => Yii::t('application', 'Модератор'),
                Account::TYPE_OPERATOR => Yii::t('application', 'Оператор'),
            );
        }

    }
    