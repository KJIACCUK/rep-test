<?php

    class m140627_094133_event_changes extends CDbMigration
    {

        public function up()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->execute('CREATE TABLE `city` ('
                    .'`cityId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, '
                    .'`name` text NOT NULL, '
                    .'`latitude` float(9,6) NOT NULL, '
                    .'`longitude` float(9,6) NOT NULL, '
                    .'`lowerCornerLatitude` float(9,6) NOT NULL, '
                    .'`lowerCornerLongitude` float(9,6) NOT NULL, '
                    .'`upperCornerLatitude` float(9,6) NOT NULL, '
                    .'`upperCornerLongitude` float(9,6) NOT NULL, '
                    .'PRIMARY KEY (`cityId`)  '
                    .') ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');

            $this->addColumn('event', 'cityId', 'int(11) UNSIGNED NOT NULL AFTER `description`');
            $this->addColumn('event', 'street', 'text NULL AFTER `cityId`');
            $this->addColumn('event', 'houseNumber', 'varchar(10) NULL AFTER `street`');
            $this->alterColumn('event', 'latitude', 'float(9,6) NULL');
            $this->alterColumn('event', 'longitude', 'float(9,6) NULL');
            $this->dropColumn('event', 'city');
            $this->dropColumn('event', 'place');
            $this->addForeignKey('fk_event_city_1', 'event', 'cityId', 'city', 'cityId', 'CASCADE', 'CASCADE');
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
        }

        public function down()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->dropForeignKey('fk_event_city_1', 'event');
            $this->addColumn('event', 'city', 'varchar(255) NOT NULL AFTER `description`');
            $this->addColumn('event', 'place', 'text DEFAULT NULL AFTER `city`');
            $this->alterColumn('event', 'longitude', 'float(8,5) NULL');
            $this->alterColumn('event', 'latitude', 'float(8,5) NULL');
            $this->dropColumn('event', 'houseNumber');
            $this->dropColumn('event', 'street');
            $this->dropColumn('event', 'cityId');
            $this->dropTable('city');
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
        }

    }
    