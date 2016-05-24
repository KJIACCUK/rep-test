<?php

    class m140817_131418_event_is_pushed extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('event', 'isPushed', 'tinyint(1) UNSIGNED NOT NULL AFTER `timeEnd`');
        }

        public function down()
        {
            $this->dropColumn('event', 'isPushed');
        }

    }
    