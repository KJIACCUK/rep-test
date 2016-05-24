<?php

    class MarketingResearchHelper
    {

        const SCHEMA_DETAIL = 'detail';
        const SCHEMA_QUESTIONS = 'questions';
        const SCHEMA_STATS = 'stats';

        public static function export(MarketingResearch $research, $schema = '', User $user = null)
        {
            $schema = CommonHelper::parseSchema($schema);
            // base schema
            $data = array(
                'marketingResearchId' => (int)$research->marketingResearchId,
                'name' => (string)$research->name,
                'isAnswered' => (bool)$research->isAnswered,
                'dateCreated' => date(Yii::app()->params['dateFormat'], $research->dateCreated),
            );

            if(in_array(self::SCHEMA_DETAIL, $schema))
            {
                $data['type'] = $research->type;
                $data['content'] = '<p></p>'.$research->content;
            }

            if(in_array(self::SCHEMA_QUESTIONS, $schema))
            {
                $data['variants'] = array();
                if($research->type == MarketingResearch::TYPE_CHECKBOX || $research->type == MarketingResearch::TYPE_RADIO)
                {
                    $variants = MarketingResearchVariant::model()->findAllByAttributes(array('marketingResearchId' => $research->marketingResearchId), array('order' => 'marketingResearchVariantId ASC'));
                    foreach($variants as $variant)
                    {
                        $data['variants'][] = array(
                            'marketingResearchVariantId' => (int)$variant->marketingResearchVariantId,
                            'variant' => (string)$variant->variant
                        );
                    }
                }
            }

            if(in_array(self::SCHEMA_STATS, $schema))
            {
                $data += self::exportStats($research, $user);
            }

            return $data;
        }

        public static function exportStats(MarketingResearch $research, User $user)
        {
            $data = array();
            if($research->type == MarketingResearch::TYPE_CUSTOM_TEXT)
            {
                $data['answerText'] = '';
                $answerText = MarketingResearchAnswerText::model()->findByAttributes(array('marketingResearchId' => $research->marketingResearchId, 'userId' => $user->userId));
                /* @var $answerText MarketingResearchAnswerText */
                if($answerText)
                {
                    $data['answerText'] = (string)$answerText->answer;
                }
            }
            else
            {
                $data['stats'] = array();
                $answerVariants = MarketingResearchAnswerVariant::model()->findAllByAttributes(array('marketingResearchId' => $research->marketingResearchId));
                /* @var $answerVariants MarketingResearchAnswerVariant[] */

                $counts = array();
                $total = 0;

                foreach($research->variants as $variant)
                {
                    $counts[$variant->marketingResearchVariantId] = 0;
                }

                foreach($answerVariants as $item)
                {
                    $counts[$item->marketingResearchVariantId] ++;
                    $total++;
                }

                foreach($counts as $variantId => $c)
                {
                    $counts[$variantId] = $total?round(($c / $total) * 100):0;
                }

                foreach($research->variants as $variant)
                {
                    $data['stats'][] = array(
                        'marketingResearchVariantId' => (int)$variant->marketingResearchVariantId,
                        'variant' => (string)$variant->variant,
                        'percents' => $counts[$variant->marketingResearchVariantId]
                    );
                }
            }

            return $data;
        }
        
        public static function exportVariantsStats(MarketingResearch $research)
        {
            $data = array();
            if($research->type != MarketingResearch::TYPE_CUSTOM_TEXT)
            {
                $data['stats'] = array();
                $answerVariants = MarketingResearchAnswerVariant::model()->findAllByAttributes(array('marketingResearchId' => $research->marketingResearchId));
                /* @var $answerVariants MarketingResearchAnswerVariant[] */

                $counts = array();
                $total = 0;
                $totalUsers = array();

                foreach($research->variants as $variant)
                {
                    $counts[$variant->marketingResearchVariantId] = 0;
                }

                foreach($answerVariants as $item)
                {
                    $counts[$item->marketingResearchVariantId] ++;
                    $total++;
                    if(!in_array($item->userId, $totalUsers))
                    {
                        $totalUsers[] = $item->userId;
                    }
                }

                foreach($counts as $variantId => $c)
                {
                    $counts[$variantId] = $total?round(($c / $total) * 100):0;
                }

                foreach($research->variants as $variant)
                {
                    $data['stats'][] = array(
                        'marketingResearchVariantId' => (int)$variant->marketingResearchVariantId,
                        'variant' => (string)$variant->variant,
                        'percents' => $counts[$variant->marketingResearchVariantId]
                    );
                }
                
                $data['totalUsers'] = count($totalUsers);
            }

            return $data;
        }

        public static function variantsToList($variants)
        {
            $result = array();
            foreach($variants as $item)
            {
                /* @var $item MarketingResearchVariant */
                $result[$item->marketingResearchVariantId] = $item->variant;
            }
            return $result;
        }

        public static function typesToDropDown()
        {
            return array(
                MarketingResearch::TYPE_RADIO => Yii::t('application', 'Radio'),
                MarketingResearch::TYPE_CHECKBOX => Yii::t('application', 'Checkbox'),
                MarketingResearch::TYPE_CUSTOM_TEXT => Yii::t('application', 'Текст')
            );
        }
        
        public static function typesToGridList()
        {
            return array(
                array('id' => MarketingResearch::TYPE_RADIO, 'title' => Yii::t('application', 'Radio')),
                array('id' => MarketingResearch::TYPE_CHECKBOX, 'title' => Yii::t('application', 'Checkbox')),
                array('id' => MarketingResearch::TYPE_CUSTOM_TEXT, 'title' => Yii::t('application', 'Текст')),
            );
        }

        public static function typeGridValue($value)
        {
            if($value == MarketingResearch::TYPE_RADIO)
            {
                return Yii::t('application', 'Radio');
            }
            elseif($value == MarketingResearch::TYPE_CHECKBOX)
            {
                return Yii::t('application', 'Checkbox');
            }
            else
            {
                return Yii::t('application', 'Текст');
            }
        }

    }
    