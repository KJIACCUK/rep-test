<?php

    /**
     * This is the model class for table "point_user".
     *
     * The followings are the available columns in table 'point_user':
     * @property integer $pointUserId
     * @property integer $pointId
     * @property integer $userId
     * @property integer $pointsCount
     * @property string $params
     * @property integer $dateCreated
     *
     * The followings are the available model relations:
     * @property User $user
     * @property Point $point
     */
    class PointUser extends CActiveRecord
    {
        public $pointsSum;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return PointUser the static model class
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
            return 'point_user';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('pointId, userId, pointsCount, dateCreated', 'required'),
                array('pointId, userId, pointsCount', 'length', 'max' => 11),
                array('dateCreated', 'length', 'max' => 10),
                array('params', 'safe')
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
                'point' => array(self::BELONGS_TO, 'Point', 'pointId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'pointUserId' => 'Point User',
                'pointId' => 'Point',
                'userId' => 'User',
                'pointsCount' => 'Points Count',
                'params' => 'Params',
                'dateCreated' => 'Date Created',
            );
        }

    }
    