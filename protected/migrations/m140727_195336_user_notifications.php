<?php

    class m140727_195336_user_notifications extends CDbMigration
    {

        public function up()
        {
            $this->execute("
                CREATE TABLE `user_notification` ( 
                    `userNotificationId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `settingKey` varchar(255) NOT NULL,
                    `params` text NOT NULL,
                    `notificationText` text NOT NULL,
                    `isReaded` tinyint(1) UNSIGNED NOT NULL,
                    `isPushed` tinyint(1) UNSIGNED NOT NULL,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`userNotificationId`)  
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->addForeignKey('fk_user_notification_user_1', 'user_notification', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_user_notification_user_1', 'user_notification');
            $this->dropTable('user_notification');
        }

    }
    