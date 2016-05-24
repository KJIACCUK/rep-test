<?php

    class ParamFilter extends CFilter
    {

        public $param;
        public $function;

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

            $param = Web::getParam($this->param);
            
            if(!call_user_func($this->function, $param))
            {
                if(Yii::app()->request->isAjaxRequest)
                {
                    Web::jsonError(Yii::t('application', 'Неверно задан параметр \''.$this->param.'\'.'));
                }
                else
                {
                    throw new CHttpException(403, Yii::t('application', 'Неверно задан параметр \''.$this->param.'\'.'));
                }
            }

            return true;
        }

    }