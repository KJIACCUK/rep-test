<?php

    class m140724_130243_user_message_table extends CDbMigration
    {

        public function up()
        {
            $this->execute("
                CREATE TABLE `user_message` ( 
                    `userMessageId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `recipientId` int(11) UNSIGNED NOT NULL,
                    `message` text NOT NULL,
                    `isReaded` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`userMessageId`)  ) 
                ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->addForeignKey('fk_user_message_user_1', 'user_message', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_user_message_user_2', 'user_message', 'recipientId', 'user', 'userId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_user_message_user_1', 'user_message');
            $this->dropForeignKey('fk_user_message_user_2', 'user_message');
            $this->dropTable('user_message');
        }

    }
    