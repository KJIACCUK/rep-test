<?php

class m150406_121036_add_deleted_relax_events extends CDbMigration
{

    public function up()
    {
        $this->execute("
                CREATE TABLE `event_relax_deleted` ( 
                `eventRelaxDeletedId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
                `relaxId` varchar(255) NOT NULL,
                PRIMARY KEY (`eventRelaxDeletedId`)  
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
        ");
    }

    public function down()
    {
        $this->dropTable('event_relax_deleted');
    }
}