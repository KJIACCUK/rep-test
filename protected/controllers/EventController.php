<?php

class EventController extends WebController
{
    public $eventsLimit = 20;
    public $myEventsLimit = 20;
    public $commentsShortLimit = 5;
    public $commentsLimit = 20;
    public $subscribersLimit = 20;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            array('ProfileIsFilledFilter - share'),
            array(
                'ParamFilter + index, calendar, map',
                'param' => 'city',
                'function' => 'FilterHelper::checkCity'
            ),
            array(
                'ExistsFilter + detail, share, subscribe, unsubscribe, subscribers, invites, edit, saveImage, delete,'
                .'comments, addComment, gallery, getAlbum, addAlbum, addImage',
                'param' => 'eventId',
                'function' => 'FilterHelper::checkEventExists',
                'errorMessage' => Yii::t('application', 'Мероприятие не найдено')
            ),
            array(
                'ExistsFilter + getAlbum, renameAlbum, deleteAlbum, addImage',
                'param' => 'albumId',
                'function' => 'FilterHelper::checkEventAlbumExists',
                'errorMessage' => Yii::t('application', 'Альбом не найден')
            ),
            array(
                'ExistsFilter + deleteImage, downloadImage',
                'param' => 'imageId',
                'function' => 'FilterHelper::checkEventImageExists',
                'errorMessage' => Yii::t('application', 'Фото не найдено')
            ),
            array(
                'ExistsFilter + invites, edit, saveImage, delete, addAlbum, addImage',
                'param' => 'eventId',
                'function' => 'FilterHelper::checkEventIsOwner',
                'errorMessage' => Yii::t('application', 'Мероприятие не найдено')
            ),
            array(
                'ExistsFilter + renameAlbum, deleteAlbum, addImage',
                'param' => 'albumId',
                'function' => 'FilterHelper::checkEventAlbumIsOwner',
                'errorMessage' => Yii::t('application', 'Альбом не найден')
            ),
            array(
                'ExistsFilter + deleteImage',
                'param' => 'imageId',
                'function' => 'FilterHelper::checkEventImageIsOwner',
                'errorMessage' => Yii::t('application', 'Фото не найдено')
            ),
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
                'actions' => array('index', 'myEvents', 'detail', 'subscribe', 'unsubscribe', 'subscribers', 'invites', 'add', 'edit', 'saveImage', 'delete', 'calendar', 'map', 'comments', 'addComment', 'gallery', 'getAlbum', 'addAlbum', 'renameAlbum', 'deleteAlbum', 'addImage', 'deleteImage', 'downloadImage', 'addImageToAlbum', 'removeImageFromAlbum', 'comingEvents', 'pastEvents'),
                'roles' => array('user'),
            ),
            array('allow',
                'actions' => array('share'),
                'users' => array('*'),
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
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
        $criteria->alias = 'e';
        $city = YandexMapsHelper::getCityByName(Web::getParam('city', Yii::t('application', 'Минск')));
        $criteria->addColumnCondition(array('e.cityId' => $city->cityId));
        //$criteria->addInCondition('e.status', array(Event::STATUS_APPROVED));
        $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
        $criteria->addCondition('e.userId = :currentUserId OR e.isPublic OR e.eventId IN (SELECT eui.eventId FROM '.EventUserInvite::model()->tableName().' eui WHERE eui.userId = :currentUserId)');
        $criteria->addCondition('e.dateStart >= '.time());
        $order = Web::getParam('order', EventHelper::GET_EVENTS_ORDER_DATE_START);
        if ($order == EventHelper::GET_EVENTS_ORDER_SUBSCRIBERS_COUNT) {
            $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE eventId = e.eventId) AS subscribersCount';
            $criteria->order = $order.' DESC, e.dateCreated DESC';
        } elseif ($order == EventHelper::GET_EVENTS_ORDER_DATE_START) {
            $criteria->order = 'e.'.$order.' ASC';
        } else {
            $criteria->order = 'e.'.EventHelper::GET_EVENTS_ORDER_DATE_CREATED.' DESC';
        }
        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->eventsLimit);


        $criteria->params[':currentUserId'] = $currentUser->userId;

        $rows = Event::model()->findAll($criteria);

        $events = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, '200x150');
            $events[] = $data;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_events_items', array('events' => $events));
        } else {
            $this->render('index', array('events' => $events, 'order' => $order, 'city' => $city));
        }
    }

    public function actionMyEvents()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
        $criteria->addColumnCondition(array('userId' => $currentUser->userId));
        $criteria->order = 'dateCreated DESC';
        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->myEventsLimit);

        $rows = Event::model()->findAll($criteria);

        $events = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item, 'mine');
            $data['image'] = CommonHelper::getImageLink($item->image, '200x150');
            $events[] = $data;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_my_events_items', array('events' => $events));
        } else {
            $this->render('my_events', array('events' => $events));
        }
    }

    public function actionDetail()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'commentsCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
        $criteria->alias = 'e';
        $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE eventId = e.eventId AND userId <> :currentUserId AND userId IN (SELECT friendId FROM '.UserFriend::model()->tableName().' WHERE userId = :currentUserId)) AS subscribersFriendsCount';
        $criteria->addColumnCondition(array('e.eventId' => Web::getParam('eventId')));
        $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
        $criteria->with = array('cityObject');

        $criteria->params[':currentUserId'] = $currentUser->userId;

        $row = Event::model()->find($criteria);
        /* @var $row Event */

        $event = array();
        $event = EventHelper::export($row, 'detail, mine, map');
        $event['image'] = CommonHelper::getImageLink($row->image, '740x448');
        $event['gallery']['albums'] = EventGalleryHelper::getGallery($row, '180x135', '250x166');
        $event['gallery']['countPhotos'] = EventGalleryHelper::countPhotos($row->eventId);
        $event['comments'] = EventCommentHelper::getComments($row, 0, 5, '82x80');
        $event['commentsCount'] = (int)$row->commentsCount;

        $this->render('detail', array('event' => $event));
    }

    public function actionShare()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'commentsCount');
        //$criteria->addColumnCondition(array('eventId' => Web::getParam('eventId'), 'isPublic' => 1));
        $criteria->addCondition('status = "'.Event::STATUS_APPROVED.'" OR (status = "'.Event::STATUS_WAITING.'" AND userId = "'.$currentUser->userId.'")');
        $criteria->with = array('cityObject');

        $row = Event::model()->find($criteria);
        /* @var $row Event */

        if (!$row) {
            throw new CHttpException(404, Yii::t('application', 'Мероприятие не найдено'));
        }

        $event = EventHelper::export($row, 'detail, mine, map');
        $event['image'] = CommonHelper::getImageLink($row->image, '740x448');
        $event['hasImage'] = ($row->image != EventHelper::getDefaultImage());
        $event['gallery']['albums'] = EventGalleryHelper::getGallery($row, '180x135', '250x166');
        $event['gallery']['countPhotos'] = EventGalleryHelper::countPhotos($row->eventId);

        $this->render('share', array('event' => $event));
    }

    public function actionSubscribe()
    {
        $currentUser = $this->getUser();
        $eventId = Web::getParam('eventId');
        $event = Event::model()->findByPk($eventId);
        /* @var $event Event */

        if (!EventUser::model()->countByAttributes(array('eventId' => $eventId, 'userId' => $currentUser->userId))) {
            $eventUser = new EventUser();
            $eventUser->eventId = $eventId;
            $eventUser->userId = $currentUser->userId;
            $eventUser->dateCreated = time();

            if (!$eventUser->save()) {
                Web::jsonError(Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
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
        Web::jsonSuccess();
    }

    public function actionUnsubscribe()
    {
        $currentUser = $this->getUser();
        $event = Event::model()->findByPk(Web::getParam('eventId'));
        /* @var $event Event */

        if ($event->userId != $currentUser->userId) {
            EventUser::model()->deleteAllByAttributes(array('eventId' => $event->eventId, 'userId' => $currentUser->userId));
        }

        Web::jsonSuccess();
    }

    public function actionSubscribers()
    {
        $currentUser = $this->getUser();
        $event = Event::model()->findByPk(Web::getParam('eventId'));
        /* @var $event Event */
        $users = array();

        $friendsOnly = Web::getParam('friends');

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';

        $criteria->join = 'INNER JOIN '.(EventUser::model()->tableName()).' eu ON (u.userId = eu.userId ANd eu.eventId = :eventId)';

        if ($friendsOnly) {
            $criteria->addCondition('u.userId IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');
        } else {
            $criteria->select .= ', (SELECT COUNT(*) FROM '.UserFriend::model()->tableName().' uf WHERE u.userId = uf.userId AND uf.friendId = :currentUserId) AS isFriend';
        }

        $criteria->params[':currentUserId'] = $currentUser->userId;
        $criteria->params[':eventId'] = Web::getParam('eventId');

        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->subscribersLimit);
        $criteria->order = 'u.name ASC';

        $rows = User::model()->findAll($criteria);

        foreach ($rows as $item) {
            /* @var $item User */
            $data = UserHelper::export($item, 'online, friends');
            $data['image'] = CommonHelper::getImageLink($item->image, '80x82');
            $users[] = $data;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_subscribers_items', array('users' => $users));
        } else {
            $this->render('subscribers', array('users' => $users, 'event' => $event));
        }
    }

    public function actionInvites()
    {
        $currentUser = $this->getUser();
        $event = Event::model()->findByPk(Web::getParam('eventId'));
        /* @var $event Event */
        $users = array();
        $invited = array();

        $criteria = new CDbCriteria();
        $criteria->alias = 'u';
        $criteria->index = 'userId';

        $criteria->addNotInCondition('u.userId', array($currentUser->userId));
        $criteria->addCondition('u.userId IN (SELECT u1.friendId FROM '.(UserFriend::model()->tableName()).' u1 WHERE u1.userId = :currentUserId)');

        $criteria->params[':currentUserId'] = $currentUser->userId;
        $criteria->order = 'u.name ASC';

        $friends = User::model()->findAll($criteria);

        if (Web::getParam('save')) {
            $userIds = Web::getParam('userIds', array());

            EventUserInvite::model()->deleteAllByAttributes(array('eventId' => $event->eventId));

            foreach ($userIds as $userId) {
                if (array_key_exists($userId, $friends)) {
                    $userInvite = new EventUserInvite();
                    $userInvite->eventId = $event->eventId;
                    $userInvite->userId = $userId;
                    $userInvite->save();

                    UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_INVITE, $userId, array('eventId' => $event->eventId, 'userId' => $currentUser->userId));

                    $invited[] = $userId;
                }
            }

            Web::flashSuccess(Yii::t('application', 'Приглашения на мероприятия обновлены'));
            $this->refresh();
        } else {
            $criteria2 = new CDbCriteria();
            $criteria2->index = 'userId';
            $criteria2->addColumnCondition(array('eventId' => Web::getParam('eventId')));
            $eventUserInvites = EventUserInvite::model()->findAll($criteria2);
            $invited = array_keys($eventUserInvites);
        }

        foreach ($friends as $item) {
            /* @var $item User */
            $data = UserHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, '82x80');
            $data['isInvited'] = in_array($item->userId, $invited);
            $users[] = $data;
        }

        $this->render('invites', array('users' => $users, 'event' => $event));
    }

    public function actionAdd()
    {
        $currentUser = $this->getUser();
        $model = new Event('insert');
        $model->dateStartDay = date('d');
        $model->dateStartMonth = date('n');
        $model->dateStartYear = date('Y');
        $model->timeStartHours = date('H', strtotime('+1 hours'));
        $model->timeStartMinutes = date('i');
        $model->timeEndHours = '';
        $model->timeEndMinutes = '';
        $model->city = Yii::t('application', 'Минск');
        $model->eventAccess = Event::EVENT_ACCESS_ALL;

        if (isset($_POST['Event'])) {
            $model->attributes = $_POST['Event'];
            $model->userId = $currentUser->userId;
            $model->publisherName = $currentUser->name;

            $transaction = Yii::app()->db->beginTransaction();

            if ($model->save()) {
                if (!EventHelper::createDefaultSubscription($model, $currentUser->userId)) {
                    $transaction->rollback();
                    throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                }

                if (!EventGalleryHelper::createDefaultAlbum($model)) {
                    $transaction->rollback();
                    throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                }

                $transaction->commit();

                Web::flashSuccess(Yii::t('application', 'Мероприятие создано.'));
                $this->redirect(array('event/detail', 'eventId' => $model->eventId));
            }
        }

        $this->render('add', array('model' => $model));
    }

    public function actionEdit()
    {
        $model = Event::model()->with('cityObject')->findByPk(Web::getParam('eventId'));
        /* @var $model Event */

        if ($model->status == Event::STATUS_WAITING) {
            Web::flashError(Yii::t('application', 'Нельзя редактировать мероприятие, пока оно не прошло модерацию.'));
            $this->redirect(array('event/detail', 'eventId' => $model->eventId));
        }

        $model->setScenario('update');
        $model->dateStartDay = date('d', $model->dateStart);
        $model->dateStartMonth = date('n', $model->dateStart);
        $model->dateStartYear = date('Y', $model->dateStart);
        $timeStart = explode(':', $model->timeStart);
        $model->timeStartHours = $timeStart[0];
        $model->timeStartMinutes = $timeStart[1];
        $timeEnd = $model->timeEnd?explode(':', $model->timeEnd):false;
        $model->timeEndHours = $timeEnd?$timeEnd[0]:'';
        $model->timeEndMinutes = $timeEnd?$timeEnd[1]:'';
        $model->city = $model->cityObject->name;
        $model->eventAccess = $model->isPublic?Event::EVENT_ACCESS_ALL:Event::EVENT_ACCESS_FRIENDS;

        if (isset($_POST['Event'])) {
            $model->attributes = $_POST['Event'];
            if ($model->save()) {
                Web::flashSuccess(Yii::t('application', 'Мероприятие сохранено.'));
                $this->redirect(array('event/detail', 'eventId' => $model->eventId));
            }
        }

        $this->render('edit', array('model' => $model));
    }

    public function actionSaveImage()
    {
        $event = Event::model()->findByPk(Web::getParam('eventId'));

        $event->setScenario('update_image');
        $event->imageFile = CUploadedFile::getInstance($event, 'imageFile');
        $event->imageFileCropper = Web::getParam('cropper');

        if ($event->save()) {
            Web::jsonSuccess(array('image' => CommonHelper::getImageLink($event->image, '740x448')));
        }

        Web::jsonError($event->getError('imageFile'));
    }

    public function actionDelete()
    {
        $event = Event::model()->findByPk(Web::getParam('eventId'));
        $event->delete();
        Web::flashSuccess('Мероприятие удалено');
        $this->redirect(array('event/myEvents'));
    }

    public function actionComments()
    {
        $row = Event::model()->with('commentsCount')->findByPk(Web::getParam('eventId'));
        /* @var $row Event */

        if (Yii::app()->request->isAjaxRequest) {
            $comments = EventCommentHelper::getComments($row, Web::getParam('offset', 0), Web::getParam('limit', $this->commentsLimit), '82x80');
            $this->renderPartial('_comments_items', array('comments' => $comments));
        } else {
            $event = EventHelper::export($row, 'detail');
            $event['comments'] = EventCommentHelper::getComments($row, Web::getParam('offset', 0), Web::getParam('limit', $this->commentsLimit), '82x80');
            $event['commentsCount'] = (int)$row->commentsCount;
            $this->render('comments', array('event' => $event));
        }
    }

    public function actionAddComment()
    {
        $currentUser = $this->getUser();
        $eventId = Web::getParam('eventId');
        $event = Event::model()->findByPk($eventId);
        /* @var $event Event */

        $comment = new EventComment('insert');
        $comment->eventId = $eventId;
        $comment->userId = $currentUser->userId;
        $comment->content = Web::getParam('content');
        $comment->dateCreated = time();

        if (!$comment->save()) {
            Web::jsonError($comment->getError('content'));
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

        Web::jsonSuccess();
    }

    public function actionGallery()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount');
        $criteria->alias = 'e';
        $criteria->select .= ', (SELECT COUNT(*) FROM '.EventUser::model()->tableName().' WHERE eventId = e.eventId AND userId <> :currentUserId AND userId IN (SELECT friendId FROM '.UserFriend::model()->tableName().' WHERE userId = :currentUserId)) AS subscribersFriendsCount';
        $criteria->addColumnCondition(array('e.eventId' => Web::getParam('eventId')));
        $criteria->with = array('cityObject');

        $criteria->params[':currentUserId'] = $currentUser->userId;

        $row = Event::model()->find($criteria);
        /* @var $row Event */

        $event = EventHelper::export($row, 'detail');
        $event['gallery']['albums'] = EventGalleryHelper::getGallery($row, '180x135', '250x166');
        $event['gallery']['countPhotos'] = EventGalleryHelper::countPhotos($row->eventId);

        $albumModel = new EventGalleryAlbum();
        $defaultAlbum = EventGalleryAlbum::model()->findByAttributes(array('eventId' => $row->eventId, 'isDefault' => 1));

        $this->render('gallery', array('event' => $event, 'albumModel' => $albumModel, 'defaultAlbum' => $defaultAlbum));
    }

    public function actionGetAlbum()
    {
        $row = Event::model()->findByPk(Web::getParam('eventId'));
        /* @var $row Event */
        $row2 = EventGalleryAlbum::model()->with('images')->findByPk(Web::getParam('albumId'));
        /* @var $row EventGalleryAlbum */

        $event = EventHelper::export($row);
        $album = EventGalleryHelper::exportAlbum($row2, '180x135', '250x166');

        $this->render('gallery_album', array('album' => $album, 'event' => $event, 'albumModel' => $row2));
    }

    public function actionAddAlbum()
    {
        $event = Event::model()->findByPk(Web::getParam('eventId'));
        /* @var $event Event */

        $album = new EventGalleryAlbum('insert');
        $album->eventId = $event->eventId;
        $album->name = Web::getParam('name');
        $album->isDefault = 0;

        if ($album->save()) {
            $data = $this->renderPartial('_gallery_albums_items', array('eventId' => $event->eventId, 'albums' => EventGalleryHelper::getGallery($event, '180x135', '250x166')), true);
            Web::jsonSuccess(array('data' => $data));
        }

        Web::jsonError($album->getError('name'));
    }

    public function actionRenameAlbum()
    {
        $album = EventGalleryAlbum::model()->findByPk(Web::getParam('albumId'));
        $album->name = Web::getParam('name');

        if ($album->save()) {
            Web::jsonSuccess();
        }

        Web::jsonError($album->getError('name'));
    }

    public function actionDeleteAlbum()
    {
        $album = EventGalleryAlbum::model()->findByPk(Web::getParam('albumId'));
        /* @var $album EventGalleryAlbum */
        if ($album->isDefault) {
            throw new CHttpException(500, 'Нельзя удалять главный альбом');
        }

        $album->delete();

        Web::flashSuccess('Альбом удален');
        $this->redirect(array('event/gallery', 'eventId' => $album->eventId));
    }

    public function actionAddImage()
    {
        $currentUser = $this->getUser();
        $eventId = Web::getParam('eventId');

        $files = CUploadedFile::getInstancesByName('imageFiles');
        $saved = array();
        $errors = array();

        foreach ($files as $item) {
            $photo = new EventGalleryImage('insert');
            $photo->eventId = $eventId;
            $photo->eventGalleryAlbumId = Web::getParam('albumId');
            $photo->imageFile = $item;

            if ($photo->save()) {
                $saved[] = $photo->image;
            } else {
                $errors[] = $item->getName().' - '.$photo->getError('imageFile');
            }
        }

        if (count($saved) > 0) {
            $subscribers = EventUser::model()->findAllByAttributes(array('eventId' => $eventId));
            /* @var $subscribers EventUser */

            foreach ($subscribers as $user) {
                if ($currentUser->userId == $user->userId) {
                    continue;
                }
                UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED, $user->userId, array('eventId' => $eventId));
            }
        } else {
            $status = 'none';
        }

        $savedData = array();
        foreach ($saved as $image) {
            $savedData[] = array('previewImage' => CommonHelper::getImageLink($image, '500x281'), 'originalImage' => $image, 'image' => CommonHelper::getImageLink($image, '180x135'));
        }

        if (count($saved) == count($files)) {
            $status = 'all';
        } else {
            $status = 'partial';
        }

        Web::jsonSuccess(array('status' => $status, 'saved' => $savedData, 'savedCount' => count($savedData), 'errors' => $errors));
    }

    public function actionDeleteImage()
    {
        $photo = EventGalleryImage::model()->findByPk(Web::getParam('imageId'));
        $photo->delete();
        Web::jsonSuccess();
    }

    public function actionDownloadImage()
    {
        $photo = EventGalleryImage::model()->findByPk(Web::getParam('imageId'));
        /* @var $photo EventGalleryImage */
        Web::jsonSuccess(array('link' => Yii::app()->request->getBaseUrl(true).$photo->image));
    }

    public function actionAddImageToAlbum()
    {
        
    }

    public function actionRemoveImageFromAlbum()
    {
        
    }

    public function actionCalendar()
    {
        $currentUser = $this->getUser();
        $city = YandexMapsHelper::getCityByName(Web::getParam('city', Yii::t('application', 'Минск')));
        $year = Web::getParam('year', date('Y'));
        $month = Web::getParam('month', date('n'));

        $startDate = mktime(0, 0, 0, $month, 1, $year);
        $endDate = mktime(0, 0, 0, $month, (int)date('t', $startDate), $year);

        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
        $criteria->alias = 'e';
        $criteria->addColumnCondition(array('e.cityId' => $city->cityId));
        //$criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
        $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
        $criteria->addCondition('e.isPublic OR e.eventId IN (SELECT eui.eventId FROM '.EventUserInvite::model()->tableName().' eui WHERE eui.userId = :currentUserId)');
        $criteria->addBetweenCondition('dateStart', $startDate, $endDate);
        $criteria->params[':currentUserId'] = $currentUser->userId;

        $rows = Event::model()->findAll($criteria);

        $events = array();
        $eventCounts = array(
            'global' => array(),
            'mine' => array(),
            'subscribed' => array()
        );
        $subscribedEvents = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item);
            $day = date('Y-m-d', $item->dateStart);
            if ($data['isGlobal']) {
                if (!isset($eventCounts['global'][$day])) {
                    $eventCounts['global'][$day] = 0;
                }

                if (!isset($events[$day])) {
                    $events[$day] = array();
                }

                $eventCounts['global'][$day] ++;
                $events[$day][] = $data;
            } elseif ($data['isMine']) {
                if (!isset($eventCounts['mine'][$day])) {
                    $eventCounts['mine'][$day] = 0;
                }

                if (!isset($events[$day])) {
                    $events[$day] = array();
                }

                $eventCounts['mine'][$day] ++;
                $events[$day][] = $data;
            } elseif ($data['isSubscribe']) {
                if (!isset($eventCounts['subscribed'][$day])) {
                    $eventCounts['subscribed'][$day] = 0;
                }

                if (!isset($events[$day])) {
                    $events[$day] = array();
                }

                $eventCounts['subscribed'][$day] ++;
                $events[$day][] = $data;
            }
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_calendar_table', array('events' => $events, 'eventCounts' => $eventCounts, 'city' => $city, 'year' => $year, 'month' => $month));
        } else {
            $this->render('calendar', array('events' => $events, 'eventCounts' => $eventCounts, 'city' => $city, 'year' => $year, 'month' => $month));
        }
    }

    public function actionMap()
    {
        $currentUser = $this->getUser();
        $city = YandexMapsHelper::getCityByName(Web::getParam('city', Yii::t('application', 'Минск')));

        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $currentUser->userId)));
            $criteria->alias = 'e';
            $criteria->addColumnCondition(array('e.cityId' => $city->cityId));
            //$criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
            $criteria->addCondition('e.status = "'.Event::STATUS_APPROVED.'" OR (e.status = "'.Event::STATUS_WAITING.'" AND e.userId = "'.$currentUser->userId.'")');
            $criteria->addCondition('e.isPublic OR e.eventId IN (SELECT eui.eventId FROM '.EventUserInvite::model()->tableName().' eui WHERE eui.userId = :currentUserId)');
            $criteria->addCondition('e.latitude IS NOT NULL AND e.longitude IS NOT NULL');
            $criteria->addCondition('e.dateStart >= '.strtotime('-1 day'));
            $criteria->params[':currentUserId'] = $currentUser->userId;

            $rows = Event::model()->findAll($criteria);

            $data = YandexMapsHelper::exportEvents($rows);
            Web::jsonSuccess(array('data' => $data));
        } else {
            $this->render('map', array('city' => $city));
        }
    }

    public function actionComingEvents()
    {
        $currentUser = $this->getUser();

        $userId = Web::getParam('userId', $currentUser->userId);

        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $userId)));
        $criteria->alias = 'e';
        $criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
        $criteria->addCondition('e.eventId IN (SELECT eu.eventId FROM '.EventUser::model()->tableName().' eu WHERE eu.userId = :selectedUser)');
        $criteria->addCondition('e.dateStart >= '.time());

        $criteria->order = 'e.dateStart ASC';

        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->eventsLimit);


        $criteria->params[':selectedUser'] = $userId;

        $rows = Event::model()->findAll($criteria);

        $events = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, '200x150');
            $events[] = $data;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_subscribed_events_items', array('events' => $events));
        } else {
            $this->render('subscribed_events', array('events' => $events));
        }
    }

    public function actionPastEvents()
    {
        $currentUser = $this->getUser();

        $userId = Web::getParam('userId', $currentUser->userId);

        $criteria = new CDbCriteria();
        $criteria->with = array('subscribersCount', 'isSubscribed' => array('scopes' => array('selectedUser' => $userId)));
        $criteria->alias = 'e';
        $criteria->addNotInCondition('e.status', array(Event::STATUS_DECLINED));
        $criteria->addCondition('e.eventId IN (SELECT eu.eventId FROM '.EventUser::model()->tableName().' eu WHERE eu.userId = :selectedUser)');
        $criteria->addCondition('e.dateStart < '.time());

        $criteria->order = 'e.dateStart ASC';

        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->eventsLimit);


        $criteria->params[':selectedUser'] = $userId;

        $rows = Event::model()->findAll($criteria);

        $events = array();

        foreach ($rows as $item) {
            /* @var $item Event */
            $data = EventHelper::export($item);
            $data['image'] = CommonHelper::getImageLink($item->image, '200x150');
            $events[] = $data;
        }

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_subscribed_events_items', array('events' => $events));
        } else {
            $this->render('subscribed_events', array('events' => $events));
        }
    }
}