<?php

    class m140803_084101_user_points_added extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('user', 'points', 'int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `password`');
        }

        public function down()
        {
            $this->dropColumn('user', 'points');
        }

    }
    