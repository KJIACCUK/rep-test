<?php

    /**
     * This is the model class for table "user_api_token".
     *
     * The followings are the available columns in table 'user_api_token':
     * @property integer $userApiTokenId
     * @property integer $userId
     * @property string $platform
     * @property string $token
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class UserApiToken extends CActiveRecord
    {

        const PLATFORM_ANDROID = 'android';
        const PLATFORM_IOS = 'ios';

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserApiToken the static model class
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
            return 'user_api_token';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, platform, token, dateCreated', 'required'),
                array('userId', 'length', 'max' => 11),
                array('platform, dateCreated', 'length', 'max' => 10),
                array('token', 'length', 'max' => 32),
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
                'userApiTokenId' => 'User Api Token',
                'userId' => 'User',
                'platform' => 'Platform',
                'token' => 'Token',
                'dateCreated' => 'Date Created',
            );
        }

    }
    