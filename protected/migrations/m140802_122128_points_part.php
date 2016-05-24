<?php

    class m140802_122128_points_part extends CDbMigration
    {

        public function up()
        {
            $this->execute("
                CREATE TABLE `point` (
                    `pointId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `pointKey` varchar(20) NOT NULL,
                    `pointsCount` int(11) UNSIGNED NOT NULL,
                    PRIMARY KEY (`pointId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->execute("
                CREATE TABLE `point_user` (
                    `pointUserId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `pointId` int(11) UNSIGNED NOT NULL,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `pointsCount` int(11) UNSIGNED NOT NULL,
                    `params` text NULL,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`pointUserId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->addForeignKey('fk_point_user_point_1', 'point_user', 'pointId', 'point', 'pointId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_point_user_user_1', 'point_user', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_point_user_user_1', 'point_user');
            $this->dropForeignKey('fk_point_user_point_1', 'point_user');

            $this->dropTable('point');
            $this->dropTable('point_user');
        }

    }
    