<?php

    /**
     * This is the model class for table "user_message".
     *
     * The followings are the available columns in table 'user_message':
     * @property integer $userMessageId
     * @property integer $userId
     * @property integer $recipientId
     * @property string $message
     * @property integer $isReaded
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $recipient
     * @property User $user
     */
    class UserMessage extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserMessage the static model class
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
            return 'user_message';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, recipientId, message, dateCreated', 'required'),
                array('isReaded', 'numerical', 'integerOnly' => true),
                array('userId, recipientId', 'length', 'max' => 11),
                array('message', 'length', 'max' => 2000),
                array('dateCreated', 'length', 'max' => 10)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'recipient' => array(self::BELONGS_TO, 'User', 'recipientId'),
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userMessageId' => Yii::t('application', 'ID'),
                'userId' => Yii::t('application', 'Отправитель'),
                'recipientId' => Yii::t('application', 'Получатель'),
                'message' => Yii::t('application', 'Сообщение'),
                'isReaded' => Yii::t('application', 'Прочтено'),
                'dateCreated' => Yii::t('application', 'Создано'),
            );
        }

    }
    