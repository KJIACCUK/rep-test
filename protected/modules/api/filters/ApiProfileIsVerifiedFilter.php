<?php

    class ApiProfileIsVerifiedFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $currentUser = $filterChain->controller->getUser();
            /* @var $currentUser User */
            
            if(!$currentUser->isVerified)
            {
                throw new ApiException(Api::CODE_FORBIDDEN, Yii::t('application', 'Profile must be vefified.'));
            }
            
            return true;
        }

    }
    