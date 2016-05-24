<?php

    /**
     * This is the model class for table "account".
     *
     * The followings are the available columns in table 'account':
     * @property integer $accountId
     * @property string $type
     * @property integer $isActive
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     * @property Employee $employee
     */
    class Account extends CActiveRecord
    {

        const TYPE_ADMIN = 'admin';
        const TYPE_MODERATOR = 'moderator';
        const TYPE_OPERATOR = 'operator';
        const TYPE_USER = 'user';

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return Account the static model class
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
            return 'account';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('type, dateCreated', 'required'),
                array('isActive', 'numerical', 'integerOnly' => true),
                array('type', 'in', 'range' => array(self::TYPE_ADMIN, self::TYPE_MODERATOR, self::TYPE_OPERATOR, self::TYPE_USER)),
                array('dateCreated', 'length', 'max' => 10)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'user' => array(self::HAS_ONE, 'User', 'accountId'),
                'employee' => array(self::HAS_ONE, 'Employee', 'accountId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'accountId' => Yii::t('application', 'ID'),
                'type' => Yii::t('application', 'Тип'),
                'isActive' => Yii::t('application', 'Активен'),
                'dateCreated' => Yii::t('application', 'Дата добавления'),
            );
        }

    }
    