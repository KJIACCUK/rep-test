<?php

    class ApiProfileIsFilledFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $currentUser = $filterChain->controller->getUser();
            /* @var $currentUser User */
            
            if(!$currentUser->isFilled)
            {
                throw new ApiException(Api::CODE_FORBIDDEN, Yii::t('application', 'Profile must be completed. Call \'/api/profile_complete\' function.'));
            }
            
            return true;
        }

    }
    