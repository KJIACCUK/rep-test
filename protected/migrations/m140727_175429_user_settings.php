<?php

    class m140727_175429_user_settings extends CDbMigration
    {

        public function up()
        {
            $this->execute("
                CREATE TABLE `user_notification_setting` ( 
                    `userNotificationSettingId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `settingKey` varchar(255) NOT NULL,
                    `isChecked` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
                    PRIMARY KEY (`userNotificationSettingId`)  
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->addForeignKey('fk_user_notification_setting_user_1', 'user_notification_setting', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_user_notification_setting_user_1', 'user_notification_setting');
            $this->dropTable('user_notification_setting');
        }

    }
    