<?php

    /**
     * This is the model class for table "user_friend_request".
     *
     * The followings are the available columns in table 'user_friend_request':
     * @property integer $userFriendRequestId
     * @property integer $userId
     * @property integer $recipientId
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $recipient
     * @property User $user
     */
    class UserFriendRequest extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserFriendRequest the static model class
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
            return 'user_friend_request';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, recipientId, dateCreated', 'required'),
                array('userId, recipientId', 'length', 'max' => 11),
                array('dateCreated', 'length', 'max' => 10)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'recipient' => array(self::BELONGS_TO, 'User', array('recipientId' => 'userId')),
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userFriendRequestId' => 'User Friend Request',
                'userId' => 'User',
                'recipientId' => 'Recipient',
                'dateCreated' => 'Date Created',
            );
        }

    }
    