<?php

class m141020_111544_photo_verification extends CDbMigration
{

    public function up()
    {
        $this->addColumn('user_verification_request', 'photoAttachment', 'varchar(255) NULL AFTER `attachment`');
        $this->addColumn('user_verification_request', 'isPhotoVerification', 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `isMissed`');
        $this->alterColumn('user_verification_request', 'isMissed', 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `isVerified`');
    }

    public function down()
    {
        $this->dropColumn('user_verification_request', 'photoAttachment');
        $this->dropColumn('user_verification_request', 'isPhotoVerification');
    }

}
