<?php

    /**
     * This is the model class for table "event_user".
     *
     * The followings are the available columns in table 'event_user':
     * @property integer $eventUserId
     * @property integer $eventId
     * @property integer $userId
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     * @property Event $event
     */
    class EventUser extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return EventUser the static model class
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
            return 'event_user';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('eventId, userId, dateCreated', 'required'),
                array('eventId, userId', 'length', 'max' => 11),
                array('dateCreated', 'length', 'max' => 10)
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

        public function selectedUser($userId)
        {
            $this->getDbCriteria()->mergeWith(array(
                'condition' => 'userId = :userIdForStats',
                'params' => array(':userIdForStats' => $userId),
            ));
            return $this;
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'eventUserId' => 'Event User',
                'eventId' => 'Event',
                'userId' => 'User',
                'dateCreated' => 'Date Created',
            );
        }

    }
    