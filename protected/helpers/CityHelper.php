<?php

    class CityHelper
    {

        public static function export(City $city)
        {
            return array(
                'cityId' => $city->cityId,
                'name' => $city->name,
                'center' => array(
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude
                ),
                'boundary' => array(
                    'lowerCorner' => array(
                        'latitude' => $city->lowerCornerLatitude,
                        'longitude' => $city->lowerCornerLongitude
                    ),
                    'upperCorner' => array(
                        'latitude' => $city->upperCornerLatitude,
                        'longitude' => $city->upperCornerLongitude
                    )
                )
            );
        }
        
        public static function getCities()
        {
            $cities = array();
            $criteria = new CDbCriteria();
            $criteria->order = 'name ASC';
            $rows = City::model()->findAll($criteria);
            foreach($rows as $item)
            {
                $cities[] = self::export($item);
            }
            return $cities;
        }

    }
    