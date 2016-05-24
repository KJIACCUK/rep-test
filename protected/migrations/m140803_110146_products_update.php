<?php

    class m140803_110146_products_update extends CDbMigration
    {

        public function up()
        {
            $this->addColumn('product', 'publisherName', 'varchar(255) NULL AFTER `productCategoryId`');
            $this->addColumn('product', 'dateStart', 'int(10) UNSIGNED NULL AFTER `isActive`');
        }

        public function down()
        {
            $this->dropColumn('product', 'publisherName');
            $this->dropColumn('product', 'dateStart');
        }

    }
    