<?php

    /**
     * This is the model class for table "product_purchase".
     *
     * The followings are the available columns in table 'product_purchase':
     * @property integer $productPurchaseId
     * @property integer $productId
     * @property integer $userId
     * @property string $purchaseCode
     * @property integer $pointsCount
     * @property string $deliveryType
     * @property string $comment
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property DeliveryAddress $deliveryAddress
     * @property User $user
     * @property Product $product
     */
    class ProductPurchase extends CActiveRecord
    {

        const DELIVERY_TYPE_SELF = 'self';
        const DELIVERY_TYPE_COMPANY = 'company';

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return ProductPurchase the static model class
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
            return 'product_purchase';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                ApiValidatorHelper::length('comment', null, 255, 'api_insert'),
                array('productId, userId, pointsCount, deliveryType, dateCreated', 'required'),
                array('productId, userId, pointsCount', 'length', 'max' => 11),
                array('purchaseCode', 'length', 'max' => 255),
                array('deliveryType', 'length', 'max' => 50),
                array('dateCreated', 'length', 'max' => 10),
                array('comment', 'length', 'max' => 5000, 'on' => 'insert'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('productPurchaseId, productId, userId, pointsCount, deliveryType, dateCreated', 'safe', 'on' => 'search'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'deliveryAddress' => array(self::HAS_ONE, 'DeliveryAddress', 'productPurchaseId'),
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
                'product' => array(self::BELONGS_TO, 'Product', 'productId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'productPurchaseId' => Yii::t('application', 'ID'),
                'productId' => Yii::t('application', 'Товар'),
                'userId' => Yii::t('application', 'Пользователь'),
                'purchaseCode' => Yii::t('application', 'Код покупки'),
                'pointsCount' => Yii::t('application', 'Количество баллов'),
                'deliveryType' => Yii::t('application', 'Тип доставки'),
                'comment' => Yii::t('application', 'Комментарий'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
            );
        }

        /**
         * Retrieves a list of models based on the current search/filter conditions.
         * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
         */
        public function search()
        {
            // Warning: Please modify the following code to remove attributes that
            // should not be searched.

            $criteria = new CDbCriteria;

            $criteria->compare('productPurchaseId', $this->productPurchaseId, true);
            $criteria->compare('productId', $this->productId, true);
            $criteria->compare('userId', $this->userId, true);
            $criteria->compare('pointsCount', $this->pointsCount, true);
            $criteria->compare('deliveryType', $this->deliveryType, true);
            $criteria->compare('dateCreated', $this->dateCreated, true);

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }

    }
    