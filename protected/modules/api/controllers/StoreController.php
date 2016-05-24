<?php

    class StoreController extends ApiController
    {

        public function filters()
        {
            return array(
                array('ApiAccessControlFilter'),
                array('ApiProfileIsFilledFilter'),
                array('ApiProfileIsVerifiedFilter'),
                array(
                    'ApiParamFilter + getProducts, getProduct',
                    'param' => 'image',
                    'function' => 'FilterHelper::checkImage'
                ),
                array(
                    'ApiParamFilter + getProduct',
                    'param' => 'galleryImage',
                    'function' => 'FilterHelper::checkImage'
                ),
                array(
                    'ApiParamFilter + getProduct, buyProduct',
                    'param' => 'productId',
                    'function' => 'FilterHelper::checkProductExists'
                )
            );
        }

        public function actionGetCategories()
        {
            $categories = ProductCategory::model()->findAll();
            /* @var $categories ProductCategory[] */
            $response['categories'] = array();
            foreach($categories as $item)
            {
                $response['categories'][] = ProductHelper::exportCategory($item);
            }

            Api::respondSuccess($response);
        }

        public function actionGetProducts()
        {
            $currentUser = $this->getUser();
            $criteria = new CDbCriteria();
            $criteria->offset = Api::getParam('offset', 0);
            $criteria->limit = Api::getParam('limit', 50);
            if(($productCategoryId = Api::getParam('productCategoryId')))
            {
                $criteria->addColumnCondition(array('productCategoryId' => $productCategoryId));
            }
            $criteria->addColumnCondition(array('isActive' => 1));
            $criteria->addCondition('itemsCount > 0');

            $products = Product::model()->findAll($criteria);
            /* @var $products Product[] */
            $response['products'] = array();

            foreach($products as $item)
            {
                $data = ProductHelper::exportProduct($item);
                $data['image'] = CommonHelper::getImageLink($item->image, Api::getParam('image'));
                $response['products'][] = $data;
            }
            
            $response['total'] = Product::model()->count($criteria);

            Api::respondSuccess($response);
        }

        public function actionGetProduct()
        {
            $criteria = new CDbCriteria();
            $criteria->alias = 'p';
            $criteria->addColumnCondition(array('p.productId' => Api::getParam('productId'), 'p.isActive' => 1));
            $criteria->addCondition('p.itemsCount > 0');
            $criteria->with = array('images');

            $product = Product::model()->find($criteria);
            if (!$product) {
                Api::respondError(404, Yii::t('application', 'Товар не найден'));
            }
            /* @var $product Product */
            $response['product'] = ProductHelper::exportProduct($product, 'detail');
            $response['product']['image'] = CommonHelper::getImageLink($product->image, Api::getParam('image'));
            $response['product']['gallery'] = ProductHelper::exportImages($product->images, Api::getParam('galleryImage'));

            Api::respondSuccess($response);
        }

        public function actionBuyProduct()
        {
            $currentUser = $this->getUser();
            $criteria = new CDbCriteria();
            $criteria->alias = 'p';
            $criteria->addColumnCondition(array('productId' => Api::getParam('productId'), 'isActive' => 1));
            $criteria->addCondition('p.itemsCount > 0');

            $product = Product::model()->find($criteria);
            /* @var $product Product */

            if($currentUser->points < $product->cost)
            {
                Api::respondError(Api::CODE_BAD_REQUEST, Yii::t('application', 'У вас недостаточно баллов для покупки'));
            }
            
            if(!ProductHelper::canPurchaseByTimeout($currentUser->userId))
            {
                Api::respondError(Api::CODE_TOO_MUCH_PURCHASES, Yii::t('application', 'Совершать покупки можно раз в 30 дней'));
            }

            $purchaseModel = new ProductPurchase('api_insert');
            $purchaseModel->productId = $product->productId;
            $purchaseModel->userId = $currentUser->userId;
            $purchaseModel->purchaseCode = time().strtoupper(CommonHelper::randomString(5));
            $purchaseModel->pointsCount = $product->cost;
            $purchaseModel->deliveryType = ($product->type == Product::TYPE_WITH_DELIVERY)?ProductPurchase::DELIVERY_TYPE_COMPANY:ProductPurchase::DELIVERY_TYPE_SELF;
            $purchaseModel->comment = Api::getParam('comment');
            $purchaseModel->dateCreated = time();

            $transaction = Yii::app()->db->beginTransaction();

            if(!$purchaseModel->save())
            {
                $transaction->rollback();
                Api::respondValidationError($purchaseModel);
            }

            if($product->type == Product::TYPE_WITH_DELIVERY)
            {
                $addressModel = new DeliveryAddress('api_insert');
                $addressModel->attributes = Api::getParams(array('postIndex', 'city', 'street', 'home', 'corp', 'apartment'));
                $addressModel->productPurchaseId = $purchaseModel->productPurchaseId;
                $addressModel->email = $currentUser->email;
                $addressModel->phoneCode = $currentUser->phoneCode;
                $addressModel->phoneNumber = $currentUser->phone;

                if(!$addressModel->save())
                {
                    $transaction->rollback();
                    Api::respondValidationError($addressModel);
                }
            }

            switch($product->type)
            {
                case Product::TYPE_WITH_SERTIFICATE:
                    EmailHelper::send($currentUser->email, EmailHelper::TYPE_PURCHASE_WITH_SERTIFICATE, array('user' => $currentUser, 'product' => $product, 'purchaseCode' => $purchaseModel->purchaseCode));
                    break;

                case Product::TYPE_WITH_RECEIPT_ADDRESS:
                    EmailHelper::send($currentUser->email, EmailHelper::TYPE_PURCHASE_WITH_RECEIPT_ADDRESS, array('user' => $currentUser, 'product' => $product, 'purchaseCode' => $purchaseModel->purchaseCode));
                    break;

                case Product::TYPE_WITH_DELIVERY:
                    EmailHelper::send($currentUser->email, EmailHelper::TYPE_PURCHASE_WITH_DELIVERY, array('user' => $currentUser, 'product' => $product, 'purchaseCode' => $purchaseModel->purchaseCode, 'address' => $addressModel));
                    break;
            }

            User::model()->updateByPk($currentUser->userId, array('points' => new CDbExpression('points - :points', array(':points' => $product->cost))));
            $product->itemsCount -= 1;
            $product->save(false);
            $transaction->commit();
            Api::respondSuccess(array('purchaseCode' => $purchaseModel->purchaseCode));
        }

        public function actionGetPurchaseHistory()
        {
            $currentUser = $this->getUser();
            $criteria = new CDbCriteria();
            $criteria->offset = Api::getParam('offset', 0);
            $criteria->limit = Api::getParam('limit', 50);
            $criteria->addColumnCondition(array('userId' => $currentUser->userId));
            $criteria->with = array('product');

            $purchases = ProductPurchase::model()->findAll($criteria);
            /* @var $purchases ProductPurchase[] */
            $response['purchases'] = array();

            foreach($purchases as $item)
            {
                $data = ProductHelper::exportProduct($item->product);
                $data['cost'] = $item->pointsCount;
                $data['dateCreated'] = $item->dateCreated;
                $data['purchaseCode'] = $item->purchaseCode;
                $data['image'] = CommonHelper::getImageLink($item->product->image, Api::getParam('image'));
                $response['purchases'][] = $data;
            }
            
            $response['total'] = ProductPurchase::model()->count($criteria);

            Api::respondSuccess($response);
        }

    }
    