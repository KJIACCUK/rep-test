<?php

class MarketingResearchController extends WebController
{
    public $researchesLimit = 50;

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
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
                'actions' => array('index', 'list', 'detail'),
                'roles' => array('user'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $currentUser = $this->getUser();

        $criteria = new CDbCriteria();
        $criteria->alias = 'a';
        $criteria->join = 'INNER JOIN '.MarketingResearch::model()->tableName().' r ON (a.marketingResearchId = r.marketingResearchId AND r.isEnabled = 1)';
        $criteria->addColumnCondition(array('a.userId' => $currentUser->userId));

        $answeredCount = MarketingResearchUserAnswer::model()->count($criteria);
        $totalCount = MarketingResearch::model()->countByAttributes(array('isEnabled' => 1));

        if ($totalCount == $answeredCount) {

            // last answered

            $criteria = new CDbCriteria();
            $criteria->alias = 'a';
            $criteria->join = 'INNER JOIN '.MarketingResearch::model()->tableName().' r ON (a.marketingResearchId = r.marketingResearchId AND r.isEnabled = 1)';
            $criteria->addColumnCondition(array('a.userId' => $currentUser->userId));
            $criteria->order = 'a.dateCreated DESC, a.marketingResearchUserAnswerId DESC';

            $lastAnswer = MarketingResearchUserAnswer::model()->find($criteria);
            /* @var $lastAnswer MarketingResearchUserAnswer */

            if (!$lastAnswer) {
                throw new CHttpException(404);
            }

            $researchId = $lastAnswer->marketingResearchId;
        } else {

            // last added and not answered

            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('isEnabled' => 1));
            $criteria->addCondition('marketingResearchId NOT IN (SELECT marketingResearchId FROM '.MarketingResearchUserAnswer::model()->tableName().' WHERE userId = :userId)');
            $criteria->params[':userId'] = $currentUser->userId;
            $criteria->order = 'dateCreated DESC, marketingResearchId DESC';

            $lastAdded = MarketingResearch::model()->find($criteria);
            /* @var $lastAdded MarketingResearch */

            if (!$lastAdded) {
                throw new CHttpException(404);
            }

            $researchId = $lastAdded->marketingResearchId;
        }

        $research = $this->findResearchDetail($researchId);
        $nextResearch = $this->findNextResearch($research);
        $this->saveVisit();
        
        if ($research->isAnswered) {
            $stats = MarketingResearchHelper::exportStats($research, $currentUser);
            $this->render('detail', array('research' => $research, 'stats' => $stats, 'nextResearch' => $nextResearch));
        } else {
            $model = new MarketingResearchForm($research->type);
            $model->variants = array_keys($research->variants);

            $this->handleMarketingResearchForm($model, $research);

            $this->render('detail', array('research' => $research, 'model' => $model, 'nextResearch' => $nextResearch));
        }
    }

    public function actionList()
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->alias = 'r';
        $criteria->select = 'r.*, IF(a.userId,1,0) AS isAnsweredInList';
        $criteria->join = 'LEFT JOIN '.MarketingResearchUserAnswer::model()->tableName().' a ON (r.marketingResearchId = a.marketingResearchId AND a.userId = :userId)';
        $criteria->params[':userId'] = $currentUser->userId;
        $criteria->addColumnCondition(array('r.isEnabled' => 1));
        $criteria->offset = Web::getParam('offset', 0);
        $criteria->limit = Web::getParam('limit', $this->researchesLimit);
        $criteria->order = 'isAnsweredInList ASC, r.dateCreated DESC, r.marketingResearchId DESC';

        $researches = MarketingResearch::model()->findAll($criteria);

        $this->saveVisit();

        if (Yii::app()->request->isAjaxRequest) {
            $this->renderPartial('_researches_items', array('researches' => $researches));
        } else {
            $this->render('index', array('researches' => $researches));
        }
    }

    public function actionDetail()
    {
        $currentUser = $this->getUser();
        $research = $this->findResearchDetail(Web::getParam('marketingResearchId'));
        $nextResearch = $this->findNextResearch($research);
        $this->saveVisit();

        if ($research->isAnswered) {
            $stats = MarketingResearchHelper::exportStats($research, $currentUser);
            $this->render('detail', array('research' => $research, 'stats' => $stats, 'nextResearch' => $nextResearch));
        } else {
            $model = new MarketingResearchForm($research->type);
            $model->variants = array_keys($research->variants);

            $this->handleMarketingResearchForm($model, $research);

            $this->render('detail', array('research' => $research, 'model' => $model, 'nextResearch' => $nextResearch));
        }
    }

    public function getViewPath()
    {
        return Yii::app()->getViewPath().DIRECTORY_SEPARATOR.'marketing_research';
    }

    /**
     * 
     * @param MarketingResearch $research
     * @return MarketingResearch
     */
    private function findNextResearch(MarketingResearch $research)
    {
        $nextResearchCriteria = new CDbCriteria();
        $nextResearchCriteria->addColumnCondition(array('isEnabled' => 1));
        $nextResearchCriteria->addCondition('dateCreated < :currentMarketingResearchDateCreated OR marketingResearchId < :currentMarketingResearchId');
        $nextResearchCriteria->order = 'dateCreated DESC, marketingResearchId DESC';
        $nextResearchCriteria->params[':currentMarketingResearchDateCreated'] = $research->dateCreated;
        $nextResearchCriteria->params[':currentMarketingResearchId'] = $research->marketingResearchId;
        $nextResearchCriteria->index = 'marketingResearchId';

        $nextResearch = MarketingResearch::model()->find($nextResearchCriteria);
        /* @var $nextResearch MarketingResearch */

        if (!$nextResearch) {
            $nextResearchCriteria = new CDbCriteria();
            $nextResearchCriteria->addColumnCondition(array('isEnabled' => 1));
            $nextResearchCriteria->addCondition('marketingResearchId != :currentMarketingResearchId');
            $nextResearchCriteria->order = 'dateCreated DESC, marketingResearchId DESC';
            $nextResearchCriteria->params[':currentMarketingResearchId'] = $research->marketingResearchId;
            $nextResearchCriteria->index = 'marketingResearchId';

            $nextResearch = MarketingResearch::model()->find($nextResearchCriteria);
            /* @var $nextResearch MarketingResearch */
        }

        return $nextResearch;
    }

    /**
     * 
     */
    private function saveVisit()
    {
        $currentUser = $this->getUser();
        $visitDateTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        PointHelper::addPoints(Point::KEY_MARKETING_RESEARCH_VISIT, $currentUser->userId, array('dateTime' => $visitDateTime));
    }

    /**
     * 
     * @param integer $researchId
     * @return MarketingResearch
     * @throws CHttpException
     */
    private function findResearchDetail($researchId)
    {
        $currentUser = $this->getUser();
        $criteria = new CDbCriteria();
        $criteria->alias = 'mr';
        $criteria->addColumnCondition(array('mr.marketingResearchId' => $researchId, 'mr.isEnabled' => 1));
        $criteria->with = array('variants' => array('index' => 'marketingResearchVariantId', 'order' => 'marketingResearchVariantId ASC'), 'isAnswered' => array('scopes' => array('selectedUser' => $currentUser->userId)));

        $research = MarketingResearch::model()->find($criteria);
        /* @var $research MarketingResearch */

        if (!$research) {
            throw new CHttpException(400);
        }

        return $research;
    }

    /**
     * 
     * @param MarketingResearchForm $model
     * @param MarketingResearch $research
     * @throws CHttpException
     */
    private function handleMarketingResearchForm(MarketingResearchForm $model, MarketingResearch $research)
    {
        $currentUser = $this->getUser();
        if (isset($_POST['MarketingResearchForm'])) {
            $model->attributes = $_POST['MarketingResearchForm'];
            if ($model->validate()) {
                $transaction = Yii::app()->db->beginTransaction();

                $userAnswer = new MarketingResearchUserAnswer();
                $userAnswer->marketingResearchId = $research->marketingResearchId;
                $userAnswer->userId = $currentUser->userId;
                $userAnswer->dateCreated = time();

                if (!$userAnswer->save()) {
                    $transaction->rollback();
                    throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                }

                if ($research->type == MarketingResearch::TYPE_CUSTOM_TEXT) {
                    $answerText = new MarketingResearchAnswerText();
                    $answerText->marketingResearchId = $research->marketingResearchId;
                    $answerText->userId = $currentUser->userId;
                    $answerText->answer = $model->answerText;

                    if (!$answerText->save()) {
                        $transaction->rollback();
                        throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                    }
                } else {
                    if (!is_array($model->answerVariants)) {
                        $model->answerVariants = array($model->answerVariants);
                    }
                    foreach ($model->answerVariants as $variantId) {
                        $answerVariant = new MarketingResearchAnswerVariant();
                        $answerVariant->marketingResearchId = $research->marketingResearchId;
                        $answerVariant->userId = $currentUser->userId;
                        $answerVariant->marketingResearchVariantId = $variantId;

                        if (!$answerVariant->save()) {
                            $transaction->rollback();
                            throw new CHttpException(500, Yii::t('application', 'Ошибка сервера. Попробуйте еще раз'));
                        }
                    }
                }

                PointHelper::addPoints(Point::KEY_MARKETING_RESEARCH_ANSWER, $currentUser->userId, array('marketingResearchId' => $research->marketingResearchId));

                $transaction->commit();

                Web::flashSuccess(Yii::t('application', 'Ваш ответ принят'));
                $this->refresh();
            }
        }
    }
}