<?php

    class ApiGetEventsParamsFilter extends CFilter
    {

        protected function preFilter(CFilterChain $filterChain)
        {
            $mode = Api::getParam('mode', EventHelper::GET_EVENTS_MODE_LIST);

            if(!in_array($mode, array(EventHelper::GET_EVENTS_MODE_LIST, EventHelper::GET_EVENTS_MODE_MINE, EventHelper::GET_EVENTS_MODE_CALENDAR, EventHelper::GET_EVENTS_MODE_MAP)))
            {
                throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Invalid param \'mode\'.'));
            }

            $city = Api::getParam('city', Yii::t('application', 'Минск'));
            if(in_array($mode, array(EventHelper::GET_EVENTS_MODE_LIST, EventHelper::GET_EVENTS_MODE_CALENDAR, EventHelper::GET_EVENTS_MODE_MAP)))
            {
                if(!$city || !YandexMapsHelper::getCityByName($city))
                {
                    throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Invalid param \'city\'.'));
                }
            }

            $month = (int)Api::getParam('month', date('m'));
            $year = (int)Api::getParam('year', date('Y'));
            if(in_array($mode, array(EventHelper::GET_EVENTS_MODE_CALENDAR)))
            {
                if($month < 1 || $month > 12)
                {
                    throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Invalid param \'month\'.'));
                }

                if($year < 0 || $year > 9999)
                {
                    throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Invalid param \'year\'.'));
                }
            }

            $order = Api::getParam('order', EventHelper::GET_EVENTS_ORDER_DATE_CREATED);
            if(in_array($mode, array(EventHelper::GET_EVENTS_MODE_LIST)))
            {
                if(!in_array($order, array(EventHelper::GET_EVENTS_ORDER_DATE_CREATED, EventHelper::GET_EVENTS_ORDER_DATE_START, EventHelper::GET_EVENTS_ORDER_SUBSCRIBERS_COUNT)))
                {
                    throw new ApiException(Api::CODE_BAD_REQUEST, Yii::t('application', 'Invalid param \'order\'.'));
                }
            }

            return true;
        }

    }
    