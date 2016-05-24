<?php

    class m140813_095523_alter_verification_request extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('user_verification_request', 'comment', 'text NOT NULL AFTER `status`');
            $this->addColumn('user_verification_request', 'attachment', 'varchar(255) NULL AFTER `comment`');
            $this->addColumn('user_verification_request', 'isVerified', 'tinyint(1) UNSIGNED NOT NULL AFTER `attachment`'); 
        }

        public function down()
        {
            $this->dropColumn('user_verification_request', 'comment');
            $this->dropColumn('user_verification_request', 'isVerified');
            $this->dropColumn('user_verification_request', 'attachment');
        }

    }
    