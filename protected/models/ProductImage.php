<?php

    /**
     * This is the model class for table "product_image".
     *
     * The followings are the available columns in table 'product_image':
     * @property integer $productImageId
     * @property integer $productId
     * @property string $image
     *
     * The followings are the available model relations:
     * @property Product $product
     */
    class ProductImage extends CActiveRecord
    {

        public $imageFile;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return ProductImage the static model class
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
            return 'product_image';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('productId', 'required'),
                array('productId', 'length', 'max' => 11),
                array('image', 'length', 'max' => 255),
                array('imageFile', 'file', 'types' => 'png, jpg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false),
            );
        }

        public function beforeValidate()
        {
            $this->imageFile = CUploadedFile::getInstance($this, 'imageFile');
            if($this->imageFile)
            {
                $imagesDir = Yii::getPathOfAlias('webroot.content.images.product_images').DIRECTORY_SEPARATOR.$this->productId.DIRECTORY_SEPARATOR;
                $this->image = $imagesDir.md5(time().$this->imageFile->getName()).'.'.$this->imageFile->getExtensionName();
                $this->image = str_replace(Yii::getPathOfAlias('webroot'), '', $this->image);
                $this->image = str_replace('\\', '/', $this->image);
            }
            return parent::beforeValidate();
        }

        public function afterSave()
        {
            if($this->imageFile)
            {
                $imagesDir = Yii::getPathOfAlias('webroot.content.images.product_images').DIRECTORY_SEPARATOR.$this->productId.DIRECTORY_SEPARATOR;
                if(!file_exists($imagesDir))
                {
                    mkdir($imagesDir);
                }
                $this->imageFile->saveAs(Yii::getPathOfAlias('webroot').$this->image);
            }
        }

        public function afterDelete()
        {
            if($this->image)
            {
                $image = Yii::getPathOfAlias('webroot').$this->image;
                if(file_exists($image))
                {
                    $imagesDir = Yii::getPathOfAlias('webroot.content.images.product_images').DIRECTORY_SEPARATOR.$this->productId.DIRECTORY_SEPARATOR;
                    unlink($image);
                    ImageHelper::cleanCacheDir($imagesDir, str_replace('/content/images/product_images/'.$this->productId.'/', '', $this->image));
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
                'product' => array(self::BELONGS_TO, 'Product', 'productId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'productImageId' => Yii::t('application', 'ID'),
                'productId' => Yii::t('application', 'Товар'),
                'image' => Yii::t('application', 'Изображение'),
                'imageFile' => Yii::t('application', 'Изображение')
            );
        }

    }
    