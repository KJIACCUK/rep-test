<?php

    /**
     * This is the model class for table "marketing_research_variant".
     *
     * The followings are the available columns in table 'marketing_research_variant':
     * @property integer $marketingResearchVariantId
     * @property integer $marketingResearchId
     * @property string $variant
     *
     * The followings are the available model relations:
     * @property MarketingResearchAnswerVariant[] $marketingResearchAnswerVariants
     * @property MarketingResearch $marketingResearch
     */
    class MarketingResearchVariant extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return MarketingResearchVariant the static model class
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
            return 'marketing_research_variant';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('marketingResearchId, variant', 'required'),
                array('marketingResearchId', 'length', 'max' => 11),
                array('variant', 'length', 'max' => 255)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'marketingResearchAnswerVariants' => array(self::HAS_MANY, 'MarketingResearchAnswerVariant', 'marketingResearchVariantId'),
                'marketingResearch' => array(self::BELONGS_TO, 'MarketingResearch', 'marketingResearchId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'marketingResearchVariantId' => Yii::t('application', 'Id'),
                'marketingResearchId' => Yii::t('application', 'Исследование'),
                'variant' => Yii::t('application', 'Вариант ответа'),
            );
        }

    }
    