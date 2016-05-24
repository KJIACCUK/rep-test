<?php

    /**
     * This is the model class for table "product".
     *
     * The followings are the available columns in table 'product':
     * @property integer $productId
     * @property integer $productCategoryId
     * @property string $publisherName
     * @property string $name
     * @property string $description
     * @property string $image
     * @property integer $cost
     * @property string $type
     * @property string $attachment
     * @property string $receiptAddress
     * @property string $articleCode
     * @property string $itemsCount
     * @property integer $isActive
     * @property integer $dateStart
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property ProductCategory $category
     * @property ProductImage[] $images
     * @property ProductPurchase[] $purchases
     */
    class Product extends CActiveRecord
    {

        const TYPE_WITH_SERTIFICATE = 'with_sertificate';
        const TYPE_WITH_RECEIPT_ADDRESS = 'with_receipt_address';
        const TYPE_WITH_DELIVERY = 'with_delivery';

        public $imageFile;
        public $attachmentFile;
        private $_attachmentToDelete;

        /**
         *
         * @var ProductImage[]
         */
        private $_imagesToDelete;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return Product the static model class
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
            return 'product';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // insert, insert_with_sertificate, update, update_with_sertificate
            return array(
                array('productCategoryId, publisherName, name, description, itemsCount, cost, type, isActive, dateCreated', 'required'),
                array('itemsCount, cost, isActive', 'numerical', 'integerOnly' => true),
                array('productCategoryId, cost', 'length', 'max' => 11),
                array('itemsCount', 'length', 'max' => 5),
                array('publisherName, name, image, attachment, articleCode', 'length', 'max' => 255),
                array('type', 'in', 'range' => array_keys(ProductHelper::typesToEdit())),
                array('dateStart', 'date', 'format' => 'd.M.yyyy'),
                array('dateCreated', 'length', 'max' => 10),
                array('imageFile', 'file', 'types' => 'png, jpg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => 'insert'),
                array('imageFile', 'file', 'types' => 'png, jpg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => true, 'on' => 'update'),
//                array('imageFile', 'file', 'types' => 'png, jpg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => array('insert', 'insert_with_sertificate')),
//                array('imageFile', 'file', 'types' => 'png, jpg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => true, 'on' => array('update', 'update_with_sertificate')),
                //array('attachmentFile', 'file', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => array('insert_with_sertificate', 'update_with_sertificate')),
                array('receiptAddress', 'safe'),
                array('productId, productCategoryId, name, cost, type, articleCode, isActive', 'safe', 'on' => 'search'),
            );
        }

        public function beforeValidate()
        {
            if(in_array($this->scenario, array('insert', 'insert_with_sertificate')))
            {
                $this->dateCreated = time();
            }
            $this->imageFile = CUploadedFile::getInstance($this, 'imageFile');
            if($this->imageFile)
            {
                $this->image = Yii::getPathOfAlias('webroot.content.images.products').DIRECTORY_SEPARATOR.md5(time().$this->imageFile->getName()).'.'.$this->imageFile->getExtensionName();
                $this->image = str_replace(Yii::getPathOfAlias('webroot'), '', $this->image);
                $this->image = str_replace('\\', '/', $this->image);
            }
            $this->attachmentFile = CUploadedFile::getInstance($this, 'attachmentFile');
            if(in_array($this->scenario, array('insert_with_sertificate', 'update_with_sertificate')) && $this->attachmentFile)
            {
                $this->attachment = Yii::getPathOfAlias('webroot.content.product_certificates').DIRECTORY_SEPARATOR.md5(time().$this->attachmentFile->getName()).'.'.$this->attachmentFile->getExtensionName();
                $this->attachment = str_replace(Yii::getPathOfAlias('webroot'), '', $this->attachment);
                $this->attachment = str_replace('\\', '/', $this->attachment);
            }
            else
            {
                $this->_attachmentToDelete = $this->attachment;
                $this->attachment = null;
            }
            return parent::beforeValidate();
        }

        public function afterValidate()
        {
            if($this->dateStart)
            {
                $this->dateStart = strtotime($this->dateStart.' midnight');
            }
            parent::afterValidate();
        }

        public function afterSave()
        {
            if($this->imageFile)
            {
                $this->imageFile->saveAs(Yii::getPathOfAlias('webroot').$this->image);
            }

            if(in_array($this->scenario, array('insert_with_sertificate', 'update_with_sertificate')) && $this->attachmentFile)
            {
                $this->attachmentFile->saveAs(Yii::getPathOfAlias('webroot').$this->attachment);
            }

            if($this->_attachmentToDelete)
            {
                $attachmentToDelete = Yii::getPathOfAlias('webroot').$this->_attachmentToDelete;
                if(file_exists($attachmentToDelete))
                {
                    unlink($attachmentToDelete);
                }
            }
        }

        public function beforeDelete()
        {
            $this->_imagesToDelete = ProductImage::model()->findAllByAttributes(array('productId' => $this->productId));
            return parent::beforeDelete();
        }

        public function afterDelete()
        {
            if($this->image)
            {
                $image = Yii::getPathOfAlias('webroot').$this->image;
                if(file_exists($image))
                {
                    $productDir = Yii::getPathOfAlias('webroot.content.images.products').'/';
                    unlink($image);
                    ImageHelper::cleanCacheDir($productDir, str_replace('/content/images/products/', '', $this->image));
                }
            }

            if($this->attachment)
            {
                $attachment = Yii::getPathOfAlias('webroot').$this->attachment;
                if(file_exists($attachment))
                {
                    unlink($attachment);
                }
            }

            $imagesDir = Yii::getPathOfAlias('webroot.content.images.product_images').DIRECTORY_SEPARATOR.$this->productId;
            foreach($this->_imagesToDelete as $item)
            {
                if($item->image)
                {
                    $image = Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.$item->image;
                    if(file_exists($image))
                    {
                        
                        unlink($image);
                        ImageHelper::cleanCacheDir($imagesDir, str_replace('/content/images/product_images/'.$this->productId.'/', '', $item->image));
                    }
                }
            }

            parent::afterDelete();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'category' => array(self::BELONGS_TO, 'ProductCategory', 'productCategoryId'),
                'images' => array(self::HAS_MANY, 'ProductImage', 'productId'),
                'purchases' => array(self::HAS_MANY, 'ProductPurchase', 'productId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'productId' => Yii::t('application', 'ID'),
                'productCategoryId' => Yii::t('application', 'Категория'),
                'publisherName' => Yii::t('application', 'Продавец'),
                'name' => Yii::t('application', 'Название'),
                'description' => Yii::t('application', 'Описание'),
                'image' => Yii::t('application', 'Изображение'),
                'imageFile' => Yii::t('application', 'Изображение'),
                'cost' => Yii::t('application', 'Стоимость'),
                'type' => Yii::t('application', 'Тип'),
                'attachment' => Yii::t('application', 'Прикрепленный документ'),
                'attachmentFile' => Yii::t('application', 'Прикрепленный документ'),
                'receiptAddress' => Yii::t('application', 'Адрес получения'),
                'articleCode' => Yii::t('application', 'Код'),
                'itemsCount' => Yii::t('application', 'Количество'),
                'isActive' => Yii::t('application', 'Активен'),
                'dateStart' => Yii::t('application', 'Дата начала'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
            );
        }

        /**
         * Retrieves a list of models based on the current search/filter conditions.
         * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
         */
        public function search()
        {
            $criteria = new CDbCriteria();
            $criteria->with = array('category');

            $criteria->compare('productId', $this->productId, true);
            $criteria->compare('t.productCategoryId', $this->productCategoryId, true);
            $criteria->compare('t.name', $this->name, true);
            $criteria->compare('cost', $this->cost, true);
            $criteria->compare('type', $this->type, true);
            $criteria->compare('articleCode', $this->articleCode, true);
            $criteria->compare('itemsCount', $this->itemsCount);
            $criteria->compare('isActive', $this->isActive);

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
                'sort'=>array(
                  'defaultOrder'=>'dateCreated DESC',
                )
            ));
        }

    }
    