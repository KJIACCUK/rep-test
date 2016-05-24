<?php

    class m140803_185932_product_purchase_code extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('product_purchase', 'purchaseCode', 'varchar(255) NOT NULL AFTER `userId`');
        }

        public function down()
        {
            $this->dropColumn('product_purchase', 'purchaseCode');
        }

    }
    