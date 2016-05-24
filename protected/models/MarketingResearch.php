<?php

/**
 * This is the model class for table "marketing_research".
 *
 * The followings are the available columns in table 'marketing_research':
 * @property integer $marketingResearchId
 * @property string $type
 * @property string $name
 * @property string $content
 * @property integer $isEnabled
 * @property integer $isPushed
 * @property integer $dateCreated
 *
 * The followings are the available model relations:
 * @property MarketingResearchAnswerText[] $answerTexts
 * @property MarketingResearchAnswerVariant[] $answerVariants
 * @property MarketingResearchUserAnswer[] $userAnswers
 * @property MarketingResearchVariant[] $variants
 * @property integer $isAnswered
 */
class MarketingResearch extends CActiveRecord
{
    const TYPE_RADIO = 'radio';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_CUSTOM_TEXT = 'custom_text';

    public $variantsData;
    public $isAnsweredInList;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return MarketingResearch the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'marketing_research';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('type, name', 'required'),
            array('isEnabled', 'boolean'),
            array('type', 'in', 'range' => array(self::TYPE_CHECKBOX, self::TYPE_RADIO, self::TYPE_CUSTOM_TEXT)),
            array('type', 'validateVariants'),
            array('dateCreated', 'length', 'max' => 10),
            array('name', 'length', 'max' => 255),
            array('content', 'safe'),
            array('marketingResearchId, type, name, isEnabled, isPushed, dateCreated', 'safe', 'on' => 'search'),
        );
    }

    public function validateVariants()
    {
        if (in_array($this->type, array(self::TYPE_RADIO, self::TYPE_CHECKBOX))) {
            if (empty($this->variantsData) || !is_array($this->variantsData)) {
                $this->addError('type', Yii::t('application', 'Добавьте варианты ответов'));
            } else {
                foreach ($this->variantsData as $i => $variant) {
                    if (mb_strlen($variant) == 0) {
                        unset($this->variantsData[$i]);
                        continue;
                    }
                    if (mb_strlen($variant) > 255) {
                        $this->addError('type', Yii::t('application', 'Длина варианта ответа не может быть больше 255 символов'));
                    }
                }
            }
        }
    }

    public function beforeSave()
    {
        if ($this->scenario == 'insert') {
            $this->dateCreated = strtotime('today midnight');
            $this->isPushed = 0;
        }

        return parent::beforeSave();
    }

    public function afterSave()
    {
        if ($this->scenario == 'insert') {
            if (in_array($this->type, array(self::TYPE_RADIO, self::TYPE_CHECKBOX)) && !empty($this->variantsData) && is_array($this->variantsData)) {
                foreach ($this->variantsData as $text) {
                    $variant = new MarketingResearchVariant();
                    $variant->marketingResearchId = $this->marketingResearchId;
                    $variant->variant = $text;
                    $variant->save();
                }
            }
        } elseif ($this->scenario == 'update') {
            $criteria = new CDbCriteria();
            $criteria->index = 'marketingResearchVariantId';
            $criteria->addColumnCondition(array('marketingResearchId' => $this->marketingResearchId));
            $oldVariants = MarketingResearchVariant::model()->findAll($criteria);
            /* @var $oldVariants MarketingResearchVariant[] */
            if (in_array($this->type, array(self::TYPE_RADIO, self::TYPE_CHECKBOX)) && !empty($this->variantsData) && is_array($this->variantsData)) {
                foreach ($this->variantsData as $id => $text) {
                    if (substr($id, 0, 4) == 'new_') {
                        $variant = new MarketingResearchVariant();
                        $variant->marketingResearchId = $this->marketingResearchId;
                        $variant->variant = $text;
                        $variant->save();
                    } elseif (isset ($oldVariants[$id])) {
                        $oldVariants[$id]->variant = $text;
                        $oldVariants[$id]->save();
                        unset($oldVariants[$id]);
                    }
                }
            }
            $criteria = new CDbCriteria();
            $criteria->addInCondition('marketingResearchVariantId', array_keys($oldVariants));
            MarketingResearchVariant::model()->deleteAll($criteria);
        }
 
        return parent::afterSave();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'answerTexts' => array(self::HAS_MANY, 'MarketingResearchAnswerText', 'marketingResearchId'),
            'answerVariants' => array(self::HAS_MANY, 'MarketingResearchAnswerVariant', 'marketingResearchId'),
            'userAnswers' => array(self::HAS_MANY, 'MarketingResearchUserAnswer', 'marketingResearchId'),
            'variants' => array(self::HAS_MANY, 'MarketingResearchVariant', 'marketingResearchId'),
            'isAnswered' => array(self::STAT, 'MarketingResearchUserAnswer', 'marketingResearchId')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'marketingResearchId' => Yii::t('application', 'ID'),
            'type' => Yii::t('application', 'Тип'),
            'name' => Yii::t('application', 'Название'),
            'content' => Yii::t('application', 'Текст исследования'),
            'isEnabled' => Yii::t('application', 'Активно'),
            'isPushed' => Yii::t('application', 'Отправлен Push'),
            'dateCreated' => Yii::t('application', 'Дата создания'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('marketingResearchId', $this->marketingResearchId, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('isEnabled', $this->isEnabled);
        $criteria->compare('isPushed', $this->isPushed);
        $criteria->compare('dateCreated', strtotime($this->dateCreated.' midnigth'));

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort'=>array(
              'defaultOrder'=>'dateCreated DESC',
            )
        ));
    }
}