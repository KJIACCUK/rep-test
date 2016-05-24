<?php

class EventHelper
{
    const SCHEMA_DETAIL = 'detail';
    const SCHEMA_MINE = 'mine';
    const SCHEMA_MAP = 'map';
    const GET_EVENTS_MODE_LIST = 'list';
    const GET_EVENTS_MODE_MINE = 'mine';
    const GET_EVENTS_MODE_CALENDAR = 'calendar';
    const GET_EVENTS_MODE_MAP = 'map';
    const GET_EVENTS_ORDER_DATE_CREATED = 'dateCreated';
    const GET_EVENTS_ORDER_DATE_START = 'dateStart';
    const GET_EVENTS_ORDER_SUBSCRIBERS_COUNT = 'subscribersCount';

    public static function getDefaultImage()
    {
        return '/content/images/events/event_default.png';
    }

    public static function export(Event $event, $schema = '')
    {
        $schema = CommonHelper::parseSchema($schema);

        $currentUser = Yii::app()->getController()->getUser();
        /* @var $currentUser User */

        $data = array(
            'eventId' => (int)$event->eventId,
            'name' => (string)$event->name,
            'publisherName' => (string)$event->publisherName,
            'dateStart' => (string)date(Yii::app()->params['dateFormat'], $event->dateStart),
            'timeStart' => (string)$event->timeStart,
            'isSubscribe' => (bool)$event->isSubscribed,
            'isMine' => (bool)($currentUser && $event->userId == $currentUser->userId),
            'isGlobal' => (bool)$event->isGlobal,
            'isPublic' => (bool)$event->isPublic,
            'isRelax' => (bool)$event->relaxId,
            'subscribersCount' => (int)$event->subscribersCount,
            'subscribersFriendsCount' => (int)$event->subscribersFriendsCount
        );

        if (in_array(self::SCHEMA_DETAIL, $schema)) {
            $data += array(
                'category' => (string)$event->category,
                'description' => (string)$event->description,
                'city' => (string)$event->cityObject->name,
                'street' => (string)$event->street,
                'houseNumber' => (string)$event->houseNumber,
                'productId' => (integer)$event->productId,
                'timeEnd' => $event->timeEnd?(string)$event->timeEnd:'',
                'dateCreated' => date(Yii::app()->params['dateTimeFormat'], $event->dateCreated)
            );
        }

        if (in_array(self::SCHEMA_MINE, $schema)) {
            $data += array(
                'status' => $event->status,
            );
        }

        if (in_array(self::SCHEMA_MAP, $schema)) {
            $data += array(
                'latitude' => $event->latitude,
                'longitude' => $event->longitude,
            );
        }

        return $data;
    }

    /**
     * 
     * @param Event $event
     * @param integer $userId
     * @return EventUser|null
     */
    public static function createDefaultSubscription(Event $event, $userId)
    {
        $eventUser = new EventUser();
        $eventUser->eventId = $event->eventId;
        $eventUser->userId = $userId;
        $eventUser->dateCreated = time();
        if ($eventUser->save()) {
            return $eventUser;
        }

        return null;
    }

    public static function getAccessList()
    {
        return array(
            Event::EVENT_ACCESS_ALL => Yii::t('application', 'Доступно всем'),
            Event::EVENT_ACCESS_FRIENDS => Yii::t('application', 'Доступно только друзьям')
        );
    }

    public static function getStatusesList()
    {
        return array(
            Event::STATUS_APPROVED => Yii::t('application', 'Разрешено'),
            Event::STATUS_WAITING => Yii::t('application', 'В ожидании'),
            Event::STATUS_DECLINED => Yii::t('application', 'Запрещено'),
        );
    }

    public static function getDaysOfWeek($week, $year)
    {
        $dateTime = new DateTime();
        $result = array();
        $result[] = $dateTime->setISODate($year, $week)->format('Y-m-d');
        for ($i = 1; $i < 7; $i++) {
            $result[] = $dateTime->modify('+1 day')->format('Y-m-d');
        }
        return $result;
    }

    public static function getRelaxErrorDescription($error)
    {
        switch ($error) {
            case 'NO_DATA':
                return Yii::t('application', 'Поле не заполнено');
            case 'TOO_LONG':
                return Yii::t('application', 'Поле слишком длинное и было обрезано');
        }
    }

    public static function getRelaxErrors($data)
    {
        $result = array();
        $event = new Event();
        if ($data && ($data = CJSON::decode($data))) {
            foreach ($data as $attribute => $error) {
                $result[$event->getAttributeLabel($attribute)] = self::getRelaxErrorDescription($error);
            }
        }
        return $result;
    }

    public static function getRelaxErrorsCount($data)
    {
        if ($data && ($data = CJSON::decode($data))) {
            $count = 0;
            foreach ($data as $attribute => $error) {
                $count++;
            }
            return $count;
        }
        return 0;
    }
}