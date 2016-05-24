<?php

    class MarketingResearchController extends ApiController
    {

        public function filters()
        {
            return array(
                array('ApiAccessControlFilter'),
                array('ApiProfileIsFilledFilter'),
                array('ApiProfileIsVerifiedFilter'),
                array(
                    'ApiParamFilter + getMarketingResearch, answerToMarketingResearch',
                    'param' => 'marketingResearchId',
                    'function' => 'FilterHelper::checkResearchExists'
                )
            );
        }

        public function actionGetMarketingResearches()
        {
            $currentUser = $this->getUser();
            $criteria = new CDbCriteria();
            $criteria->offset = Api::getParam('offset', 0);
            $criteria->limit = Api::getParam('limit', 50);
            $criteria->addColumnCondition(array('isEnabled' => 1));
            $criteria->order = 'dateCreated DESC, marketingResearchId DESC';
            $criteria->with = array('isAnswered' => array('scopes' => array('selectedUser' => $currentUser->userId)));

            $marketingResearches = MarketingResearch::model()->findAll($criteria);

            $visitDateTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            PointHelper::addPoints(Point::KEY_MARKETING_RESEARCH_VISIT, $currentUser->userId, array('dateTime' => $visitDateTime));
            
            $response = array();
            $response['researches'] = array();

            foreach($marketingResearches as $item)
            {
                /* @var $item MarketingResearch */
                $response['researches'][] = MarketingResearchHelper::export($item);
            }

            Api::respondSuccess($response);
        }

        public function actionGetMarketingResearch()
        {
            $currentUser = $this->getUser();
            $criteria = new CDbCriteria();
            $criteria->alias = 'mr';
            $criteria->addColumnCondition(array('mr.marketingResearchId' => Api::getParam('marketingResearchId'), 'mr.isEnabled' => 1));
            $criteria->with = array('isAnswered' => array('scopes' => array('selectedUser' => $currentUser->userId)));
            $marketingResearch = MarketingResearch::model()->find($criteria);
            /* @var $marketingResearch MarketingResearch */

            $visitDateTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            PointHelper::addPoints(Point::KEY_MARKETING_RESEARCH_VISIT, $currentUser->userId, array('dateTime' => $visitDateTime));
            
            $response = array();
            if(!$marketingResearch->isAnswered)
            {
                $schema = 'detail, questions';
            }
            else
            {
                $schema = 'detail, stats';
            }
            $response['research'] = MarketingResearchHelper::export($marketingResearch, $schema, $currentUser);
            Api::respondSuccess($response);
        }

        public function actionAnswerToMarketingResearch()
        {
            $currentUser = $this->getUser();
            $criteria = new CDbCriteria();
            $criteria->alias = 'mr';
            $criteria->addColumnCondition(array('mr.marketingResearchId' => Api::getParam('marketingResearchId'), 'mr.isEnabled' => 1));
            $criteria->with = array('variants' => array('index' => 'marketingResearchVariantId'), 'isAnswered' => array('scopes' => array('selectedUser' => $currentUser->userId)));
            $marketingResearch = MarketingResearch::model()->find($criteria);
            /* @var $marketingResearch MarketingResearch */
            
            if($marketingResearch->isAnswered)
            {
                Api::respondError(api::CODE_BAD_REQUEST, Yii::t('application', 'Вы уже участвовали в этом исследовании'));
            }

            $model = new ApiMarketingResearchAnswer($marketingResearch->type);
            if($marketingResearch->type == MarketingResearch::TYPE_CHECKBOX || $marketingResearch->type == MarketingResearch::TYPE_RADIO)
            {
                $model->variants = array_keys($marketingResearch->variants);
            }
            $model->attributes = Api::getParams(array('answerText', 'answerVariants'));

            if(!$model->validate())
            {
                Api::respondValidationError($model);
            }

            $transaction = Yii::app()->db->beginTransaction();

            $userAnswer = new MarketingResearchUserAnswer();
            $userAnswer->marketingResearchId = $marketingResearch->marketingResearchId;
            $userAnswer->userId = $currentUser->userId;
            $userAnswer->dateCreated = time();

            if(!$userAnswer->save())
            {
                $transaction->rollback();
                throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
            }

            if($marketingResearch->type == MarketingResearch::TYPE_CUSTOM_TEXT)
            {
                $answerText = new MarketingResearchAnswerText();
                $answerText->marketingResearchId = $marketingResearch->marketingResearchId;
                $answerText->userId = $currentUser->userId;
                $answerText->answer = $model->answerText;

                if(!$answerText->save())
                {
                    $transaction->rollback();
                    throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
                }
            }
            else
            {
                foreach($model->answerVariants as $variantId)
                {
                    $answerVariant = new MarketingResearchAnswerVariant();
                    $answerVariant->marketingResearchId = $marketingResearch->marketingResearchId;
                    $answerVariant->userId = $currentUser->userId;
                    $answerVariant->marketingResearchVariantId = $variantId;

                    if(!$answerVariant->save())
                    {
                        $transaction->rollback();
                        throw new ApiException(Api::CODE_INTERNAL_SERVER_ERROR);
                    }
                }
            }
            
            PointHelper::addPoints(Point::KEY_MARKETING_RESEARCH_ANSWER, $currentUser->userId, array('marketingResearchId' => $marketingResearch->marketingResearchId));
            
            $transaction->commit();
            
            $response['research'] = MarketingResearchHelper::export($marketingResearch, 'detail, stats', $currentUser);
            Api::respondSuccess($response);
        }

    }
    