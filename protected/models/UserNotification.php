<?php

    /**
     * This is the model class for table "user_notification".
     *
     * The followings are the available columns in table 'user_notification':
     * @property integer $userNotificationId
     * @property integer $userId
     * @property string $settingKey
     * @property string $params
     * @property string $notificationText
     * @property integer $isReaded
     * @property integer $isPushed
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class UserNotification extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserNotification the static model class
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
            return 'user_notification';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, settingKey, params, notificationText, isReaded, isPushed, dateCreated', 'required'),
                array('isReaded, isPushed', 'numerical', 'integerOnly' => true),
                array('userId', 'length', 'max' => 11),
                array('settingKey', 'in', 'range' => array_merge(array_keys(UserNotificationsHelper::getDefaultSettins()), array(UserNotificationSetting::SETTING_FRIENDSHIP_REQUEST_ADDED))),
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
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userNotificationId' => 'User Notification',
                'userId' => 'User',
                'settingKey' => 'Setting Key',
                'params' => 'Params',
                'notificationText' => 'Notification Text',
                'isReaded' => 'Is Readed',
                'isPushed' => 'Is Pushed',
                'dateCreated' => 'Date Created',
            );
        }

    }
    