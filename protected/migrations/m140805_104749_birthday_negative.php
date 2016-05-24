<?php

    class m140805_104749_birthday_negative extends CDbMigration
    {

        public function up()
        {
            $this->alterColumn('user', 'birthday', 'int(10) NULL AFTER `phoneCode`');
        }

        public function down()
        {
            $this->alterColumn('user', 'birthday', 'int(10) UNSIGNED NULL AFTER `phoneCode`');
        }

    }
    