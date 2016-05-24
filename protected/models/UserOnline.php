<?php

    /**
     * This is the model class for table "user_online".
     *
     * The followings are the available columns in table 'user_online':
     * @property integer $userOnlineId
     * @property integer $userId
     * @property integer $isOnline
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class UserOnline extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserOnline the static model class
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
            return 'user_online';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId', 'required'),
                array('isOnline', 'numerical', 'integerOnly' => true),
                array('userId', 'length', 'max' => 11),
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
                'userOnlineId' => 'User Online',
                'userId' => 'User',
                'isOnline' => 'Is Online',
            );
        }

    }
    