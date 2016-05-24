<?php

    class m140803_162747_product_purchase_update extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('product_purchase', 'comment', 'text NULL AFTER `deliveryType`');
        }

        public function down()
        {
            $this->dropColumn('product_purchase', 'comment');
        }

    }
    