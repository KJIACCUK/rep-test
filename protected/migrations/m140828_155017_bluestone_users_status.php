<?php

    class m140828_155017_bluestone_users_status extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('user', 'isBluestone', 'tinyint(1) UNSIGNED NOT NULL AFTER `isVerified`');
        }

        public function down()
        {
            $this->dropColumn('user', 'isBluestone');
        }

    }
    