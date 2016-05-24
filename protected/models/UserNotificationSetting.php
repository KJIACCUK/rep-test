<?php

    /**
     * This is the model class for table "user_notification_setting".
     *
     * The followings are the available columns in table 'user_notification_setting':
     * @property integer $userNotificationSettingId
     * @property integer $userId
     * @property string $settingKey
     * @property integer $isChecked
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class UserNotificationSetting extends CActiveRecord
    {

        const SETTING_FRIENDSHIP_REQUEST = 'friendship_request';
        const SETTING_FRIENDSHIP_REQUEST_ADDED = 'friendship_request_added';
        const SETTING_EVENT_INVITE = 'event_invite';
        const SETTING_EVENT_GLOBAL_INVITE = 'event_global_invite';
        const SETTING_EVENT_FRIEND_SUBSCRIBED = 'event_friend_subscribed';
        const SETTING_EVENT_GALLERY_UPDATED = 'event_gallery_updated';
        const SETTING_EVENT_NEW_COMMENT = 'event_new_comment';
        const SETTING_MY_EVENT_STATUS_UPDATED = 'my_event_status_updated';
        const SETTING_MY_EVENT_NEW_SUBSCRIBER = 'my_event_new_subscriber';
        const SETTING_MY_EVENT_NEW_COMMENT = 'my_event_new_comment';
        const SETTING_NEW_MARKETING_RESEARCH = 'new_marketing_research';
        const SETTING_SEND_TO_EMAIL= 'send_to_email';
        const SETTING_MONTLY_DIGEST= 'montly_digest';
        const SETTING_ANDROID_ENABLE_VIBRATION = 'anddroid_enable_vibration';

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserNotificationSetting the static model class
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
            return 'user_notification_setting';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, settingKey', 'required'),
                array('isChecked', 'numerical', 'integerOnly' => true),
                array('userId', 'length', 'max' => 11),
                array('settingKey', 'in', 'range' => array_keys(UserNotificationsHelper::getDefaultSettins()))
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
                'userNotificationSettingId' => 'User Notification Setting',
                'userId' => 'User',
                'settingKey' => 'Setting Key',
                'isChecked' => 'Is Checked',
            );
        }

    }
    