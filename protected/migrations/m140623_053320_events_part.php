<?php

    class m140623_053320_events_part extends CDbMigration
    {

        public function up()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->execute('CREATE TABLE `event` (
                                    `eventId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `category` varchar(255) NOT NULL,
                                    `publisherName` varchar(255) NOT NULL,
                                    `name` varchar(255) NOT NULL,
                                    `image` varchar(255) NOT NULL,
                                    `description` text NOT NULL,
                                    `city` varchar(255) NOT NULL,
                                    `place` text DEFAULT NULL,
                                    `latitude` float(8,5) DEFAULT NULL,
                                    `longitude` float(8,5) DEFAULT NULL,
                                    `isPublic` tinyint(1) UNSIGNED NOT NULL,
                                    `isGlobal` tinyint(1) UNSIGNED NOT NULL,
                                    `status` varchar(20) NOT NULL,
                                    `dateStart` int(10) UNSIGNED NOT NULL,
                                    `timeStart` varchar(5) NOT NULL,
                                    `timeEnd` varchar(5) NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`eventId`)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `event_comment` (
                                    `eventCommentId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `eventId` int(11) UNSIGNED NOT NULL,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `content` text NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`eventCommentId`),
                                    CONSTRAINT `fk_event_comment_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_event_comment_event_1` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_event_comment_user_1` (userId),
                                    INDEX `fk_event_comment_event_1` (eventId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `event_gallery_album` (
                                    `eventGalleryAlbumId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `eventId` int(11) UNSIGNED NOT NULL,
                                    `name` varchar(255) NOT NULL,
                                    `isDefault` tinyint(1) UNSIGNED NOT NULL DEFAULT "0",
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`eventGalleryAlbumId`),
                                    CONSTRAINT `fk_event_gallery_album_event_1` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_event_gallery_album_event_1` (eventId)
                            ) ENGINE=`InnoDB` AUTO_INCREMENT=1 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `event_gallery_image` (
                                    `eventGalleryImageId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `eventId` int(11) UNSIGNED NOT NULL,
                                    `eventGalleryAlbumId` int(11) UNSIGNED NOT NULL,
                                    `image` varchar(255) NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`eventGalleryImageId`),
                                    CONSTRAINT `fk_event_gallery_image_event_gallery_album_1` FOREIGN KEY (`eventGalleryAlbumId`) REFERENCES `event_gallery_album` (`eventGalleryAlbumId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_event_gallery_image_event_1` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_event_gallery_image_event_gallery_album_1` (eventGalleryAlbumId),
                                    INDEX `fk_event_gallery_image_event_1` (eventId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `event_user` (
                                    `eventUserId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `eventId` int(11) UNSIGNED NOT NULL,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    `dateCreated` int(10) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`eventUserId`),
                                    CONSTRAINT `fk_event_user_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_event_user_event_1` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_event_user_user_1` (userId),
                                    INDEX `fk_event_user_event_1` (eventId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('CREATE TABLE `event_user_invite` (
                                    `eventUserInviteId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `eventId` int(11) UNSIGNED NOT NULL,
                                    `userId` int(11) UNSIGNED NOT NULL,
                                    PRIMARY KEY (`eventUserInviteId`),
                                    CONSTRAINT `fk_event_user_invite_user_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    CONSTRAINT `fk_event_user_invite_event_1` FOREIGN KEY (`eventId`) REFERENCES `event` (`eventId`)   ON UPDATE CASCADE ON DELETE CASCADE,
                                    INDEX `fk_event_user_invite_user_1` (userId),
                                    INDEX `fk_event_user_invite_event_1` (eventId)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
        }

        public function down()
        {
            $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
            $this->dropTable('event');
            $this->dropTable('event_comment');
            $this->dropTable('event_gallery_album');
            $this->dropTable('event_gallery_image');
            $this->dropTable('event_user');
            $this->dropTable('event_user_invite');
            $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }
    