<?php

    /**
     * This is the model class for table "event_user_invite".
     *
     * The followings are the available columns in table 'event_user_invite':
     * @property integer $eventUserInviteId
     * @property integer $eventId
     * @property integer $userId
     *
     * The followings are the available model relations:
     * @property User $user
     * @property Event $event
     */
    class EventUserInvite extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return EventUserInvite the static model class
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
            return 'event_user_invite';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('eventId, userId', 'required'),
                array('eventId, userId', 'length', 'max' => 11)
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
                'eventUserInviteId' => 'Event User Invite',
                'eventId' => 'Event',
                'userId' => 'User',
            );
        }

    }
    