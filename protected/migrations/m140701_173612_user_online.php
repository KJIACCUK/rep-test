<?php

    class m140701_173612_user_online extends CDbMigration
    {

        public function up()
        {
            $this->execute('CREATE TABLE `budutam`.`user_online` (
                                `userOnlineId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `userId` int(11) UNSIGNED NOT NULL,
                                `isOnline` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
                                PRIMARY KEY (`userOnlineId`) 
                            )');
            $this->addForeignKey('fk_user_online_user_1', 'user_online', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_user_online_user_1', 'user_online');
            $this->dropTable('user_online');
        }

    }
    