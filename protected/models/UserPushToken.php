<?php

    /**
     * This is the model class for table "user_push_token".
     *
     * The followings are the available columns in table 'user_push_token':
     * @property integer $userPushTokenId
     * @property integer $userId
     * @property string $apiToken
     * @property string $platform
     * @property string $pushToken
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class UserPushToken extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserPushToken the static model class
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
            return 'user_push_token';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, apiToken, platform, pushToken, dateCreated', 'required'),
                array('dateCreated', 'numerical', 'integerOnly' => true),
                array('userId', 'length', 'max' => 11),
                array('apiToken', 'length', 'max' => 32),
                array('platform', 'length', 'max' => 10),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userPushTokenId' => 'User Push Token',
                'userId' => 'User',
                'apiToken' => 'Api Token',
                'platform' => 'Platform',
                'pushToken' => 'Push Token',
                'dateCreated' => 'Date Created',
            );
        }

    }
    