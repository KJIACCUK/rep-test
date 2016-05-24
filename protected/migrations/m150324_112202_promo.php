<?php

class m150324_112202_promo extends CDbMigration
{

    public function up()
    {
        $this->execute('CREATE TABLE `promo_code` (
                                `promoCodeId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                                `code` varchar(255) NOT NULL,
                                `userId` int(11) UNSIGNED NULL,
                                `status` enum("free","activated") NOT NULL,
                                `pointsActivated` int(11) UNSIGNED NOT NULL DEFAULT 0,
                                `dateCreated` int(10) UNSIGNED NOT NULL,
                                `dateActivated` int(10) UNSIGNED NOT NULL,
                                PRIMARY KEY (`promoCodeId`)
                            ) ENGINE=`InnoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ROW_FORMAT=COMPACT CHECKSUM=0 DELAY_KEY_WRITE=0;');
        
        $this->insert('global_setting', array('name' => 'promo_points_per_code', 'value' => '50'));
    }

    public function down()
    {
        $this->dropTable('promo_code');
    }
}