<?php

    /**
     * This is the model class for table "marketing_research_user_answer".
     *
     * The followings are the available columns in table 'marketing_research_user_answer':
     * @property integer $marketingResearchUserAnswerId
     * @property integer $marketingResearchId
     * @property integer $userId
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     * @property MarketingResearch $marketingResearch
     */
    class MarketingResearchUserAnswer extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return MarketingResearchUserAnswer the static model class
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
            return 'marketing_research_user_answer';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('marketingResearchId, userId, dateCreated', 'required'),
                array('marketingResearchId, userId', 'length', 'max' => 11),
                array('dateCreated', 'length', 'max' => 10)
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
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
                'marketingResearchUserAnswerId' => 'Marketing Research User Answer',
                'marketingResearchId' => 'Marketing Research',
                'userId' => 'User',
                'dateCreated' => 'Date Created',
            );
        }

    }
    