<?php

    class ExistsFilter extends CFilter
    {

        public $param;
        public $function;
        public $errorMessage;

        protected function preFilter(CFilterChain $filterChain)
        {
            if(!$this->param)
            {
                throw new Exception(Yii::t('application', 'Not configured ApiParamFilter.param'), 500);
            }

            if(!$this->function)
            {
                throw new Exception(Yii::t('application', 'Not configured ApiParamFilter.function'), 500);
            }
            
            if(!$this->errorMessage)
            {
                $this->errorMessage = Yii::t('application', 'Страница не найдена');
            }

            $param = Web::getParam($this->param);
            
            if(!call_user_func($this->function, $param))
            {
                if(Yii::app()->request->isAjaxRequest)
                {
                    Web::jsonError($this->errorMessage);
                }
                else
                {
                    throw new CHttpException(404, $this->errorMessage);
                }
            }

            return true;
        }

    }
    