<?php

class VerificationHelper
{
    public static function isOperatorWork()
    {
        $dayOfWeek = strtolower(date('l'));
        
        $startTimeParam = 'operator_'.$dayOfWeek.'_start_time';
        $endTimeParam = 'operator_'.$dayOfWeek.'_end_time';
        
        $criteria = new CDbCriteria();
        $criteria->addInCondition('name', array($startTimeParam, $endTimeParam));
        $criteria->index = 'name';
        
        $settings = GlobalSetting::model()->findAll($criteria);
        
        $now = time();
        $startTime = strtotime(date('Y-m-d').' '.$settings[$startTimeParam]->value.':00');
        $endTime = strtotime(date('Y-m-d').' '.$settings[$endTimeParam]->value.':00');
        
        if ($now >= $startTime && $now <= $endTime) {
            return true;
        }
        
        return false;
    }
}
