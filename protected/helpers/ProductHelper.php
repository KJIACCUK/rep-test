<?php

    class ProductHelper
    {

        const SCHEMA_DETAIL = 'detail';

        public static function exportCategory(ProductCategory $category)
        {
            return array(
                'productCategoryId' => (int)$category->productCategoryId,
                'name' => (string)$category->name,
                'parentId' => (int)$category->parent,
            );
        }

        public static function exportProduct(Product $product, $schema = '')
        {
            $schema = CommonHelper::parseSchema($schema);
            // base schema
            $data = array(
                'productId' => (int)$product->productId,
                'productCategoryId' => (int)$product->productCategoryId,
                'publisherName' => (string)$product->publisherName,
                'type' => (string)$product->type,
                'name' => (string)$product->name,
                'cost' => (int)$product->cost,
                'dateStart' => $product->dateStart?date(Yii::app()->params['dateTimeFormat'], $product->dateStart):'',
                'dateCreated' => date(Yii::app()->params['dateTimeFormat'], $product->dateCreated)
            );

            if(in_array(self::SCHEMA_DETAIL, $schema))
            {
                $data += array(
                    'productCategoryName' => (string)$product->category->name,
                    'description' => (string)$product->description,
                    'receiptAddress' => (string)$product->receiptAddress
                );
            }

            return $data;
        }

        public static function exportImages($productImages, $image)
        {
            $data = array();
            foreach($productImages as $item)
            {
                /* @var $item ProductImage */
                $data[] = CommonHelper::getImageLink($item->image, $image);
            }

            return $data;
        }

        public static function getCategoriesToList()
        {
            $result = array(
                '' => Yii::t('application', 'Все товары')
            );
            $categories = ProductCategory::model()->findAll();
            /* @var $categories ProductCategory[] */

            foreach($categories as $item)
            {
                $result[$item->productCategoryId] = $item->name;
            }
            return $result;
        }

        public static function getCategoriesToEdit($withoutCategoryId = null)
        {
            $result = array(
                '' => ''
            );
            $categories = ProductCategory::model()->findAll();
            /* @var $categories ProductCategory[] */

            foreach($categories as $item)
            {
                if($item->productCategoryId == $withoutCategoryId)
                {
                    continue;
                }
                $result[$item->productCategoryId] = $item->name;
            }
            return $result;
        }

        public static function categoriesToGridList()
        {
            $result = array();
            $categories = ProductCategory::model()->findAll();
            /* @var $categories ProductCategory[] */

            foreach($categories as $item)
            {
                $result[] = array('id' => $item->productCategoryId, 'title' => $item->name);
            }
            return $result;
        }

        public static function typesToGridList()
        {
            return array(
                array('id' => Product::TYPE_WITH_SERTIFICATE, 'title' => Yii::t('application', 'Электронный товар')),
                array('id' => Product::TYPE_WITH_RECEIPT_ADDRESS, 'title' => Yii::t('application', 'С получением по адресу')),
                array('id' => Product::TYPE_WITH_DELIVERY, 'title' => Yii::t('application', 'С доставкой')),
            );
        }

        public static function typeGridValue($value)
        {
            if($value == Product::TYPE_WITH_SERTIFICATE)
            {
                return Yii::t('application', 'Электронный товар');
            }
            elseif($value == Product::TYPE_WITH_RECEIPT_ADDRESS)
            {
                return Yii::t('application', 'С получением по адресу');
            }
            else
            {
                return Yii::t('application', 'С доставкой');
            }
        }

        public static function typesToEdit()
        {
            return array(
                Product::TYPE_WITH_SERTIFICATE => Yii::t('application', 'Электронный товар'),
                Product::TYPE_WITH_RECEIPT_ADDRESS => Yii::t('application', 'С получением по адресу'),
                Product::TYPE_WITH_DELIVERY => Yii::t('application', 'С доставкой')
            );
        }
        
        public static function canPurchaseByTimeout($userId)
        {
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('userId' => $userId));
            $criteria->order = 'dateCreated DESC';
            $lastPurchase = ProductPurchase::model()->find($criteria);
            if ($lastPurchase) {
                $datePurchase = new DateTime();
                $datePurchase->setTimestamp($lastPurchase->dateCreated);
                $now = new DateTime();
                $interval = $now->diff($datePurchase);
                $daysLeft = $interval->days;
                if ($daysLeft <= 30) {
                    return false;
                }
            }
            return true;
        }

    }
    