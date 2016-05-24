<?php

    class m140617_121016_change_user extends CDbMigration
    {

        public function up()
        {
            $this->alterColumn('user', 'login', 'VARCHAR(255) NULL');
            $this->alterColumn('user', 'password', 'VARCHAR(32) NULL');
        }

        public function down()
        {
            $this->alterColumn('user', 'login', 'VARCHAR(255) NOT NULL');
            $this->alterColumn('user', 'password', 'VARCHAR(32) NOT NULL');
        }

    }
    