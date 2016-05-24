<?php

    /**
     * This is the model class for table "user_verification".
     *
     * The followings are the available columns in table 'user_verification':
     * @property integer $userVerificationId
     * @property integer $userId
     * @property integer $employeeId
     * @property string $comment
     * @property string $attachmentFilePath
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property Employee $employee
     * @property User $user
     */
    class UserVerification extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserVerification the static model class
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
            return 'user_verification';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('userId, employeeId, dateCreated', 'required'),
                array('userId, employeeId, dateCreated', 'length', 'max' => 11),
                array('attachmentFilePath', 'length', 'max' => 255),
                array('comment', 'safe')
            );
        }

        public function beforeValidate()
        {
            $this->dateCreated = time();
            return parent::beforeValidate();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'employee' => array(self::BELONGS_TO, 'Employee', 'employeeId'),
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userVerificationId' => Yii::t('application', 'ID'),
                'userId' => Yii::t('application', 'Пользователь'),
                'employeeId' => Yii::t('application', 'Сотрудник'),
                'comment' => Yii::t('application', 'Комментарий'),
                'attachmentFilePath' => Yii::t('application', 'Приложение'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
                'isVerified' => Yii::t('application', 'Верифицировать'),
            );
        }

    }
    