<?php

    class StoreController extends AdminController
    {

        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl', // perform access control for CRUD operations
                'postOnly + delete', // we only allow deletion via POST request
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
                    'actions' => array('index', 'indexCategory', 'createCategory', 'updateCategory', 'deleteCategory',
                        'create', 'update', 'delete', 'gallery', 'deleteImage', 'orders', 'orderView', 'export'),
                    'roles' => array('admin', 'moderator'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndexCategory()
        {
            $model = new ProductCategory('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['ProductCategory']))
            {
                $model->attributes = $_GET['ProductCategory'];
            }
            
            $this->render('index_category', array(
                'model' => $model,
            ));
        }
        
        public function actionCreateCategory()
        {
            $model = new ProductCategory();

            if(isset($_POST['ProductCategory']))
            {
                $model->attributes = $_POST['ProductCategory'];
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Категория добавлена'));
                    $this->redirect(array('indexCategory'));
                }
            }

            $this->render('create_category', array(
                'model' => $model,
            ));
        }

        public function actionUpdateCategory($id)
        {
            $model = $this->loadCategory($id);

            if(isset($_POST['ProductCategory']))
            {
                $model->attributes = $_POST['ProductCategory'];
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Категория обновлена'));
                    $this->redirect(array('indexCategory'));
                }
            }

            $this->render('update_category', array(
                'model' => $model,
            ));
        }

        public function actionDeleteCategory($id)
        {
            if(Yii::app()->request->isPostRequest)
            {
                $category = $this->loadCategory($id);
                if(count($category->childsCategories) == 0)
                {
                    Web::flashSuccess(Yii::t('application', 'Категория удалена'));
                    $category->delete();
                }
                else
                {
                    throw new CHttpException(400, Yii::t('application', 'Нельзя удалять категории, у которых есть вложенные категории'));
                }

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                {
                    $this->redirect(array('indexCategory'));
                }
            }
            else
            {
                throw new CHttpException(400, Yii::t('application', 'Невалидный запрос'));
            }
        }

        public function actionIndex()
        {
            $model = new Product('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['Product']))
            {
                $model->attributes = $_GET['Product'];
            }

            $this->render('index', array(
                'model' => $model,
            ));
        }

        public function actionCreate()
        {
            $model = new Product();
            $model->isActive = 1;

            if(isset($_POST['Product']))
            {
                $model->attributes = $_POST['Product'];
//                if($model->type == Product::TYPE_WITH_SERTIFICATE)
//                {
//                    $model->setScenario('insert_with_sertificate');
//                }
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Товар добавлен'));
                    $this->redirect(array('index'));
                }
            }

            if($model->dateStart)
            {
                $model->dateStart = date(Yii::app()->params['dateFormat'], $model->dateStart);
            }

            $this->render('create', array(
                'model' => $model,
            ));
        }

        public function actionUpdate($id)
        {
            $model = $this->loadProduct($id);

            if(isset($_POST['Product']))
            {
                $model->attributes = $_POST['Product'];
//                if($model->type == Product::TYPE_WITH_SERTIFICATE)
//                {
//                    $model->setScenario('update_with_sertificate');
//                }
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Товар обновлен'));
                    $this->redirect(array('index'));
                }
            }

            if($model->dateStart)
            {
                $model->dateStart = date(Yii::app()->params['dateFormat'], $model->dateStart);
            }
            else
            {
                $model->dateStart = '';
            }

            $this->render('update', array(
                'model' => $model,
            ));
        }

        public function actionDelete($id)
        {
            if(Yii::app()->request->isPostRequest)
            {
                // we only allow deletion via POST request
                $this->loadProduct($id)->delete();
                Web::flashSuccess(Yii::t('application', 'Товар удален'));

                if(!isset($_GET['ajax']))
                {
                    $this->redirect(array('index'));
                }
            }
            else
            {
                throw new CHttpException(400, Yii::t('application', 'Невалидный запрос'));
            }
        }

        public function actionGallery($id)
        {
            $productImages = ProductImage::model()->findAllByAttributes(array('productId' => $id));

            $model = new ProductImage();
            if(Yii::app()->request->isPostRequest)
            {

                $model->productId = $id;
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Фотография товара добавлена'));
                    $this->refresh();
                }
            }

            $this->render('gallery', array(
                'productImages' => $productImages,
                'model' => $model
            ));
        }

        public function actionDeleteImage($id)
        {
            $model = ProductImage::model()->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Изображение не найдено'));
            }
            $model->delete();
            Web::flashSuccess(Yii::t('application', 'Фотография товара удалена'));
            $this->redirect(array('gallery', 'id' => $model->productId));
        }

        public function actionOrders()
        {
            $criteria = new CDbCriteria();
            $criteria->compare('productPurchaseId', Web::getParam('search'), true, 'OR');
            $criteria->compare('product.name', Web::getParam('search'), true, 'OR');
            $criteria->compare('user.name', Web::getParam('search'), true, 'OR');
            $criteria->compare('purchaseCode', Web::getParam('search'), true, 'OR');

            if(($dateStart = Web::getParam('dateStart')))
            {
                $criteria->addCondition('t.dateCreated >= :dateStart');
                $criteria->params[':dateStart'] = $dateStart;
            }

            if(($dateEnd = Web::getParam('dateEnd')))
            {
                $criteria->addCondition('t.dateCreated <= :dateEnd');
                $criteria->params[':dateEnd'] = $dateEnd;
            }

            $criteria->with = array('product', 'user');
            $criteria->order = 't.dateCreated DESC';


            $dataProvider = new CActiveDataProvider('ProductPurchase', array(
                'criteria' => $criteria
            ));

            $this->render('orders', array(
                'dataProvider' => $dataProvider
            ));
        }
        
        public function actionOrderView($id)
        {
            $order = $this->loadOrder($id);
            $this->render('order_view', array(
                'order' => $order
            ));
            
        }
        
        public function actionExport()
        {
            $model = new ExportOrderForm();
            
            if(isset($_POST['ExportOrderForm']))
            {
                $model->attributes = $_POST['ExportOrderForm'];
                if($model->validate())
                {
                    $model->export();
                }
            }
            
            $this->render('export', array(
                'model' => $model
            ));
        }

        /**
         * 
         * @param integer $id
         * @return Product
         * @throws CHttpException
         */
        public function loadProduct($id)
        {
            $model = Product::model()->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Товар не найден'));
            }
            return $model;
        }

        /**
         * 
         * @param integer $id
         * @return ProductCategory
         * @throws CHttpException
         */
        public function loadCategory($id)
        {
            $model = ProductCategory::model()->with(array('parentCategory', 'childsCategories'))->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Категория не найдена'));
            }
            return $model;
        }
        
        /**
         * 
         * @param integer $id
         * @return ProductPurchase
         * @throws CHttpException
         */
        public function loadOrder($id)
        {
            $model = ProductPurchase::model()->with(array('user', 'product'))->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Заказ не найден'));
            }
            return $model;
        }

    }
    