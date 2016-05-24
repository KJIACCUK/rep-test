<?php

    /**
     * This is the model class for table "event_gallery_image".
     *
     * The followings are the available columns in table 'event_gallery_image':
     * @property integer $eventGalleryImageId
     * @property integer $eventId
     * @property integer $eventGalleryAlbumId
     * @property string $image
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property EventGalleryAlbum $album
     * @property Event $event
     */
    class EventGalleryImage extends CActiveRecord
    {

        public $imageFile;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return EventGalleryImage the static model class
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
            return 'event_gallery_image';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                // api_insert
                ApiValidatorHelper::file('imageFile', 'png, jpg, jpe, jpeg', null, 1 * 1024 * 1024, 1, 'api_insert') + array('allowEmpty' => false),
                ApiValidatorHelper::safe('eventId, eventGalleryAlbumId, dateCreated', 'api_insert'),
                // insert
                array('imageFile', 'file', 'types' => 'png, jpg, jpe, jpeg', 'maxSize' => 1 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => 'insert'),
                array('eventId, eventGalleryAlbumId, dateCreated', 'safe', 'on' => 'insert'),
            );
        }

        public function beforeValidate()
        {
            $this->dateCreated = time();
            return parent::beforeValidate();
        }

        public function afterValidate()
        {
            if($this->imageFile)
            {
                $eventPhotoDir = Yii::getPathOfAlias('webroot.content.images.photos').'/'.$this->eventId;

                $imagePath = $eventPhotoDir.'/'.CommonHelper::generateImageName(uniqid('event_'.$this->eventId)).'.'.$this->imageFile->getExtensionName();
                $imagePath = str_replace('\\', '/', $imagePath);

                $this->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
            }

            parent::afterValidate();
        }
        
        public function afterSave()
        {
            if($this->imageFile)
            {
                $eventPhotoDir = Yii::getPathOfAlias('webroot.content.images.photos').'/'.$this->eventId;
                if(!file_exists($eventPhotoDir))
                {
                    mkdir($eventPhotoDir);
                }

                $imagePath = Yii::getPathOfAlias('webroot').$this->image;

                if(file_exists($imagePath))
                {
                    unlink($imagePath);
                }

                $this->imageFile->saveAs($imagePath);
            }
            
            parent::afterSave();
        }

        public function afterDelete()
        {
            if(file_exists(Yii::getPathOfAlias('webroot').$this->image) && ($this->image != EventHelper::getDefaultImage()))
            {
                $eventPhotoDir = Yii::getPathOfAlias('webroot.content.images.photos').'/'.$this->eventId;
                unlink(Yii::getPathOfAlias('webroot').$this->image);
                ImageHelper::cleanCacheDir($eventPhotoDir, str_replace('/content/images/photos/'.$this->eventId, '', $this->image));
            }
            parent::afterDelete();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'album' => array(self::BELONGS_TO, 'EventGalleryAlbum', 'eventGalleryAlbumId'),
                'event' => array(self::BELONGS_TO, 'Event', 'eventId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'eventGalleryImageId' => Yii::t('application', 'ID'),
                'eventId' => Yii::t('application', 'Мероприятие'),
                'eventGalleryAlbumId' => Yii::t('application', 'Альбом'),
                'image' => Yii::t('application', 'Изображение'),
                'imageFile' => Yii::t('application', 'Изображение'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
            );
        }

    }
    