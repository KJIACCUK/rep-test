<?php

    /**
     * This is the model class for table "user_friend".
     *
     * The followings are the available columns in table 'user_friend':
     * @property integer $userFriendId
     * @property integer $userId
     * @property integer $friendId
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $friend
     * @property User $user
     */
    class UserFriend extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserFriend the static model class
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
            return 'user_friend';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, friendId, dateCreated', 'required'),
                array('userId, friendId', 'length', 'max' => 11),
                array('dateCreated', 'length', 'max' => 10)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'friend' => array(self::BELONGS_TO, 'User', array('friendId' => 'userId')),
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userFriendId' => 'User Friend',
                'userId' => 'User',
                'friendId' => 'Friend',
                'dateCreated' => 'Date Created',
            );
        }

    }
    