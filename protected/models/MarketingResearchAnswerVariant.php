<?php

    /**
     * This is the model class for table "marketing_research_answer_variant".
     *
     * The followings are the available columns in table 'marketing_research_answer_variant':
     * @property integer $marketingResearchAnswerVariantId
     * @property integer $marketingResearchId
     * @property integer $marketingResearchVariantId
     * @property integer $userId
     *
     * The followings are the available model relations:
     * @property MarketingResearchVariant $marketingResearchVariant
     * @property User $user
     * @property MarketingResearch $marketingResearch
     */
    class MarketingResearchAnswerVariant extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return MarketingResearchAnswerVariant the static model class
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
            return 'marketing_research_answer_variant';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('marketingResearchId, marketingResearchVariantId, userId', 'required'),
                array('marketingResearchId, marketingResearchVariantId, userId', 'length', 'max' => 11)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'marketingResearchVariant' => array(self::BELONGS_TO, 'MarketingResearchVariant', 'marketingResearchVariantId'),
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
                'marketingResearch' => array(self::BELONGS_TO, 'MarketingResearch', 'marketingResearchId'),
            );
        }
        
        public function selectedUser($userId)
        {
            $this->getDbCriteria()->mergeWith(array(
                'condition' => 'userId = :selectedUser',
                'params' => array(':selectedUser' => $userId),
            ));
            return $this;
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'marketingResearchAnswerVariantId' => 'Marketing Research Answer Variant',
                'marketingResearchId' => 'Marketing Research',
                'marketingResearchVariantId' => 'Marketing Research Variant',
                'userId' => 'User',
            );
        }

    }
    