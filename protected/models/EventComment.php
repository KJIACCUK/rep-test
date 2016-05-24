<?php

    /**
     * This is the model class for table "event_comment".
     *
     * The followings are the available columns in table 'event_comment':
     * @property integer $eventCommentId
     * @property integer $eventId
     * @property integer $userId
     * @property string $content
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     * @property Event $event
     */
    class EventComment extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return EventComment the static model class
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
            return 'event_comment';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                // api_insert
                ApiValidatorHelper::required('content', 'api_insert'),
                ApiValidatorHelper::length('content', null, 2000, 'api_insert'),
                ApiValidatorHelper::safe('eventId, userId, dateCreated', 'api_insert'),
                // insert
                array('content', 'required', 'on' => 'insert'),
                array('content', 'length', 'max' => 2000, 'on' => 'insert'),
                array('eventId, userId, dateCreated', 'safe', 'on' => 'insert'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
                'event' => array(self::BELONGS_TO, 'Event', 'eventId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'eventCommentId' => Yii::t('application', 'ID'),
                'eventId' => Yii::t('application', ' Мероприятие'),
                'userId' => Yii::t('application', 'Автор комментария'),
                'content' => Yii::t('application', 'Текст'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
            );
        }

    }
    