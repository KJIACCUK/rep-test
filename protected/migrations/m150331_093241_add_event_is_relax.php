<?php

class m150331_093241_add_event_is_relax extends CDbMigration
{

    public function up()
    {
        $this->addColumn('event', 'relaxId', 'varchar(255) NULL AFTER `isGlobal`');
        $this->addColumn('event', 'relaxUrl', 'text NULL AFTER `relaxId`');
        $this->addColumn('event', 'relaxParsingErrors', 'text NULL AFTER `relaxUrl`');
        $this->alterColumn('event', 'timeEnd', 'varchar(5) NULL AFTER `timeStart`');
    }

    public function down()
    {
        $this->dropColumn('event', 'relaxId');
        $this->dropColumn('event', 'relaxUrl');
        $this->dropColumn('event', 'relaxParsingErrors');
        $this->alterColumn('event', 'timeEnd', 'varchar(5) NOT NULL AFTER `timeStart`');
    }
}