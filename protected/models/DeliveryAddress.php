<?php

    /**
     * This is the model class for table "delivery_address".
     *
     * The followings are the available columns in table 'delivery_address':
     * @property integer $deliveryAddressId
     * @property integer $productPurchaseId
     * @property string $postIndex
     * @property string $city
     * @property string $street
     * @property string $home
     * @property string $corp
     * @property string $apartment
     * @property string $email
     * @property string $phone
     *
     * The followings are the available model relations:
     * @property ProductPurchase $productPurchase
     */
    class DeliveryAddress extends CActiveRecord
    {

        public $phoneCode;
        public $phoneNumber;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return DeliveryAddress the static model class
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
            return 'delivery_address';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // insert, api_insert
            return array(
                ApiValidatorHelper::required(', postIndex, city, street, home', 'api_insert'),
                ApiValidatorHelper::length('postIndex, city, street, home, corp, apartment', null, 255, 'api_insert'),
                ApiValidatorHelper::email('email', 'api_insert'),
                ApiValidatorHelper::type('phoneNumber', 'integer', 'api_insert'),
                ApiValidatorHelper::length('phoneNumber', 7, 7, 'api_insert'),
                ApiValidatorHelper::in('phoneCode', array_keys(Yii::app()->params['phoneCodes']), 'api_insert'),
                ApiValidatorHelper::safe('productPurchaseId, email, phone, phoneCode, phoneNumber', 'api_insert'),
                array('productPurchaseId, postIndex, city, street, home, email, phone, phoneCode, phoneNumber', 'required', 'on' => 'insert'),
                array('productPurchaseId', 'length', 'max' => 11, 'on' => 'insert'),
                array('postIndex, city, street, home, corp, apartment, email, phone', 'length', 'max' => 255, 'on' => 'insert'),
                array('email', 'email', 'on' => 'insert'),
                array('phoneNumber', 'length', 'min' => 7, 'max' => 7, 'on' => 'insert'),
                array('phoneNumber', 'numerical', 'integerOnly' => true, 'min' => 0, 'on' => 'insert', 'tooSmall' => Yii::t('application', 'Неверное значение номера телефона')),
                array('phoneCode', 'in', 'range' => array_keys(Yii::app()->params['phoneCodes']), 'on' => 'insert'),
            );
        }

        public function beforeValidate()
        {
            $this->phone = '+375'.$this->phoneCode.$this->phoneNumber;
            return parent::beforeValidate();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'productPurchase' => array(self::BELONGS_TO, 'ProductPurchase', 'productPurchaseId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'deliveryAddressId' => Yii::t('application', 'ID'),
                'productPurchaseId' => Yii::t('application', 'Заказ'),
                'postIndex' => Yii::t('application', 'Индекс'),
                'city' => Yii::t('application', 'Город'),
                'street' => Yii::t('application', 'Улица'),
                'home' => Yii::t('application', 'Дом'),
                'corp' => Yii::t('application', 'Корпус'),
                'apartment' => Yii::t('application', 'Помещение'),
                'email' => Yii::t('application', 'E-mail'),
                'phoneCode' => Yii::t('application', 'Код оператора'),
                'phoneNumber' => Yii::t('application', 'Номер')
            );
        }

    }
    