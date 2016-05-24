<?php

class m141017_135459_address_delivery_change extends CDbMigration
{

    public function up()
    {
        $this->addColumn('delivery_address', 'corp', 'varchar(255) NULL AFTER `home`');
    }

    public function down()
    {
        $this->dropColumn('delivery_address', 'corp');
    }

}
