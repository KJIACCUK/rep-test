<?php

    class m140801_165741_add_event_product_id extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('event', 'productId', 'int(11) UNSIGNED NULL AFTER `isGlobal`');
        }

        public function down()
        {
            $this->dropColumn('event', 'productId');
        }

    }
    