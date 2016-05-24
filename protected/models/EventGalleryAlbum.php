<?php

    /**
     * This is the model class for table "event_gallery_album".
     *
     * The followings are the available columns in table 'event_gallery_album':
     * @property integer $eventGalleryAlbumId
     * @property integer $eventId
     * @property string $name
     * @property integer $isDefault
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property Event $event
     * @property EventGalleryImage[] $images
     */
    class EventGalleryAlbum extends CActiveRecord
    {
        /**
         *
         * @var EventGalleryImage[]
         */
        public $imagesToDelete = array();

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return EventGalleryAlbum the static model class
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
            return 'event_gallery_album';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                // api_insert, api_update
                ApiValidatorHelper::required('name', array('api_insert', 'api_update')),
                ApiValidatorHelper::length('name', null, 255, array('api_insert', 'api_update')),
                ApiValidatorHelper::safe('eventId, isDefault, dateCreated', 'api_insert'),
                // insert, update
                array('name', 'required', 'on' => array('insert', 'update')),
                array('name', 'length', 'max' => 255, 'on' => array('insert', 'update')),
                array('eventId, isDefault, dateCreated', 'safe', 'on' => 'insert'),
            );
        }

        public function beforeValidate()
        {
            $this->dateCreated = time();
            return parent::beforeValidate();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'event' => array(self::BELONGS_TO, 'Event', 'eventId'),
                'images' => array(self::HAS_MANY, 'EventGalleryImage', 'eventGalleryAlbumId'),
            );
        }
        
        public function beforeDelete()
        {
            $criteria = new CDbCriteria();
            $criteria->index = 'eventGalleryImageId';
            $criteria->addColumnCondition(array('eventGalleryAlbumId' => $this->eventGalleryAlbumId));
            $this->imagesToDelete = EventGalleryImage::model()->findAll($criteria);
            return parent::beforeDelete();
        }
        
        public function afterDelete()
        {
            foreach($this->imagesToDelete as $item)
            {
                $item->delete();
            }
            parent::afterDelete();
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'eventGalleryAlbumId' => Yii::t('application', 'ID'),
                'eventId' => Yii::t('application', 'Мероприятие'),
                'name' => Yii::t('application', 'Название'),
                'isDefault' => Yii::t('application', 'По умолчанию'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
            );
        }

    }
    