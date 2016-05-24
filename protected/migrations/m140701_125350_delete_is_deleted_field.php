<?php

    class m140701_125350_delete_is_deleted_field extends CDbMigration
    {

        public function up()
        {
            $this->dropColumn('account', 'isDeleted');
        }

        public function down()
        {
            $this->addColumn('account', 'isDeleted', 'tinyint(1) UNSIGNED NOT NULL AFTER isActive');
        }

    }
    