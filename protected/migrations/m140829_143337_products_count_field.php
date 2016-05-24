<?php

    class m140829_143337_products_count_field extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('product', 'itemsCount', 'int(5) UNSIGNED NOT NULL DEFAULT 0 AFTER `articleCode`');
        }

        public function down()
        {
            $this->dropColumn('product', 'itemsCount');
        }

    }
    