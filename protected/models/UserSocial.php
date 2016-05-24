<?php

    /**
     * This is the model class for table "user_social".
     *
     * The followings are the available columns in table 'user_social':
     * @property integer $userSocialId
     * @property integer $userId
     * @property string $type
     * @property string $socialId
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class UserSocial extends CActiveRecord
    {

        const TYPE_FACEBOOK = 'facebook';
        const TYPE_VKONTAKTE = 'vkontakte';
        const TYPE_TWITTER = 'twitter';

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserSocial the static model class
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
            return 'user_social';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, type, socialId, dateCreated', 'required'),
                array('userId', 'length', 'max' => 11),
                array('type, dateCreated', 'length', 'max' => 10),
                array('socialId', 'length', 'max' => 255)
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
                'userSocialId' => 'User Social',
                'userId' => 'User',
                'type' => 'Type',
                'socialId' => 'Social',
                'dateCreated' => 'Date Created',
            );
        }

    }
    