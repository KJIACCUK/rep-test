<?php

class EventController extends ApiController
{

    public function filters()
    {
        return array(
            array('ApiAccessControlFilter'),
            array('ApiProfileIsFilledFilter'),
            array('ApiGetEventsParamsFilter + getEvents'),
            array(
                'ApiParamFilter + addEvent, updateEvent',
                'param' => 'city',
                'function' => 'FilterHelper::checkCity'
            ),
            array(
                'ApiParamFilter + getEvent, addEvent, updateEvent, saveEventImage',
                'param' => 'eventImage',
                'function' => 'FilterHelper::checkImage'
            ),
            array(
                'ApiParamFilter + getEvent, addEvent, updateEvent, saveEventImage',
                'param' => 'galleryImage',
                'function' => 'FilterHelper::checkImage'
            ),
            array(
                'ApiParamFilter + getEvent, addEvent, updateEvent, saveEventImage, addEventAlbum, renameEventAlbum, getEventPhotos',
                'param' => 'albumImage',
                'function' => 'FilterHelper::checkImage'
            ),
            array(
                'ApiParamFilter + getEvent, addEvent, updateEvent, saveEventImage',
                'param' => 'commentImage',
                'function' => 'FilterHelper::checkImage'
            ),
            array(
                'ApiParamFilter + getEvents, getEventComments, addEventComment, getEventSubscribers, '
                .'getEventInvites, saveEventInvites, getEventPhotos, addEventAlbum, renameEventAlbum, '
                .'addEventPhoto, getComingEvents, getPastEvents',
                'param' => 'image',
                'function' => 'FilterHelper::checkImage'
            ),
            array(
                'ApiParamFilter + getComingEvents, getPastEvents',
                'param' => 'userId',
                'function' => 'FilterHelper::checkUserExists'
            ),
            array(
                'ApiParamFilter + getEventPhotos, renameEventAlbum, deleteEventAlbum, addEventPhotoToAlbum',
                'param' => 'albumId',
                'function' => 'FilterHelper::checkEventAlbumExists'
            ),
            array(
                'ApiParamFilter + deleteEventPhoto, addEventPhotoToAlbum, deleteEventPhotoFromAlbum',
                'param' => 'imageId',
                'function' => 'FilterHelper::checkEventImageExists'
            ),
            array(
                'ApiParamFilter + renameEventAlbum, deleteEventAlbum, addEventPhotoToAlbum',
                'param' => 'albumId',
                'function' => 'FilterHelper::checkEventAlbumIsOwner'
            ),
            array(
                'ApiParamFilter + deleteEventPhoto, addEventPhotoToAlbum, deleteEventPhotoFromAlbum',
                'param' => 'imageId',
                'function' => 'FilterHelper::checkEventImageIsOwner'
            ),
            array('ApiEventExistsFilter + getEvent, getEventComments, addEventComment, getEventSubscribers, subscribeToEvent, '
                .'unsubscribeFromEvent, updateEvent, deleteEvent, saveEventImage, getEventInvites, saveEventInvites, getEventPhotos, '
                .'addEventAlbum, addEventPhoto'),
            array('ApiEventIsOwnerFilter + updateEvent, deleteEvent, saveEventImage, getEventInvites, saveEventInvites, '
                .'addEventAlbum, addEventPhoto')
        );
    }

    public function actionGetEvents()
    {
        $currentUser = $this->getUser();
        $mode = Api::getParam('mode', EventHelper::GET_EVENTS_MODE_LIST);
        $schema = '';
        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
        $criteria->alias = 'e';
        $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE eventId = e.eventId AND userId <> :currentUserId AND userId IN (SELECT friendId FROM '.UserFriend::model()->tableName().' WHERE userId = :currentUserId)) AS subscribersFriendsCount';
        switch ($mode) {
            case EventHelper::GET_EVENTS_MODE_LIST:
                $city = YandexMapsHelper::getCityByName(Api::getParam('city', Yii::t('application', 'Минск')));
                $criteria->addColumnCondition(array('e.cityId' => $city->cityId));
                //$criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
                $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
                $criteria->addCondition('e.userId = :currentUserId OR e.isPublic OR e.eventId IN (SELECT eui.eventId FROM '.EventUserInvite::model()->tableName().' eui WHERE eui.userId = :currentUserId)');
                $criteria->addCondition('e.dateStart >= '.time());
                $order = Api::getParam('order', 'dateCreated');
                if ($order == EventHelper::GET_EVENTS_ORDER_SUBSCRIBERS_COUNT) {
                    $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE eventId = e.eventId) AS subscribersCount';
                    $criteria->order = EventHelper::GET_EVENTS_ORDER_SUBSCRIBERS_COUNT.' DESC';
                } elseif ($order == EventHelper::GET_EVENTS_ORDER_DATE_START) {
                    $criteria->order = 'e.'.$order.' ASC';
                } else {
                    $criteria->order = 'e.'.EventHelper::GET_EVENTS_ORDER_DATE_CREATED.' DESC';
                }
                $criteria->offset = Api::getParam('offset', 0);
                $criteria->limit = Api::getParam('limit', 50);
                break;

            case EventHelper::GET_EVENTS_MODE_MINE:
                $criteria->addColumnCondition(array('e.userId' => $currentUser->userId));
                $criteria->order = 'e.dateCreated';
                $criteria->offset = Api::getParam('offset', 0);
                $criteria->limit = Api::getParam('limit', 50);
                $schema = 'mine';
                break;

            case EventHelper::GET_EVENTS_MODE_CALENDAR:
                $city = YandexMapsHelper::getCityByName(Api::getParam('city', Yii::t('application', 'Минск')));
                $criteria->addColumnCondition(array('e.cityId' => $city->cityId));
                //$criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
                $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
                $criteria->addCondition('e.isPublic OR e.eventId IN (SELECT eui.eventId FROM '.EventUserInvite::model()->tableName().' eui WHERE eui.userId = :currentUserId)');
                $startDate = mktime(0, 0, 0, Api::getParam('month', (int)date('n')), 1, Api::getParam('year', (int)date('Y')));
                $endDate = mktime(0, 0, 0, Api::getParam('month', (int)date('n')), (int)date('t', $startDate), Api::getParam('year', (int)date('Y')));
                $criteria->addBetweenCondition('dateStart', $startDate, $endDate);
                break;

            case EventHelper::GET_EVENTS_MODE_MAP:
                $city = YandexMapsHelper::getCityByName(Api::getParam('city', Yii::t('application', 'Минск')));
                $criteria->addColumnCondition(array('e.cityId' => $city->cityId));
                $criteria->addCondition('e.isPublic OR e.eventId IN (SELECT eui.eventId FROM '.EventUserInvite::model()->tableName().' eui WHERE eui.userId = :currentUserId)');
                //$criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
                $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
                $criteria->addCondition('e.latitude IS NOT NULL AND e.longitude IS NOT NULL');
                $criteria->addCondition('e.dateStart >= '.strtotime('-1 day'));
                $schema = 'map';
                break;
        }

        $criteria->params[':currentUserId'] = $currentUser->userId;

        $events = Event::model()->findAll($criteria);

        $response = array();
        $response['events'] = array();

        foreach ($events as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item, $schema);
            $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
            $response['events'][] = $data;
        }

        if ($mode == EventHelper::GET_EVENTS_MODE_MINE) {
            unset($criteria->params[':currentUserId']);
        }

        $response['total'] = (int)Event::model()->count($criteria);
        Api::respondSuccess($response);
    }

    public function actionGetEvent()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->with = array('cityObject', 'subscribersCount', 'commentsCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
        $criteria->alias = 'e';
        $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE eventId = e.eventId AND userId <> :currentUserId AND userId IN (SELECT friendId FROM '.UserFriend::model()->tableName().' WHERE userId = :currentUserId)) AS subscribersFriendsCount';
        $criteria->addColumnCondition(array('e.eventId' => Api::getParam('eventId')));
        $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');

        $criteria->params[':currentUserId'] = $currentUser->userId;

        $event = Event::model()->find($criteria);
        /* @var $event Event */

        $response = array();
        $response['event'] = EventHelper::export($event, 'detail, mine, map');
        $response['event']['image'] = CommonHelper::getImageLink($event->image, Api::getParam('eventImage'));
        $response['event']['gallery']['albums'] = EventGalleryHelper::getGallery($event, Api::getParam('galleryImage'), Api::getParam('albumImage'));
        $response['event']['comments'] = EventCommentHelper::getComments($event, 0, 5, Api::getParam('commentImage'));
        $response['event']['commentsCount'] = (int)$event->commentsCount;
        Api::respondSuccess($response);
    }

    public function actionGetEventComments()
    {
        $event = Event::model()->with('commentsCount')->findByPk(Api::getParam('eventId'));
        /* @var $event Event */
        $response = array();
        $response['comments'] = EventCommentHelper::getComments($event, Api::getParam('offset', 0), Api::getParam('limit', 50), Api::getParam('image'));
        $response['total'] = (int)$event->commentsCount;
        Api::respondSuccess($response);
    }

    public function actionAddEventComment()
    {
        $currentUser = $this->getUser();
        $eventId = Api::getParam('eventId');
        $event = Event::model()->findByPk($eventId);
        /* @var $event Event */

        $comment = new EventComment('api_insert');
        $comment->eventId = $eventId;
        $comment->userId = $currentUser->userId;
        $comment->content = Api::getParam('content');
        $comment->dateCreated = time();

        if (!$comment->save()) {
            Api::respondValidationError($comment);
        }

        $subscribers = EventUser::model()->findAllByAttributes(array('eventId' => $eventId));
        /* @var $subscribers EventUser */

        foreach ($subscribers as $user) {
            if ($currentUser->userId == $user->userId || $event->userId == $user->userId) {
                continue;
            }
            UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_NEW_COMMENT, $user->userId, array('eventId' => $eventId));
        }

        if ($event->userId) {
            UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_MY_EVENT_NEW_COMMENT, $event->userId, array('eventId' => $eventId));
        }

        $response = array();
        $response['comment'] = EventCommentHelper::export($comment, Api::getParam('image'));
        Api::respondSuccess($response);
    }

    public function actionGetEventSubscribers()
    {
        $currentUser = $this->getUser();

        $response = array();
        $response['users'] = array();

        $friendsOnly = Api::getParam('friendsOnly');

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';

        $criteria->join = 'INNER JOIN '.(EventUser::model()->tableName()).' eu ON (u.userId = eu.userId ANd eu.eventId = :eventId)';

        if ($friendsOnly) {
            $criteria->addCondition('u.userId IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');
        } else {
            $criteria->select .= ', (SELECT COUNT(*) FROM '.UserFriend::model()->tableName().' uf WHERE u.userId = uf.userId AND uf.friendId = :currentUserId) AS isFriend';
        }

        $criteria->params[':currentUserId'] = $currentUser->userId;
        $criteria->params[':eventId'] = Api::getParam('eventId');

        $criteria->offset = Api::getParam('offset', 0);
        $criteria->limit = Api::getParam('limit', 50);
        $criteria->order = 'u.name ASC';

        $users = User::model()->findAll($criteria);

        foreach ($users as $item) {
            /* @var $item User */
            $data = UserHelper::export($item, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
            $response['users'][] = $data;
        }

        if (!$friendsOnly) {
            unset($criteria->params[':currentUserId']);
        }

        $response['total'] = (int)User::model()->count($criteria);
        Api::respondSuccess($response);
    }

    public function actionSubscribeToEvent()
    {
        $currentUser = $this->getUser();
        $eventId = Api::getParam('eventId');
        $event = Event::model()->findByPk($eventId);
        /* @var $event Event */

        if (!EventUser::model()->countByAttributes(array('eventId' => $eventId, 'userId' => $currentUser->userId))) {
            $eventUser = new EventUser();
            $eventUser->eventId = $eventId;
            $eventUser->userId = $currentUser->userId;
            $eventUser->dateCreated = time();

            if (!$eventUser->save()) {
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            $eventsSubscribed = EventUser::model()->countByAttributes(array('userId' => $currentUser->userId));

            if ($eventsSubscribed % 10 == 0)
            {
                //Yii::log("User #{$currentUser->userId} Events Count: $eventsSubscribed", CLogger::LEVEL_WARNING);
                PointHelper::addPoints(Point::KEY_TEN_EVENTS_SUBSCRIBED, $currentUser->userId, array('eventId' => $eventId));
            }
            
            foreach ($currentUser->friends as $user) {
                if ($event->userId == $user->userId) {
                    continue;
                }
                UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_FRIEND_SUBSCRIBED, $user->userId, array('eventId' => $eventId, 'userId' => $currentUser->userId));
            }

            if ($event->userId) {
                UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_MY_EVENT_NEW_SUBSCRIBER, $event->userId, array('eventId' => $eventId, 'userId' => $currentUser->userId));
                $totalSubscribers = EventUser::model()->countByAttributes(array('eventId' => $eventId));
                if ($totalSubscribers >= Yii::app()->params['eventSubscribersCountToPoints']) {
                    PointHelper::addPoints(Point::KEY_EVENT_CREATE, $event->userId, array('eventId' => $eventId));
                }
            }
        }

        Api::respondSuccess();
    }

    public function actionUnsubscribeFromEvent()
    {
        $currentUser = $this->getUser();
        $event = Event::model()->findByPk(Api::getParam('eventId'));
        /* @var $event Event */

        if ($event->userId != $currentUser->userId) {
            EventUser::model()->deleteAllByAttributes(array('eventId' => $event->eventId, 'userId' => $currentUser->userId));
        }

        Api::respondSuccess();
    }

    public function actionAddEvent()
    {
        $currentUser = $this->getUser();

        $event = new Event('api_insert');
        $event->attributes = Api::getParams(array('name', 'dateStart', 'timeStart', 'timeEnd', 'city', 'street', 'houseNumber', 'category', 'isPublic', 'description'));
        $event->userId = $currentUser->userId;
        $event->publisherName = $currentUser->name;

        $transaction = Yii::app()->db->beginTransaction();

        if (!$event->save()) {
            $transaction->rollback();
            Api::respondValidationError($event);
        }

        if (!EventHelper::createDefaultSubscription($event, $currentUser->userId)) {
            $transaction->rollback();
            throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
        }

        if (!EventGalleryHelper::createDefaultAlbum($event)) {
            $transaction->rollback();
            throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
        }

        $transaction->commit();

        $response = array();
        $response['event'] = EventHelper::export($event, 'detail, mine, map');
        $response['event']['image'] = CommonHelper::getImageLink($event->image, Api::getParam('eventImage'));
        $response['event']['gallery']['albums'] = EventGalleryHelper::getGallery($event, Api::getParam('galleryImage'), Api::getParam('albumImage'));
        $response['event']['comments'] = array();
        $response['event']['commentsCount'] = 0;
        Api::respondSuccess($response);
    }

    public function actionUpdateEvent()
    {
        $currentUser = $this->getUser();
        $event = Event::model()->with(array('subscribersCount', 'commentsCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId))))->findByPk(Api::getParam('eventId'));
        /* @var $event Event */

        if ($event->status == Event::STATUS_WAITING) {
            throw new ApiException(Api::CODE_BAD_REQUEST, 'Dont update event with status \''.Event::STATUS_WAITING.'\'');
        }

        $event->setScenario('api_update');
        $event->attributes = Api::getParams(array('name', 'dateStart', 'timeStart', 'timeEnd', 'city', 'street', 'houseNumber', 'category', 'isPublic', 'description'));

        if (!$event->save()) {
            Api::respondValidationError($event);
        }

        $event->refresh();

        $response = array();
        $response['event'] = EventHelper::export($event, 'detail, mine, map');
        $response['event']['image'] = CommonHelper::getImageLink($event->image, Api::getParam('eventImage'));
        $response['event']['gallery']['albums'] = EventGalleryHelper::getGallery($event, Api::getParam('galleryImage'), Api::getParam('albumImage'));
        $response['event']['comments'] = EventCommentHelper::getComments($event, 0, 5, Api::getParam('commentImage'));
        $response['event']['commentsCount'] = (int)$event->commentsCount;
        Api::respondSuccess($response);
    }

    public function actionDeleteEvent()
    {
        $event = Event::model()->findByPk(Api::getParam('eventId'));
        $event->delete();
        Api::respondSuccess();
    }

    public function actionSaveEventImage()
    {
        $currentUser = $this->getUser();

        $event = Event::model()->with(array('subscribersCount', 'commentsCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId))))->findByPk(Api::getParam('eventId'));
        /* @var $event Event */
        $event->setScenario('api_update_image');
        $event->imageFile = CUploadedFile::getInstanceByName('imageFile');
        if (!$event->save()) {
            Api::respondValidationError($event);
        }

        $event->refresh();

        $response = array();
        $response['event'] = EventHelper::export($event, 'detail, mine, map');
        $response['event']['image'] = CommonHelper::getImageLink($event->image, Api::getParam('eventImage'));
        $response['event']['gallery']['albums'] = EventGalleryHelper::getGallery($event, Api::getParam('galleryImage'), Api::getParam('albumImage'));
        $response['event']['comments'] = EventCommentHelper::getComments($event, 0, 5, Api::getParam('commentImage'));
        $response['event']['commentsCount'] = (int)$event->commentsCount;
        Api::respondSuccess($response);
    }

    public function actionGetEventInvites()
    {
        $currentUser = $this->getUser();

        $response = array();
        $response['users'] = array();

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';

        $criteria->addNotInCondition('u.userId', array($currentUser->userId));
        $criteria->addCondition('u.userId IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');

        $criteria->params[':currentUserId'] = $currentUser->userId;
        $criteria->order = 'u.name ASC';

        $friends = User::model()->findAll($criteria);

        $criteria2 = new CDbCriteria();
        $criteria2->index = 'userId';
        $criteria2->addColumnCondition(array('eventId' => Api::getParam('eventId')));
        $eventUserInvites = EventUserInvite::model()->findAll($criteria2);

        foreach ($friends as $item) {
            /* @var $item User */
            $data = UserHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
            $data['isInvited'] = array_key_exists($item->userId, $eventUserInvites);
            $response['users'][] = $data;
        }

        Api::respondSuccess($response);
    }

    public function actionSaveEventInvites()
    {
        $currentUser = $this->getUser();
        $eventId = Api::getParam('eventId');
        $userIds = Api::getParam('userId', array());
        $response = array();
        $response['users'] = array();

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';
        $criteria->index = 'userId';

        $criteria->addNotInCondition('u.userId', array($currentUser->userId));
        $criteria->addCondition('u.userId IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');

        $criteria->params[':currentUserId'] = $currentUser->userId;
        $criteria->order = 'u.name ASC';

        $friends = User::model()->findAll($criteria);

        EventUserInvite::model()->deleteAllByAttributes(array('eventId' => $eventId));

        foreach ($userIds as $userId) {
            if (array_key_exists($userId, $friends)) {
                $userInvite = new EventUserInvite();
                $userInvite->eventId = $eventId;
                $userInvite->userId = $userId;
                UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_INVITE, $userId, array('eventId' => $eventId, 'userId' => $currentUser->userId));
                $userInvite->save();
            }
        }

        foreach ($friends as $item) {
            /* @var $item User */
            $data = UserHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
            $data['isInvited'] = in_array($item->userId, $userIds);
            $response['users'][] = $data;
        }

        Api::respondSuccess($response);
    }

    public function actionAddEventAlbum()
    {
        $album = new EventGalleryAlbum('api_insert');
        $album->eventId = Api::getParam('eventId');
        $album->name = Api::getParam('name');
        $album->isDefault = 0;
        if (!$album->save()) {
            Api::respondValidationError($album);
        }

        $response = array();
        $response['album'] = EventGalleryHelper::exportAlbum($album, Api::getParam('image'), Api::getParam('albumImage'));
        Api::respondSuccess($response);
    }

    public function actionRenameEventAlbum()
    {
        $album = EventGalleryAlbum::model()->findByPk(Api::getParam('albumId'));
        $album->setScenario('api_update');
        $album->name = Api::getParam('name');
        if (!$album->save()) {
            Api::respondValidationError($album);
        }

        $response = array();
        $response['album'] = EventGalleryHelper::exportAlbum($album, Api::getParam('image'), Api::getParam('albumImage'));
        Api::respondSuccess($response);
    }

    public function actionDeleteEventAlbum()
    {
        $album = EventGalleryAlbum::model()->findByPk(Api::getParam('albumId'));
        /* @var $album EventGalleryAlbum */
        if ($album->isDefault) {
            throw new ApiException(Api::CODE_BAD_REQUEST, 'Нельзя удалять главный альбом');
        }

        $album->delete();
        Api::respondSuccess();
    }

    public function actionGetEventPhotos()
    {
        $event = Event::model()->findByPk(Api::getParam('eventId'));
        /* @var $event Event */

        $response = array();
        $response['event']['gallery']['albums'] = EventGalleryHelper::getGallery($event, Api::getParam('image'), Api::getParam('albumImage'), Api::getParam('albumId'));
        Api::respondSuccess($response);
    }

    public function actionAddEventPhoto()
    {
        $currentUser = $this->getUser();
        $eventId = Api::getParam('eventId');
        $defaultAlbum = EventGalleryAlbum::model()->findByAttributes(array('eventId' => $eventId, 'isDefault' => 1));
        /* @var $defaultAlbum EventGalleryAlbum */

        $albumId = Api::getParam('albumId');

        if (!(int)$albumId) {
            $albumId = $defaultAlbum->eventGalleryAlbumId;
        }

        $photo = new EventGalleryImage('api_insert');
        $photo->eventId = $eventId;
        $photo->eventGalleryAlbumId = $albumId;
        $photo->imageFile = CUploadedFile::getInstanceByName('imageFile');

        if (!$photo->save()) {
            Api::respondValidationError($photo);
        }

        $subscribers = EventUser::model()->findAllByAttributes(array('eventId' => $eventId));
        /* @var $subscribers EventUser */

        foreach ($subscribers as $user) {
            if ($currentUser->userId == $user->userId) {
                continue;
            }
            UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED, $user->userId, array('eventId' => $eventId));
        }

        $response = array();
        $response['image'] = EventGalleryHelper::exportImage($photo, Api::getParam('image'));
        Api::respondSuccess($response);
    }

    public function actionDeleteEventPhoto()
    {
        $photo = EventGalleryImage::model()->findByPk(Api::getParam('imageId'));
        $photo->delete();
        Api::respondSuccess();
    }

    public function actionAddEventPhotoToAlbum()
    {
        $photo = EventGalleryImage::model()->findByPk(Api::getParam('imageId'));
        /* @var $photo EventGalleryImage */
        $photo->eventGalleryAlbumId = Api::getParam('albumId');
        $photo->save();
        Api::respondSuccess();
    }

    public function actionDeleteEventPhotoFromAlbum()
    {
        $photo = EventGalleryImage::model()->findByPk(Api::getParam('imageId'));
        /* @var $photo EventGalleryImage */
        $defaultAlbum = EventGalleryAlbum::model()->findByAttributes(array('eventId' => $photo->eventId, 'isDefault' => 1));
        /* @var $defaultAlbum EventGalleryAlbum */
        $photo->eventGalleryAlbumId = $defaultAlbum->eventGalleryAlbumId;
        $photo->save();
        Api::respondSuccess();
    }

    public function actionGetComingEvents()
    {
        $currentUser = $this->getUser();

        $userId = Api::getParam('userId', $currentUser->userId);

        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $userId)));
        $criteria->alias = 'e';
        $criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
        $criteria->addCondition('e.eventId IN (SELECT eu.eventId FROM '.EventUser::model()->tableName().' eu WHERE eu.userId = :selectedUser)');
        $criteria->addCondition('e.dateStart >= '.time());

        $criteria->order = 'e.dateStart ASC';

        $criteria->offset = Api::getParam('offset', 0);
        $criteria->limit = Api::getParam('limit', 50);


        $criteria->params[':selectedUser'] = $userId;

        $rows = Event::model()->findAll($criteria);

        $response = array();
        $response['events'] = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
            $response['events'][] = $data;
        }

        $response['total'] = Event::model()->count($criteria);

        Api::respondSuccess($response);
    }

    public function actionGetPastEvents()
    {
        $currentUser = $this->getUser();

        $userId = Api::getParam('userId', $currentUser->userId);

        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $userId)));
        $criteria->alias = 'e';
        $criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
        $criteria->addCondition('e.eventId IN (SELECT eu.eventId FROM '.EventUser::model()->tableName().' eu WHERE eu.userId = :selectedUser)');
        $criteria->addCondition('e.dateStart < '.time());

        $criteria->order = 'e.dateStart ASC';

        $criteria->offset = Api::getParam('offset', 0);
        $criteria->limit = Api::getParam('limit', 50);


        $criteria->params[':selectedUser'] = $userId;

        $rows = Event::model()->findAll($criteria);

        $response = array();
        $response['events'] = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
            $response['events'][] = $data;
        }

        $response['total'] = Event::model()->count($criteria);

        Api::respondSuccess($response);
    }
}