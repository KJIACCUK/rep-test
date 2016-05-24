<?php

    /**
     * This is the model class for table "city".
     *
     * The followings are the available columns in table 'city':
     * @property integer $cityId
     * @property string $name
     * @property double $latitude
     * @property double $longitude
     * @property double $lowerCornerLatitude
     * @property double $lowerCornerLongitude
     * @property double $upperCornerLatitude
     * @property double $upperCornerLongitude
     *
     * The followings are the available model relations:
     * @property Event[] $events
     */
    class City extends CActiveRecord
    {
        public $relaxCityId;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.÷
         * @return City the static model class
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
            return 'city';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('name, latitude, longitude, lowerCornerLatitude, lowerCornerLongitude, upperCornerLatitude, upperCornerLongitude', 'required'),
                array('latitude, longitude, lowerCornerLatitude, lowerCornerLongitude, upperCornerLatitude, upperCornerLongitude', 'numerical'),
                array('name', 'safe', 'on' => 'search')
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'events' => array(self::HAS_MANY, 'Event', 'cityId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'cityId' => 'City',
                'name' => 'Name',
                'latitude' => 'Latitude',
                'longitude' => 'Longitude',
                'lowerCornerLatitude' => 'Lower Corner Latitude',
                'lowerCornerLongitude' => 'Lower Corner Longitude',
                'upperCornerLatitude' => 'Upper Corner Latitude',
                'upperCornerLongitude' => 'Upper Corner Longitude',
            );
        }

    }
    