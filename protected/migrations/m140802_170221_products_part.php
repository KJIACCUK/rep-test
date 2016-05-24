<?php

    class m140802_170221_products_part extends CDbMigration
    {

        public function up()
        {
            $this->execute("
                CREATE TABLE `product` (
                    `productId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `productCategoryId` int(11) UNSIGNED NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `description` text NOT NULL,
                    `image` varchar(255) NOT NULL,
                    `cost` int(11) UNSIGNED NOT NULL,
                    `type` varchar(50) NOT NULL,
                    `attachment` varchar(255) NULL,
                    `receiptAddress` text NULL,
                    `articleCode` varchar(255) NULL,
                    `isActive` tinyint(1) UNSIGNED NOT NULL,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`productId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");
            
            $this->execute("
                CREATE TABLE `product_category` (
                    `productCategoryId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `parent` int(11) UNSIGNED NULL,
                    `level` int(11) UNSIGNED NOT NULL DEFAULT 0,
                    PRIMARY KEY (`productCategoryId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");
            
            $this->execute("
                CREATE TABLE `product_image` (
                    `productImageId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `productId` int(11) UNSIGNED NOT NULL,
                    `image` varchar(255) NOT NULL,
                    PRIMARY KEY (`productImageId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");
            
            $this->execute("
                CREATE TABLE `product_purchase` (
                    `productPurchaseId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `productId` int(11) UNSIGNED NOT NULL,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `pointsCount` int(11) UNSIGNED NOT NULL,
                    `deliveryType` varchar(50) NOT NULL,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`productPurchaseId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");
            
            $this->execute("
                CREATE TABLE `delivery_address` (
                    `deliveryAddressId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `productPurchaseId` int(11) UNSIGNED NOT NULL,
                    `postIndex` varchar(255) NOT NULL,
                    `city` varchar(255) NOT NULL,
                    `street` varchar(255) NOT NULL,
                    `home` varchar(255) NOT NULL,
                    `apartment` varchar(255) NULL,
                    `email` varchar(255) NOT NULL,
                    `phone` varchar(255) NOT NULL,
                    PRIMARY KEY (`deliveryAddressId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");
            
            $this->addForeignKey('fk_product_product_category_1', 'product', 'productCategoryId', 'product_category', 'productCategoryId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_product_category_product_category_1', 'product_category', 'parent', 'product_category', 'productCategoryId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_product_image_product_1', 'product_image', 'productId', 'product', 'productId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_product_purchase_product_1', 'product_purchase', 'productId', 'product', 'productId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_product_purchase_user_1', 'product_purchase', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_delivery_address_product_purchase_1', 'delivery_address', 'productPurchaseId', 'product_purchase', 'productPurchaseId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_product_product_category_1', 'product');
            $this->dropForeignKey('fk_product_category_product_category_1', 'product_category');
            $this->dropForeignKey('fk_product_image_product_1', 'product_image');
            $this->dropForeignKey('fk_product_purchase_product_1', 'product_purchase');
            $this->dropForeignKey('fk_product_purchase_user_1', 'product_purchase');
            $this->dropForeignKey('fk_delivery_address_product_purchase_1', 'delivery_address');
            
            $this->dropTable('product');
            $this->dropTable('product_category');
            $this->dropTable('product_image');
            $this->dropTable('product_purchase');
            $this->dropTable('delivery_address');
        }

    }
    