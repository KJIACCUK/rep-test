<?php

    class ApiParamFilter extends CFilter
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

            $param = Api::getParam($this->param);
            $controller = $filterChain->controller;
            /* @var $controller ApiController */
            
            if(!call_user_func($this->function, $param))
            {
                throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Invalid param \''.$this->param.'\' in \''.$this->function.'\'.'));
            }

            return true;
        }

    }
    