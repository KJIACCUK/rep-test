<?php

    class m140703_083332_modify_verification_request extends CDbMigration
    {

        public function up()
        {
            $this->alterColumn('user_verification_request', 'callTime', 'varchar(5) NOT NULL AFTER `callDate`');
        }

        public function down()
        {
            $this->alterColumn('user_verification_request', 'callTime', 'int(10) NOT NULL AFTER `callDate`');
        }

    }
    