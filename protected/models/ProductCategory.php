<?php

    /**
     * This is the model class for table "product_category".
     *
     * The followings are the available columns in table 'product_category':
     * @property integer $productCategoryId
     * @property string $name
     * @property integer $parent
     * @property integer $level
     *
     * The followings are the available model relations:
     * @property Product[] $products
     * @property ProductCategory $parentCategory
     * @property ProductCategory[] $childsCategories
     */
    class ProductCategory extends CActiveRecord
    {
        /**
         *
         * @var ProductCategory
         */
        private $_parent;
        public $parentCategoryName;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return ProductCategory the static model class
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
            return 'product_category';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('name', 'required'),
                array('name', 'length', 'max' => 255),
                array('parent, level', 'length', 'max' => 11),
                array('parent', 'validateParent'),
                array('parent', 'unsafe', 'on' => 'update'),
                array('productCategoryId, name, level', 'safe', 'on' => 'search'),
            );
        }
        
        public function validateParent()
        {
            if($this->parent)
            {
                $this->_parent = ProductCategory::model()->findByPk($this->parent);
                if(!$this->_parent)
                {
                    $this->addError('parent', Yii::t('application', 'Выбранной родительской категории не существует'));
                }
            }
        }
        
        public function beforeSave()
        {
            if($this->_parent)
            {
                $this->level = $this->_parent->level + 1;
            }
            else
            {
                $this->level = 1;
            }
            if(!$this->parent)
            {
                $this->parent = null;
            }
            return parent::beforeSave();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'products' => array(self::HAS_MANY, 'Product', 'productCategoryId'),
                'parentCategory' => array(self::BELONGS_TO, 'ProductCategory', 'parent'),
                'childsCategories' => array(self::HAS_MANY, 'ProductCategory', 'parent'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'productCategoryId' => Yii::t('application', 'ID'),
                'name' => Yii::t('application', 'Название'),
                'parent' => Yii::t('application', 'Родительская категория'),
                'parentCategoryName' => Yii::t('application', 'Родительская категория'),
                'level' => Yii::t('application', 'Уровень'),
            );
        }

        /**
         * Retrieves a list of models based on the current search/filter conditions.
         * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
         */
        public function search()
        {
            $criteria = new CDbCriteria();

            $criteria->compare('productCategoryId', $this->productCategoryId, true);
            $criteria->compare('name', $this->name, true);
            $criteria->compare('level', $this->level);

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }

    }
    