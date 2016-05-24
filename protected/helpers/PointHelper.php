<?php

    class PointHelper
    {
        public static function addPoints($pointKey, $userId, $params = array())
        {
            $params_array = $params;
            $params = CJSON::encode($params);
            $point = Point::model()->findByAttributes(array('pointKey' => $pointKey));
            /* @var $point Point */
            if($pointKey !== Point::KEY_FROM_ADMIN && PointUser::model()->findByAttributes(array('pointId' => $point->pointId, 'userId' => $userId, 'params' => $params)))
            {
                return true;
            }
            
            $pointUser = new PointUser();
            $pointUser->pointId = $point->pointId;
            $pointUser->userId = $userId;            
            $pointUser->params = $params;
            $pointUser->dateCreated = time();
            if ($pointKey == Point::KEY_FROM_ADMIN)
                $pointUser->pointsCount = $params_array['count'];
            else
                $pointUser->pointsCount = $point->pointsCount;

            if(!$pointUser->save())
            {
                return false;
            }

            if ($pointKey == Point::KEY_FROM_ADMIN)
                User::model()->updateByPk($userId, array('points' => new CDbExpression('points + :points', array(':points' => $params_array['count']))));
            else
                User::model()->updateByPk($userId, array('points' => new CDbExpression('points + :points', array(':points' => $point->pointsCount))));
            
            return true;
        }

        public static function hasPoint($pointKey, $userId, $params = array())
        {
            $params = CJSON::encode($params);
            $point = Point::model()->findByAttributes(array('pointKey' => $pointKey));
            /* @var $point Point */
            if(PointUser::model()->findByAttributes(array('pointId' => $point->pointId, 'userId' => $userId, 'params' => $params)))
            {
                return true;
            }
            return false;
        }

        public static function categoryNameById($id)
        {
            $categories = array(
              Point::KEY_SOCIAL_INVITE => 'Пригласил друга',
              Point::KEY_VERIFICATION => 'Прошел верификацию',
              Point::KEY_MARKETING_RESEARCH_VISIT => 'Посетил раздел МИ',
              Point::KEY_MARKETING_RESEARCH_ANSWER => 'Ответил на вопрос в разделе МИ',
              Point::KEY_EVENT_CREATE => 'Создал мероприятие',
              Point::KEY_SOCIAL_SHARE => 'Поделился в соц. сети',
              Point::KEY_TEN_EVENTS_SUBSCRIBED => 'Подписался на 10 мероприятий',
              Point::KEY_FROM_ADMIN => 'Добавлено в админке'
            );
            return $categories[$id];
        }
    }
    