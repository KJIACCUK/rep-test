<?php

    class m140610_140950_user_part extends CDbMigration
    {

        public function up()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->execute('CREATE TABLE `account` (
                                    `accountId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `type` varchar(10) NOT NULL,
                                    `isActive` tinyint(1) UNSIGNED NOT NULL DEFAULT "1",
                                    `isDeleted` tinyint(1) UNSIGNED NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`accountId`)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `auth_assignment` (
                                    `itemname` varchar(64) CHARACTER SET latin1 NOT NULL,
                                    `userid` varchar(64) CHARACTER SET latin1 NOT NULL,
                                    `bizrule` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
                                    `data` varchar(255) CHARACTER SET latin1 DEFAULT NULL
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `auth_item` (
                                    `name` varchar(64) CHARACTER SET latin1 NOT NULL,
                                    `type` int(11) UNSIGNED NOT NULL,
                                    `description` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
                                    `bizrule` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
                                    `data` varchar(255) CHARACTER SET latin1 DEFAULT NULL
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `auth_item_child` (
                                    `parent` varchar(64) CHARACTER SET latin1 NOT NULL,
                                    `child` varchar(64) CHARACTER SET latin1 NOT NULL
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `employee` (
                                    `employeeId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `accountId` int(11) UNSIGNED NOT NULL,
                                    `name` varchar(255) NOT NULL,
                                    `email` varchar(255) NOT NULL,
                                    `login` varchar(255) NOT NULL,
                                    `password` varchar(32) NOT NULL,
                                    PRIMARY KEY (`employeeId`),
                                    CONSTRAINT `fk_employee_account_1` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_employee_account_1` (accountId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user` (
                                    `userId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `accountId` int(11) UNSIGNED NOT NULL,
                                    `name` varchar(255) DEFAULT NULL,
                                    `email` varchar(255) DEFAULT NULL,
                                    `phone` varchar(255) DEFAULT NULL,
                                    `phoneCode` varchar(2) DEFAULT NULL,
                                    `birthday` int(10) UNSIGNED DEFAULT NULL,
                                    `image` varchar(255) DEFAULT NULL,
                                    `messenger` varchar(10) DEFAULT NULL,
                                    `messengerLogin` varchar(255) DEFAULT NULL,
                                    `favoriteMusicGenre` varchar(255) DEFAULT NULL,
                                    `favoriteCigaretteBrand` varchar(255) DEFAULT NULL,
                                    `login` varchar(255) NOT NULL,
                                    `password` varchar(32) NOT NULL,
                                    `xmppChatLogin` varchar(255) NOT NULL,
                                    `xmppChatPassword` varchar(255) NOT NULL,
                                    `isFilled` tinyint(1) UNSIGNED NOT NULL DEFAULT "0",
                                    `isVerified` tinyint(1) UNSIGNED NOT NULL DEFAULT "0",
                                    PRIMARY KEY (`userId`),
                                    CONSTRAINT `fk_user_account_1` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_account_1` (accountId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_api_token` (
                                    `userApiTokenId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `platform` varchar(10) NOT NULL,
                                    `token` varchar(255) NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`userApiTokenId`),
                                    CONSTRAINT `fk_user_api_token_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_api_token_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_friend` (
                                    `userFriendId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `friendId` int(11) UNSIGNED NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`userFriendId`),
                                    CONSTRAINT `fk_user_friend_user_2` FOREIGN KEY (`friendId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_user_friend_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_friend_user_2` (friendId),
                                    INDEX `fk_user_friend_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_friend_request` (
                                    `userFriendRequestId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `recipientId` int(11) UNSIGNED NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`userFriendRequestId`),
                                    CONSTRAINT `fk_user_friend_request_user_2` FOREIGN KEY (`recipientId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_user_friend_request_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_friend_request_user_2` (recipientId),
                                    INDEX `fk_user_friend_request_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_push_token` (
                                    `userPushTokenId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `apiToken` varchar(32) NOT NULL,
                                    `platform` varchar(10) NOT NULL,
                                    `pushToken` text NOT NULL,
                                    `dateCreated` int(11) NOT NULL,
                                    PRIMARY KEY (`userPushTokenId`),
                                    CONSTRAINT `fk_user_push_token_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_push_token_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_social` (
                                    `userSocialId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `type` varchar(10) NOT NULL,
                                    `socialId` varchar(255) NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`userSocialId`),
                                    CONSTRAINT `fk_user_social_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_social_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_verification` (
                                    `userVerificationId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `employeeId` int(11) UNSIGNED NOT NULL,
                                    `comment` text DEFAULT NULL,
                                    `attachmentFilePath` varchar(255) DEFAULT NULL,
                                    `dateCreated` int(11) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`userVerificationId`),
                                    CONSTRAINT `fk_user_verification_employee_1` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_user_verification_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_verification_employee_1` (employeeId),
                                    INDEX `fk_user_verification_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `user_verification_request` (
                                    `userVerificationRequestId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `employeeId` int(11) UNSIGNED DEFAULT NULL,
                                    `messenger` varchar(10) NOT NULL,
                                    `messengerLogin` varchar(255) NOT NULL,
                                    `callDate` int(10) UNSIGNED NOT NULL,
                                    `callTime` int(10) UNSIGNED NOT NULL,
                                    `status` varchar(10) NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    `dateClosed` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`userVerificationRequestId`),
                                    CONSTRAINT `fk_user_verification_request_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_user_verification_request_user_1` (userId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            
            
            $this->execute("INSERT INTO `auth_item` VALUES ('admin', '2', 'Administrator', null, null), ('moderator', '2', 'Moderator', null, null), ('operator', '2', 'Operator', null, null), ('user', '2', 'User', null, null);");
            $this->execute("INSERT INTO `auth_item_child` VALUES ('admin', 'guest'), ('moderator', 'guest'), ('operator', 'guest'), ('user', 'guest');");
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
        }

        public function down()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->dropTable('account');
            $this->dropTable('auth_assignment');
            $this->dropTable('auth_item');
            $this->dropTable('auth_item_child');
            $this->dropTable('employee');
            $this->dropTable('user');
            $this->dropTable('user_api_token');
            $this->dropTable('user_friend');
            $this->dropTable('user_friend_request');
            $this->dropTable('user_push_token');
            $this->dropTable('user_social');
            $this->dropTable('user_verification');
            $this->dropTable('user_verification_request');
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }
    