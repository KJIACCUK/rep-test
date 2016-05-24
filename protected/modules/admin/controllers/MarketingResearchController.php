<?php

    class MarketingResearchController extends AdminController
    {

        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl',
                'postOnly + delete'
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
                    'actions' => array('index', 'create', 'update', 'delete', 'push', 'statistics', 'textAnswers', 'uploadImage'),
                    'roles' => array('admin', 'moderator'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        /**
         * Lists all models.
         */
        public function actionIndex()
        {
            $model = new MarketingResearch('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['MarketingResearch']))
            {
                $model->attributes = $_GET['MarketingResearch'];
            }

            $this->render('index', array(
                'model' => $model,
            ));
        }

        /**
         * Creates a new model.
         * If creation is successful, the browser will be redirected to the 'view' page.
         */
        public function actionCreate()
        {
            $model = new MarketingResearch();

            if(isset($_POST['MarketingResearch']))
            {
                $model->attributes = $_POST['MarketingResearch'];
                if(isset($_POST['MarketingResearchVariant']))
                {
                    $model->variantsData = $_POST['MarketingResearchVariant'];
                }
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Маркетинговое исследование добавлено'));
                    $this->redirect(array('index'));
                }
            }

            $this->render('create', array(
                'model' => $model,
            ));
        }

        /**
         * Updates a particular model.
         * If update is successful, the browser will be redirected to the 'view' page.
         * @param integer $id the ID of the model to be updated
         */
        public function actionUpdate($id)
        {
            $model = $this->loadModel($id);

            foreach($model->variants as $variant)
            {
                $model->variantsData[$variant->marketingResearchVariantId] = $variant->variant;
            }

            if(isset($_POST['MarketingResearch']))
            {
                $model->attributes = $_POST['MarketingResearch'];
                if(isset($_POST['MarketingResearchVariant']))
                {
                    $model->variantsData = $_POST['MarketingResearchVariant'];
                }
                if($model->save())
                {
                    Web::flashSuccess(Yii::t('application', 'Маркетинговое исследование обновлено'));
                    $this->redirect(array('index'));
                }
            }

            $this->render('update', array(
                'model' => $model,
            ));
        }

        /**
         * Deletes a particular model.
         * If deletion is successful, the browser will be redirected to the 'admin' page.
         * @param integer $id the ID of the model to be deleted
         */
        public function actionDelete($id)
        {
            if(Yii::app()->request->isPostRequest)
            {
                // we only allow deletion via POST request
                $this->loadModel($id)->delete();

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if(!isset($_GET['ajax']))
                {
                    Web::flashSuccess(Yii::t('application', 'Маркетинговое исследование удалено'));
                    $this->redirect(array('index'));
                }
            }
            else
            {
                throw new CHttpException(400, Yii::t('application', 'Невалидный запрос'));
            }
        }
        
        public function actionPush()
        {
            
        }
        
        public function actionUploadImage()
        {
            $allowedMimeTypes = array(
                'image/png',
                'image/gif',
                'image/jpg',
                'image/jpeg',
                'image/jpe',
            );
            $image = CUploadedFile::getInstanceByName('file');
            if($image && in_array($image->getType(), $allowedMimeTypes))
            {
                $path = Yii::getPathOfAlias('webroot.content.images.marketing_researches').DIRECTORY_SEPARATOR;
                $filename = md5(time().$image->getName()).'.'.$image->getExtensionName();
                $image->saveAs($path.$filename);
                print Yii::app()->request->getBaseUrl(true).'/content/images/marketing_researches/'.$filename;
            }
        }
        
        public function actionStatistics()
        {
            $criteria = new CDbCriteria();
            $criteria->with = array('variants');
            $criteria->order = 't.dateCreated DESC';
            $dataProvider = new CActiveDataProvider('MarketingResearch', array(
                'criteria' => $criteria
            ));
            $this->render('statistics', array(
                'dataProvider' => $dataProvider
            ));
        }
        
        public function actionTextAnswers($id)
        {
            $model = $this->loadModel($id);
            
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('marketingResearchId' => $id));
            $criteria->with = array('user');
            $dataProvider = new CActiveDataProvider('MarketingResearchAnswerText', array(
                'criteria' => $criteria
            ));
            $this->render('answers', array(
                'dataProvider' => $dataProvider,
                'model' => $model
            ));
        }

        /**
         * Returns the data model based on the primary key given in the GET variable.
         * If the data model is not found, an HTTP exception will be raised.
         * @param integer $id the ID of the model to be loaded
         * @return MarketingResearch the loaded model
         * @throws CHttpException
         */
        public function loadModel($id)
        {
            $model = MarketingResearch::model()->with('variants')->findByPk($id);
            if($model === null)
            {
                throw new CHttpException(404, Yii::t('application', 'Маркетинговое исследование не найдено'));
            }
            return $model;
        }

    }
    