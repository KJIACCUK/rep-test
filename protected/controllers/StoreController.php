<?php

class StoreController extends WebController
{
    public $productsLimit = 50;
    public $purchasesLimit = 50;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            array('ProfileIsFilledFilter'),
            array('ProfileIsVerifiedFilter')
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'detail', 'ordering', 'history'),
                'roles' => array('user'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $selectedProductCategoryId = Web::getParam('productCategoryId', null);

        $criteria = new CDbCriteria();
        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->productsLimit);
        if ($selectedProductCategoryId) {
            $criteria->addColumnCondition(array('productCategoryId' => $selectedProductCategoryId));
        }
        $criteria->addColumnCondition(array('isActive' => 1));
        $criteria->addCondition('itemsCount > 0');

        $products = Product::model()->findAll($criteria);
        /* @var $products Product[] */

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_products_items', array('products' => $products));
        } else {
            $this->render('index', array('selectedProductCategoryId' => $selectedProductCategoryId, 'products' => $products));
        }
    }

    public function actionDetail()
    {
        $criteria = new CDbCriteria();
        $criteria->alias = 'p';
        $criteria->addColumnCondition(array('p.productId' => Web::getParam('productId'), 'p.isActive' => 1));
        $criteria->addCondition('p.itemsCount > 0');
        $criteria->with = array('category', 'images');

        $product = Product::model()->find($criteria);
        /* @var $product Product */

        if (!$product) {
            $this->redirect(array('store/index'));
        }

        $this->render('detail', array('product' => $product));
    }

    public function actionOrdering()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->alias = 'p';
        $criteria->addColumnCondition(array('p.productId' => Web::getParam('productId'), 'p.isActive' => 1));
        $criteria->addCondition('p.itemsCount > 0');

        $product = Product::model()->find($criteria);
        /* @var $product Product */

        $purchaseModel = new ProductPurchase();
        $addressModel = new DeliveryAddress();
        $addressModel->email = $currentUser->email;
        $addressModel->phoneCode = $currentUser->phoneCode;
        $addressModel->phoneNumber = $currentUser->phone;

        if (isset($_POST['ProductPurchase'])) {
            if ($currentUser->points < $product->cost) {
                Web::flashError(Yii::t('application', 'У вас недостаточно баллов для покупки'));
                $this->refresh();
            }

            if (!ProductHelper::canPurchaseByTimeout($currentUser->userId)) {
                Web::flashError(Yii::t('application', 'Совершать покупки можно раз в 30 дней'));
                $this->refresh();
            }

            $purchaseModel->attributes = $_POST['ProductPurchase'];
            $purchaseModel->productId = $product->productId;
            $purchaseModel->userId = $currentUser->userId;
            $purchaseModel->purchaseCode = time().strtoupper(CommonHelper::randomString(5));
            $purchaseModel->pointsCount = $product->cost;
            $purchaseModel->deliveryType = ($product->type == Product::TYPE_WITH_DELIVERY)?ProductPurchase::DELIVERY_TYPE_COMPANY:ProductPurchase::DELIVERY_TYPE_SELF;
            $purchaseModel->dateCreated = time();

            $transaction = Yii::app()->db->beginTransaction();

            if ($purchaseModel->save()) {
                User::model()->updateByPk($currentUser->userId, array('points' => new CDbExpression('points - :points', array(':points' => $product->cost))));
                $product->itemsCount -= 1;
                $product->save(false);

                if ($product->type == Product::TYPE_WITH_DELIVERY) {
                    if (isset($_POST['DeliveryAddress'])) {
                        $addressModel->attributes = $_POST['DeliveryAddress'];
                    }
                    $addressModel->productPurchaseId = $purchaseModel->productPurchaseId;

                    if ($addressModel->save()) {
                        EmailHelper::send($currentUser->email, EmailHelper::TYPE_PURCHASE_WITH_DELIVERY, array('user' => $currentUser, 'product' => $product, 'purchaseCode' => $purchaseModel->purchaseCode, 'address' => $addressModel));

                        $transaction->commit();
                        Web::flashSuccess(Yii::t('application', 'Заказ успешно оформлен'));
                        $this->redirect(array('store/index'));
                    }
                } else {
                    if ($product->type == Product::TYPE_WITH_SERTIFICATE) {
                        EmailHelper::send($currentUser->email, EmailHelper::TYPE_PURCHASE_WITH_SERTIFICATE, array('user' => $currentUser, 'product' => $product, 'purchaseCode' => $purchaseModel->purchaseCode));
                    } else {
                        EmailHelper::send($currentUser->email, EmailHelper::TYPE_PURCHASE_WITH_RECEIPT_ADDRESS, array('user' => $currentUser, 'product' => $product, 'purchaseCode' => $purchaseModel->purchaseCode));
                    }
                    $transaction->commit();
                    Web::flashSuccess(Yii::t('application', 'Заказ успешно оформлен'));
                    $this->redirect(array('store/index'));
                }
            }

            $transaction->rollback();
        }

        $this->render('ordering', array('product' => $product, 'purchaseModel' => $purchaseModel, 'addressModel' => $addressModel));
    }

    public function actionHistory()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->purchasesLimit);
        $criteria->addColumnCondition(array('userId' => $currentUser->userId));
        $criteria->with = array('product');

        $purchases = ProductPurchase::model()->findAll($criteria);
        /* @var $purchases ProductPurchase[] */

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_history_items', array('purchases' => $purchases));
        } else {
            $this->render('history', array('purchases' => $purchases));
        }
    }
}