<?php

    class EventController extends AdminController
    {

        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl',
                'postOnly + delete'
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
                    'actions' => array('index', 'users', 'onValidation', 'fromRelax', 'view', 'push', 'create', 'update', 'delete', 'approve', 'decline', 'gallery', 'albumCreate', 'albumDelete', 'imageDelete'),
                    'roles' => array('admin', 'moderator'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $model = new Event('search_global');
            $model->unsetAttributes();  // clear any default values
            $city = new City('search');
            $city->unsetAttributes();
            if(isset($_GET['Event']))
            {
                $model->attributes = $_GET['Event'];
            }
            $model->searchCityObject = $city;

            $this->render('index', array(
                'model' => $model,
            ));
        }

        public function actionUsers()
        {
            $model = new Event('search_users');
            $model->unsetAttributes();  // clear any default values
            $city = new City('search');
            $city->unsetAttributes();
            if(isset($_GET['Event']))
            {
                $model->attributes = $_GET['Event'];
            }
            $model->searchCityObject = $city;

            $this->render('users', array(
                'model' => $model,
            ));
        }

        public function actionOnValidation()
        {
            $model = new Event('search_on_validation');
            $model->unsetAttributes();  // clear any default values
            $city = new City('search');
            $city->unsetAttributes();
            if(isset($_GET['Event']))
            {
                $model->attributes = $_GET['Event'];
            }
            $model->searchCityObject = $city;

            $this->render('on_validation', array(
                'model' => $model,
            ));
        }
        
        public function actionFromRelax()
        {
            $model = new Event('search_from_relax');
            $model->unsetAttributes();  // clear any default values
            $city = new City('search');
            $city->unsetAttributes();
            if(isset($_GET['Event']))
            {
                $model->attributes = $_GET['Event'];
            }
            $model->searchCityObject = $city;

            $this->render('from_relax', array(
                'model' => $model,
            ));
        }

        /**
         * Displays a particular model.
         * @param integer $id the ID of the model to be displayed
         */
        public function actionView($id)
        {
            $this->render('view', array(
                'model' => $this->loadModel($id),
            ));
        }

        public function actionCreate()
        {
            $model = new Event('admin_insert');
            $model->city = Yii::t('application', 'Минск');
            $model->image = EventHelper::getDefaultImage();

            if(isset($_POST['Event']))
            {
                $model->attributes = $_POST['Event'];
                $model->imageFile = CUploadedFile::getInstance($model, 'imageFile');
                if($model->save())
                {
                    EventGalleryHelper::createDefaultAlbum($model);

                    if($model->imageFile)
                    {
                        $imagePath = Yii::getPathOfAlias('webroot.content.images.events').'/'.CommonHelper::generateImageName($model->eventId).'.'.$model->imageFile->getExtensionName();
                        $imagePath = str_replace('\\', '/', $imagePath);

                        $model->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
                        $model->imageFile->saveAs($imagePath);
                        $model->save(false);
                    }
                    Web::flashSuccess(Yii::t('application', 'Мероприятие добавлено'));
                    $this->redirect(array('index'));
                }
            }

            if($model->dateStart)
            {
                $model->dateStart = date(Yii::app()->params['dateFormat'], $model->dateStart);
            }

            $this->render('create', array(
                'model' => $model,
            ));
        }

        public function actionUpdate($id)
        {
            $model = $this->loadModel($id);
            $model->city = $model->cityObject->name;
            $model->setScenario('admin_update');

            if(isset($_POST['Event']))
            {
                $model->attributes = $_POST['Event'];
                $model->imageFile = CUploadedFile::getInstance($model, 'imageFile');
                if ($model->relaxSaveAndPublish) {
                    $model->status = Event::STATUS_APPROVED;
                }
                if($model->save())
                {
                    if($model->imageFile)
                    {
                        $imagePath = Yii::getPathOfAlias('webroot.content.images.events').'/'.CommonHelper::generateImageName($model->eventId).'.'.$model->imageFile->getExtensionName();
                        $imagePath = str_replace('\\', '/', $imagePath);

                        $model->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
                        $model->imageFile->saveAs($imagePath);
                        $model->save(false);
                    }
                    if ($model->relaxSaveAndPublish) {
                        $model->relaxParsingErrors = null;
                        $model->save(false);
                    }
                    Web::flashSuccess(Yii::t('application', 'Мероприятие обновлено'));
                    $this->redirect(array('index'));
                }
            }

            if($model->dateStart)
            {
                $model->dateStart = date(Yii::app()->params['dateFormat'], $model->dateStart);
            }
            
            $model->relaxSaveAndPublish = 0;

            $this->render('update', array(
                'model' => $model,
            ));
        }

        public function actionApprove($id)
        {
            $model = $this->loadModel($id);
            if ($model->relaxId) {
                $this->redirect(array('view', 'id' => $id));
            }
            $model->status = Event::STATUS_APPROVED;
            $model->save(false);
            UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED, $model->userId, array('eventId' => $id));
            Web::flashSuccess(Yii::t('application', 'Мероприятие подтверждено'));
            $this->redirect(array('view', 'id' => $id));
        }

        public function actionDecline($id)
        {
            $model = $this->loadModel($id);
            if ($model->relaxId) {
                $this->redirect(array('view', 'id' => $id));
            }
            $model->status = Event::STATUS_DECLINED;
            $model->save(false);
            UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_MY_EVENT_STATUS_UPDATED, $model->userId, array('eventId' => $id));
            Web::flashSuccess(Yii::t('application', 'Мероприятие отклонено'));
            $this->redirect(array('view', 'id' => $id));
        }
        
        public function actionPublish($id)
        {
            $model = $this->loadModel($id);
            if (!$model->relaxId) {
                $this->redirect(array('view', 'id' => $id));
            }
            $model->status = Event::STATUS_APPROVED;
            $model->save(false);
            Web::flashSuccess(Yii::t('application', 'Мероприятие с сайта relax.by опубликовано'));
            $this->redirect(array('view', 'id' => $id));
        }

        public function actionDelete($id)
        {
            if(Yii::app()->request->isPostRequest)
            {
                $model = $this->loadModel($id);
                $model->delete();
                Web::flashSuccess(Yii::t('application', 'Мероприятие удалено'));

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                {
                    $this->redirect(array('index'));
                }
            }
            else
            {
                throw new CHttpException(400, Yii::t('application', 'Невалидный запрос'));
            }
        }

        public function actionGallery($id)
        {
            $albumId = Web::getParam('album');
            if($albumId)
            {
                $albumModel = EventGalleryAlbum::model()->findByPk($albumId);
            }
            else
            {
                $albumModel = EventGalleryAlbum::model()->findByAttributes(array('eventId' => $id, 'isDefault' => 1));
            }
            /* @var $albumModel EventGalleryAlbum */

            $albums = EventGalleryAlbum::model()->findAllByAttributes(array('eventId' => $id));

            if(isset($_POST['EventGalleryAlbum']))
            {
                $albumModel->attributes = $_POST['EventGalleryAlbum'];
                if($albumModel->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Альбом переименован'));
                    $this->refresh();
                }
            }

            $modelsWithErrors = array();
            if(isset($_POST['uploadImages']))
            {
                $files = CUploadedFile::getInstancesByName('imageFiles');
                $saved = 0;

                foreach($files as $item)
                {
                    $imageModel = new EventGalleryImage();
                    $imageModel->eventId = $id;
                    $imageModel->eventGalleryAlbumId = $albumModel->eventGalleryAlbumId;
                    $imageModel->imageFile = $item;
                    if($imageModel->save())
                    {
                        $saved++;
                    }
                    else
                    {
                        $modelsWithErrors[] = $imageModel;
                    }
                }

                if($saved > 0)
                {
                    $subscribers = EventUser::model()->findAllByAttributes(array('eventId' => $id));
                    /* @var $subscribers EventUser */

                    foreach($subscribers as $user)
                    {
                        UserNotificationsHelper::addNotification(UserNotificationSetting::SETTING_EVENT_GALLERY_UPDATED, $user->userId, array('eventId' => $id));
                    }

                    if($saved == count($files))
                    {
                        Web::flashSuccess(Yii::t('application', 'Фотографии добавлены'));
                        $this->refresh();
                    }
                    else
                    {
                        Web::flashSuccess(Yii::t('application', 'Фотографии добавлены частично'));
                    }
                }
            }
            
            $images = EventGalleryImage::model()->findAllByAttributes(array('eventId' => $id, 'eventGalleryAlbumId' => $albumModel->eventGalleryAlbumId));

            $this->render('gallery', array(
                'eventId' => $id,
                'albums' => $albums,
                'images' => $images,
                'albumModel' => $albumModel,
                'modelsWithErrors' => $modelsWithErrors
            ));
        }

        public function actionAlbumCreate($id)
        {
            $albums = EventGalleryAlbum::model()->findAllByAttributes(array('eventId' => $id));

            $albumModel = new EventGalleryAlbum();

            if(isset($_POST['EventGalleryAlbum']))
            {
                $albumModel->attributes = $_POST['EventGalleryAlbum'];
                $albumModel->eventId = $id;
                $albumModel->isDefault = false;

                if($albumModel->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Альбом добавлен'));
                    $this->redirect(array('gallery', 'id' => $id, 'album' => $albumModel->eventGalleryAlbumId));
                }
            }

            $this->render('album_create', array(
                'eventId' => $id,
                'albums' => $albums,
                'albumModel' => $albumModel
            ));
        }

        public function actionAlbumDelete($id)
        {
            $model = EventGalleryAlbum::model()->findByAttributes(array('eventGalleryAlbumId' => $id, 'isDefault' => 0));
            /* @var $model EventGalleryAlbum */
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Альбом не найден'));
            }

            $model->delete();
            Web::flashSuccess(Yii::t('application', 'Альбом удален'));
            $this->redirect(array('gallery', 'id' => $model->eventId));
        }

        public function actionImageDelete()
        {
            $model = EventGalleryImage::model()->with('album')->findByPk(Web::getParam('id'));
            /* @var $model EventGalleryImage */
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Фотография не найдена'));
            }

            $model->delete();
            Web::flashSuccess(Yii::t('application', 'Фотография удалена'));
            if($model->album->isDefault)
            {
                $this->redirect(array('gallery', 'id' => $model->eventId));
            }
            else
            {
                $this->redirect(array('gallery', 'id' => $model->eventId, 'album' => $model->eventGalleryAlbumId));
            }
        }

        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer $id the ID of the model to be loaded
         * @return Event the loaded model
         * @throws CHttpException
         */
        public function loadModel($id)
        {
            $model = Event::model()->with(array('cityObject', 'galleryAlbums'))->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Мероприятие не найдено'));
            }
            return $model;
        }

    }
    