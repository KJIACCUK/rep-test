<?php

class m141016_144453_verification extends CDbMigration
{

    public function up()
    {
        $this->addColumn('user_verification_request', 'isMissed', 'tinyint(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `isVerified`');
        $this->execute("
            CREATE TABLE `global_setting` ( 
                `globalSettingId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
                `name` varchar(255) NOT NULL, 
                `value` varchar(255) NULL, 
                PRIMARY KEY (`globalSettingId`)  
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
        ");
        
        $this->insert('global_setting', array('name' => 'operator_monday_start_time', 'value' => '14:00'));
        $this->insert('global_setting', array('name' => 'operator_monday_end_time', 'value' => '20:00'));
        $this->insert('global_setting', array('name' => 'operator_tuesday_start_time', 'value' => '14:00'));
        $this->insert('global_setting', array('name' => 'operator_tuesday_end_time', 'value' => '20:00'));
        $this->insert('global_setting', array('name' => 'operator_wednesday_start_time', 'value' => '14:00'));
        $this->insert('global_setting', array('name' => 'operator_wednesday_end_time', 'value' => '20:00'));
        $this->insert('global_setting', array('name' => 'operator_thursday_start_time', 'value' => '14:00'));
        $this->insert('global_setting', array('name' => 'operator_thursday_end_time', 'value' => '20:00'));
        $this->insert('global_setting', array('name' => 'operator_friday_start_time', 'value' => '14:00'));
        $this->insert('global_setting', array('name' => 'operator_friday_end_time', 'value' => '20:00'));
        $this->insert('global_setting', array('name' => 'operator_saturday_start_time', 'value' => '11:00'));
        $this->insert('global_setting', array('name' => 'operator_saturday_end_time', 'value' => '14:00'));
        $this->insert('global_setting', array('name' => 'operator_sunday_start_time', 'value' => '11:00'));
        $this->insert('global_setting', array('name' => 'operator_sunday_end_time', 'value' => '14:00'));
    }

    public function down()
    {
        $this->dropColumn('user_verification_request', 'isMissed');
        $this->dropTable('global_setting');
    }

}
