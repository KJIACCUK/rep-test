<?php

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property integer $eventId
 * @property integer $userId
 * @property string $category
 * @property string $publisherName
 * @property string $name
 * @property string $image
 * @property string $description
 * @property integer $cityId
 * @property string $street
 * @property string $houseNumber
 * @property double $latitude
 * @property double $longitude
 * @property integer $isPublic
 * @property integer $isGlobal
 * @property string $relaxId
 * @property string $relaxUrl
 * @property string $relaxParsingErrors
 * @property integer $productId
 * @property string $status
 * @property integer $dateStart
 * @property string $timeStart
 * @property string $timeEnd
 * @property integer $isPushed
 * @property integer $dateCreated
 *
 * The followings are the available model relations:
 * @property City $cityObject
 * @property EventComment[] $comments
 * @property EventGalleryAlbum[] $galleryAlbums
 * @property EventGalleryImage[] $galleryImages
 * @property EventUser[] $subscribers
 * @property EventUserInvite[] $invited
 * @property integer $subscribersCount
 * @property integer $invitedCount
 * @property integer $commentsCount
 * @property integer $isSubscribed
 * @property integer $isInvited
 */
class Event extends CActiveRecord
{
    const STATUS_WAITING = 'waiting';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    const EVENT_ACCESS_ALL = 'all';
    const EVENT_ACCESS_FRIENDS = 'friends';

    public $city;
    public $subscribersFriendsCount;
    public $imageFile;
    public $imageFileCropper;
    public $dateStartDay;
    public $dateStartMonth;
    public $dateStartYear;
    public $timeStartHours;
    public $timeStartMinutes;
    public $timeEndHours;
    public $timeEndMinutes;
    public $eventAccess;
    public $relaxSaveAndPublish = 0;

    /**
     *
     * @var City
     */
    public $searchCityObject;

    /**
     *
     * @var EventGalleryImage[]
     */
    public $imagesToDelete = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Event the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            // insert, update
            array('name, category, description, city, dateStart, dateStartYear, dateStartMonth, dateStartDay, timeStart, timeStartHours, timeStartMinutes', 'required', 'on' => array('insert', 'update')),
            array('name', 'length', 'max' => 255, 'on' => array('insert', 'update')),
            array('description, street', 'length', 'max' => 2000, 'on' => array('insert', 'update')),
            array('houseNumber', 'length', 'max' => 10, 'on' => array('insert', 'update')),
            array('category', 'in', 'range' => Yii::app()->params['eventCategories'], 'on' => array('insert', 'update')),
            array('eventAccess', 'in', 'range' => array_keys(EventHelper::getAccessList()), 'on' => array('insert', 'update')),
            array('dateStart', 'date', 'format' => 'dd.M.yyyy', 'on' => array('insert', 'update')),
            array('dateStart', 'validateDateStart', 'on' => array('insert', 'update')),
            array('timeStart', 'date', 'format' => 'hh:mm', 'on' => array('insert', 'update')),
            array('timeEnd', 'date', 'format' => 'hh:mm', 'allowEmpty' => true, 'on' => array('insert', 'update')),
            array('userId, publisherName, image, cityId, latitude, longitude, isGlobal, status, isPublic, dateCreated', 'safe', 'on' => 'insert'),
            array('cityId, latitude, longitude, status, isPublic', 'safe', 'on' => 'update'),
            // update_image
            array('imageFileCropper', 'required', 'on' => 'update_image'),
            array('imageFile', 'file', 'types' => 'png, jpg, jpe, jpeg', 'maxSize' => 10 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => 'update_image'),
            // api_insert
            ApiValidatorHelper::required('name, category, description, city, dateStart, timeStart', 'api_insert'),
            ApiValidatorHelper::length('name', null, 255, 'api_insert'),
            ApiValidatorHelper::length('description, street', null, 2000, 'api_insert'),
            ApiValidatorHelper::length('houseNumber', null, 10, 'api_insert'),
            ApiValidatorHelper::in('category', Yii::app()->params['eventCategories'], 'api_insert'),
            ApiValidatorHelper::type('isPublic', 'integer', 'api_insert'),
            ApiValidatorHelper::date('dateStart', 'dd.MM.yyyy', 'api_insert'),
            ApiValidatorHelper::date('timeStart', 'hh:mm', 'api_insert'),
            ApiValidatorHelper::date('timeEnd', 'hh:mm', 'api_insert') + array('allowEmpty' => true),
            array('dateStart', 'apiValidateDateStart', 'on' => 'api_insert'),
            ApiValidatorHelper::safe('userId, publisherName, image, cityId, latitude, longitude, isGlobal, status, dateCreated', 'api_insert'),
            // api_update
            ApiValidatorHelper::required('name, category, description, city, dateStart, timeStart', 'api_update'),
            ApiValidatorHelper::length('name', null, 255, 'api_update'),
            ApiValidatorHelper::length('description, street', null, 2000, 'api_update'),
            ApiValidatorHelper::length('houseNumber', null, 10, 'api_update'),
            ApiValidatorHelper::in('category', Yii::app()->params['eventCategories'], 'api_update'),
            ApiValidatorHelper::type('isPublic', 'integer', 'api_update'),
            ApiValidatorHelper::date('dateStart', 'dd.MM.yyyy', 'api_update'),
            ApiValidatorHelper::date('timeStart', 'hh:mm', 'api_update'),
            ApiValidatorHelper::date('timeEnd', 'hh:mm', 'api_update') + array('allowEmpty' => true),
            array('dateStart', 'apiValidateDateStart', 'on' => 'api_update'),
            ApiValidatorHelper::safe('cityId, latitude, longitude, status', 'api_update'),
            // api_update_image
            ApiValidatorHelper::file('imageFile', 'png, jpg, jpe, jpeg', null, 1 * 1024 * 1024, 1, 'api_update_image') + array('allowEmpty' => false),
            //admin_insert, admin_update
            array('category, publisherName, name, image, description, city, cityId, isPublic, isGlobal, status, dateStart, timeStart, dateCreated', 'required', 'on' => array('admin_insert', 'admin_update')),
            array('name, publisherName', 'length', 'max' => 255, 'on' => array('admin_insert', 'admin_update')),
            array('category', 'in', 'range' => Yii::app()->params['eventCategories'], 'on' => array('admin_insert', 'admin_update')),
            array('dateStart', 'date', 'format' => 'dd.M.yyyy', 'on' => array('admin_insert', 'admin_update')),
            array('dateStart', 'validateAdminDateStart', 'on' => array('admin_insert', 'admin_update')),
            array('timeStart', 'date', 'format' => 'hh:mm', 'on' => array('admin_insert', 'admin_update')),
            array('timeEnd', 'date', 'format' => 'hh:mm', 'allowEmpty' => true, 'on' => array('admin_insert', 'admin_update')),
            array('isPublic, isGlobal', 'numerical', 'integerOnly' => true, 'on' => array('admin_insert', 'admin_update')),
            array('latitude, longitude', 'numerical', 'on' => array('admin_insert', 'admin_update')),
            array('cityId, productId', 'length', 'max' => 11, 'on' => array('admin_insert', 'admin_update')),
            array('houseNumber', 'length', 'max' => 10, 'on' => array('admin_insert', 'admin_update')),
            array('street', 'safe', 'on' => array('admin_insert', 'admin_update')),
            array('imageFile', 'file', 'types' => 'png, jpg, jpe, jpeg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => 'admin_insert'),
            array('imageFile', 'file', 'types' => 'png, jpg, jpe, jpeg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => true, 'on' => 'admin_update'),
            array('relaxSaveAndPublish', 'safe', 'on' => 'admin_update'),
            // relax_insert
            array('category, publisherName, name, image, description, cityId, relaxId, relaxUrl, dateStart, timeStart, dateCreated', 'required', 'on' => 'relax_insert'),
            array('street, houseNumber, latitude, longitude, status, isPublic, isGlobal, relaxParsingErrors, timeEnd', 'safe', 'on' => 'relax_insert'),
            // search
            array('relaxId', 'safe', 'on' => array('search_global')),
            array('eventId, category, publisherName, name, dateStart, timeStart, dateCreated', 'safe', 'on' => array('search_global', 'search_users', 'search_on_validation', 'search_from_relax')),
        );
    }

    public function validateAdminDateStart()
    {
        $dateStart = strtotime($this->dateStart.' '.$this->timeStart);
        if ($dateStart < time()) {
            $this->addError('dateStart', Yii::t('application', 'Дата и время начала мероприятия должны быть больше текущей даты и времени'));
        }
    }

    public function validateDateStart()
    {
        $dateStart = strtotime($this->dateStartDay.'.'.$this->dateStartMonth.'.'.$this->dateStartYear.' '.$this->timeStartHours.':'.$this->timeStartMinutes);
        if ($dateStart < time()) {
            $this->addError('dateStart', Yii::t('application', 'Дата и время начала мероприятия должны быть больше текущей даты и времени'));
        }
    }

    public function apiValidateDateStart()
    {
        $dateStart = strtotime($this->dateStart.' '.$this->timeStart);
        if ($dateStart < time()) {
            $this->addError('dateStart', ValidationMessageHelper::DATE_IN_PAST);
        }
    }

    public function beforeValidate()
    {
        if (in_array($this->scenario, array('insert', 'api_insert', 'admin_insert'))) {
            $this->image = EventHelper::getDefaultImage();
            $this->dateCreated = time();
        }

        if (in_array($this->scenario, array('insert', 'api_insert', 'update', 'api_update'))) {
            $this->isGlobal = 0;
            $this->status = self::STATUS_WAITING;
        }

        if (in_array($this->scenario, array('insert', 'update'))) {
            $this->dateStart = $this->dateStartDay.'.'.$this->dateStartMonth.'.'.$this->dateStartYear;
            $this->timeStart = $this->timeStartHours.':'.$this->timeStartMinutes;
            if ($this->timeEndHours && $this->timeEndMinutes) {
                $this->timeEnd = $this->timeEndHours.':'.$this->timeEndMinutes;
            }
            $this->isPublic = ($this->eventAccess == self::EVENT_ACCESS_ALL);
        }

        if (in_array($this->scenario, array('admin_insert', 'admin_update'))) {
            $this->isPublic = 1;
            $this->isGlobal = 1;
            $this->status = self::STATUS_APPROVED;

            if ($this->city) {
                $city = YandexMapsHelper::getCityByName($this->city);
                if ($city) {
                    $this->cityId = $city->cityId;
                }
            }
        }

        return parent::beforeValidate();
    }

    public function afterValidate()
    {
        if (!in_array($this->scenario, array('update_image', 'api_update_image', 'relax_insert'))) {
            $this->dateStart = strtotime($this->dateStart.' '.$this->timeStart);
        }

        if (in_array($this->scenario, array('update_image', 'api_update_image'))) {
            if ($this->imageFile) {
                $imagePath = Yii::getPathOfAlias('webroot.content.images.events').'/'.CommonHelper::generateImageName($this->eventId).'.'.$this->imageFile->getExtensionName();
                $imagePath = str_replace('\\', '/', $imagePath);

                $this->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
            }
        }

        if (!in_array($this->scenario, array('admin_insert', 'admin_update', 'relax_insert'))) {
            if ($this->city) {
                $city = YandexMapsHelper::getCityByName($this->city);
                if ($city) {
                    $this->cityId = $city->cityId;
                    if ($this->street) {
                        if (($coordinates = YandexMapsHelper::findAddressCoordinates($city->name, $this->street, $this->houseNumber))) {
                            $this->latitude = $coordinates[0];
                            $this->longitude = $coordinates[1];
                        }
                    }
                }
            }
        }

        parent::afterValidate();
    }

    public function afterSave()
    {
        if (in_array($this->scenario, array('update_image', 'api_update_image'))) {
            if ($this->imageFile) {
                $imagePath = Yii::getPathOfAlias('webroot').$this->image;

                if ((EventHelper::getDefaultImage() != $this->image) && file_exists($imagePath)) {
                    unlink($imagePath);
                }

                $this->imageFile->saveAs($imagePath);

                if ($this->scenario == 'update_image') {
                    if (isset($this->imageFileCropper['x']) && isset($this->imageFileCropper['y']) && isset($this->imageFileCropper['width']) && isset($this->imageFileCropper['height']) && isset($this->imageFileCropper['rotate'])) {
                        $image = WideImage::load($imagePath);
                        $bgColor = $image->allocateColor(32, 39, 78);
                        if ($this->imageFileCropper['rotate'] != 0) {
                            $image = $image->rotate($this->imageFileCropper['rotate'], $bgColor);
                        }

                        $image = $image->crop($this->imageFileCropper['x'], $this->imageFileCropper['y'], $this->imageFileCropper['width'], $this->imageFileCropper['height']);

                        if ($image->getWidth() < 740) {
                            $image = $image->resizeCanvas(740, 555, 'center', 'center', $bgColor, 'up');
                        }

                        $image->saveToFile($imagePath);
                    }
                }
            }
        }

        parent::afterSave();
    }

    public function beforeDelete()
    {
        $criteria = new CDbCriteria();
        $criteria->index = 'eventGalleryImageId';
        $criteria->addColumnCondition(array('eventId' => $this->eventId));
        $this->imagesToDelete = EventGalleryImage::model()->findAll($criteria);
        return parent::beforeDelete();
    }

    public function afterDelete()
    {
        if (file_exists(Yii::getPathOfAlias('webroot').$this->image) && ($this->image != EventHelper::getDefaultImage())) {
            $eventDir = Yii::getPathOfAlias('webroot.content.images.events').'/';
            unlink(Yii::getPathOfAlias('webroot').$this->image);
            ImageHelper::cleanCacheDir($eventDir, str_replace('/content/images/events/', '', $this->image));

            foreach ($this->imagesToDelete as $item) {
                $item->delete();
            }
        }
        if ($this->relaxId) {
            $deletedRelaxId = new EventRelaxDeleted();
            $deletedRelaxId->relaxId = $this->relaxId;
            $deletedRelaxId->save();
        }
        parent::afterDelete();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'cityObject' => array(self::BELONGS_TO, 'City', 'cityId'),
            'comments' => array(self::HAS_MANY, 'EventComment', 'eventId', 'order' => 'comments.dateCreated DESC'),
            'galleryAlbums' => array(self::HAS_MANY, 'EventGalleryAlbum', 'eventId'),
            'galleryImages' => array(self::HAS_MANY, 'EventGalleryImage', 'eventId'),
            'subscribers' => array(self::HAS_MANY, 'User', array('eventId' => 'userId'), 'through' => 'event_user', 'joinType' => 'INNER JOIN', 'order' => 'user.name ASC'),
            'invited' => array(self::HAS_MANY, 'User', array('eventId' => 'userId'), 'through' => 'event_user_invite', 'joinType' => 'INNER JOIN', 'order' => 'user.name ASC'),
            'subscribersCount' => array(self::STAT, 'EventUser', 'eventId'),
            'invitedCount' => array(self::STAT, 'EventUserInvite', 'eventId'),
            'commentsCount' => array(self::STAT, 'EventComment', 'eventId'),
            'isSubscribed' => array(self::STAT, 'EventUser', 'eventId'),
            'isInvited' => array(self::STAT, 'EventUserInvite', 'eventId')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'eventId' => Yii::t('application', 'ID'),
            'userId' => Yii::t('application', 'Создатель'),
            'category' => Yii::t('application', 'Категория мероприятия'),
            'publisherName' => Yii::t('application', 'Имя создателя'),
            'name' => Yii::t('application', 'Название мероприятия'),
            'image' => Yii::t('application', 'Изображение'),
            'description' => Yii::t('application', 'Описание'),
            'cityId' => Yii::t('application', 'Город'),
            'city' => Yii::t('application', 'Город'),
            'street' => Yii::t('application', 'Улица'),
            'houseNumber' => Yii::t('application', 'Дом'),
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'isPublic' => Yii::t('application', 'Доступно всем'),
            'isGlobal' => Yii::t('application', 'Глобальное'),
            'productId' => Yii::t('application', 'Товар'),
            'status' => Yii::t('application', 'Статус'),
            'eventAccess' => Yii::t('application', 'Доступ к мероприятию'),
            'dateStart' => Yii::t('application', 'Дата начала'),
            'dateStartYear' => Yii::t('application', 'Год'),
            'dateStartMonth' => Yii::t('application', 'Месяц'),
            'dateStartDay' => Yii::t('application', 'День'),
            'timeStart' => Yii::t('application', 'Время начала'),
            'timeStartHours' => Yii::t('application', 'Часы'),
            'timeStartMinutes' => Yii::t('application', 'Минуты'),
            'timeEnd' => Yii::t('application', 'Время окончания'),
            'timeEndHours' => Yii::t('application', 'Часы'),
            'timeEndMinutes' => Yii::t('application', 'Минуты'),
            'dateCreated' => Yii::t('application', 'Дата создания'),
            'imageFile' => Yii::t('application', 'Изображение')
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        if ($this->scenario == 'search_global') {
            $criteria->addColumnCondition(array('isGlobal' => 1));
            if ($this->relaxId == '1') {
                $criteria->addCondition('relaxId IS NOT NULL AND status = "'.self::STATUS_APPROVED.'"');
            } elseif ($this->relaxId == '0') {
                $criteria->addCondition('relaxId IS NULL AND status = "'.self::STATUS_APPROVED.'"');
            }
        } elseif ($this->scenario == 'search_users') {
            $criteria->addCondition('userId IS NOT NULL AND isGlobal = 0 AND (status = "'.self::STATUS_APPROVED.'" OR status = "'.self::STATUS_DECLINED.'")');
        } elseif ($this->scenario == 'search_on_validation') {
            $criteria->addCondition('userId IS NOT NULL AND isGlobal = 0 AND status = "'.self::STATUS_WAITING.'"');
        } elseif($this->scenario == 'search_from_relax') {
            $criteria->addCondition('relaxId IS NOT NULL AND isGlobal = 1 AND status = "'.self::STATUS_WAITING.'"');
        }

        $criteria->compare('eventId', $this->eventId, true);
        $criteria->compare('category', $this->category, true);
        $criteria->compare('publisherName', $this->publisherName, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('cityObject.name', $this->searchCityObject->name, true);
        $criteria->compare('dateStart', strtotime($this->dateStart), true);
        $criteria->compare('timeStart', $this->timeStart, true);
        $criteria->compare('dateCreated', strtotime($this->dateCreated), true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort'=>array(
              'defaultOrder'=>'dateCreated DESC',
            )
        ));
    }
}