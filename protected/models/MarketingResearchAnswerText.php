<?php

    /**
     * This is the model class for table "marketing_research_answer_text".
     *
     * The followings are the available columns in table 'marketing_research_answer_text':
     * @property integer $marketingResearchAnswerTextId
     * @property integer $marketingResearchId
     * @property integer $userId
     * @property string $answer
     *
     * The followings are the available model relations:
     * @property User $user
     * @property MarketingResearch $marketingResearch
     */
    class MarketingResearchAnswerText extends CActiveRecord
    {

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return MarketingResearchAnswerText the static model class
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
            return 'marketing_research_answer_text';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('marketingResearchId, userId, answer', 'required'),
                array('marketingResearchId, userId', 'length', 'max' => 11),
                array('answer', 'length', 'max' => 5000),
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
                'marketingResearchAnswerTextId' => Yii::t('application', 'ID'),
                'marketingResearchId' => Yii::t('application', 'Исследование'),
                'userId' => Yii::t('application', 'Пользователь'),
                'answer' => Yii::t('application', 'Ответ'),
            );
        }

    }
    