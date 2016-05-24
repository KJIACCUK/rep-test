<?php

    /**
     * This is the model class for table "point".
     *
     * The followings are the available columns in table 'point':
     * @property integer $pointId
     * @property string $pointKey
     * @property integer $pointsCount
     *
     * The followings are the available model relations:
     * @property PointUser[] $pointUsers
     */
    class Point extends CActiveRecord
    {

        const KEY_SOCIAL_INVITE = 'social_invite';
        const KEY_VERIFICATION = 'verification';
        const KEY_MARKETING_RESEARCH_VISIT = 'marketing_research_visit';
        const KEY_MARKETING_RESEARCH_ANSWER = 'marketing_research_answer';
        const KEY_EVENT_CREATE = 'event_create';
        const KEY_SOCIAL_SHARE = 'social_share';
        const KEY_TEN_EVENTS_SUBSCRIBED = 'ten_events_subscribed';
        const KEY_FROM_ADMIN = 'from_admin';

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return Point the static model class
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
            return 'point';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('pointKey, pointsCount', 'required'),
                array('pointKey', 'length', 'max' => 20),
                array('pointsCount', 'length', 'max' => 11),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('pointId, pointKey, pointsCount', 'safe', 'on' => 'search'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'pointUsers' => array(self::HAS_MANY, 'PointUser', 'pointId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'pointId' => 'Point',
                'pointKey' => 'Point Key',
                'pointsCount' => 'Points Count',
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

            $criteria->compare('pointId', $this->pointId, true);
            $criteria->compare('pointKey', $this->pointKey, true);
            $criteria->compare('pointsCount', $this->pointsCount, true);

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }

    }
    