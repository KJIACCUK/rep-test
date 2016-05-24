<?php

    class ApiMarketingResearchAnswer extends CFormModel
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
                ApiValidatorHelper::required('answerText', 'custom_text'),
                ApiValidatorHelper::length('answerText', null, 5000, 'custom_text')
            );
        }

        public function checkRadioAnswers()
        {
            if(!$this->answerVariants || !is_array($this->answerVariants) || count($this->answerVariants) != 1)
            {
                $this->addError('answerVariants', ValidationMessageHelper::REQUIRED);
                return false;
            }

            if(!in_array($this->answerVariants[0], $this->variants))
            {
                $this->addError('answerVariants', ValidationMessageHelper::NOT_IN_RANGE);
                return false;
            }
        }

        public function checkCheckboxAnswers()
        {
            if(!$this->answerVariants || !is_array($this->answerVariants) || count($this->answerVariants) == 0)
            {
                $this->addError('answerVariants', ValidationMessageHelper::REQUIRED);
                return false;
            }

            foreach($this->answerVariants as $answer)
            {
                if(!in_array($answer, $this->variants))
                {
                    $this->addError('answerVariants', ValidationMessageHelper::NOT_IN_RANGE);
                    return false;
                }
            }
        }

    }
    