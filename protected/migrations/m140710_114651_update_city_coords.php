<?php

    class m140710_114651_update_city_coords extends CDbMigration
    {

        public function up()
        {
            $data = array(
                'lowerCornerLatitude' => 53.830833,
                'lowerCornerLongitude' => 27.391321,
                'upperCornerLatitude' => 53.970249,
                'upperCornerLongitude' => 27.702887
            );
            $this->update('city', $data, 'cityId = :cityId', array(':cityId' => 16));
        }

        public function down()
        {
            $data = array(
                'lowerCornerLatitude' => 53.793880,
                'lowerCornerLongitude' => 27.374416,
                'upperCornerLatitude' => 53.971588,
                'upperCornerLongitude' => 28.063091
            );
            $this->update('city', $data, 'cityId = :cityId', array(':cityId' => 16));
        }

    }
    