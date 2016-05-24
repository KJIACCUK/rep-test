<?php

    class MarketingResearchForm extends CFormModel
    {

        public $variants;
        public $answerText;
        public $answerVariants;

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // scenarios same as research types - radio, checkbox, custom_text
            return array(
                array('answerVariants', 'checkRadioAnswers', 'on' => 'radio'),
                array('answerVariants', 'checkCheckboxAnswers', 'on' => 'checkbox'),
                array('answerText', 'required', 'on' => 'custom_text'),
                array('answerText', 'length', 'max' => 5000, 'on' => 'custom_text')
            );
        }

        public function checkRadioAnswers()
        {
            if(!$this->answerVariants)
            {
                $this->addError('answerVariants', Yii::t('application', 'Выберите один из предложенных вариантов'));
                return false;
            }

            if(!in_array($this->answerVariants, $this->variants))
            {
                $this->addError('answerVariants', Yii::t('application', 'Вы ввели неправильное значение'));
                return false;
            }
        }

        public function checkCheckboxAnswers()
        {
            if(!$this->answerVariants || !is_array($this->answerVariants) || count($this->answerVariants) == 0)
            {
                $this->addError('answerVariants', Yii::t('application', 'Выберите один или несколько из предложенных вариантов'));
                return false;
            }

            foreach($this->answerVariants as $answer)
            {
                if(!in_array($answer, $this->variants))
                {
                    $this->addError('answerVariants', Yii::t('application', 'Вы ввели неправильное значение'));
                    return false;
                }
            }
        }

    }
    