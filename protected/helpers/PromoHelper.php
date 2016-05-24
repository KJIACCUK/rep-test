<?php

class PromoHelper
{

    public static function statusGridList()
    {
        return array(
            array('id' => PromoCode::STATUS_FREE, 'title' => Yii::t('application', 'Свободен')),
            array('id' => PromoCode::STATUS_ACTIVATED, 'title' => Yii::t('application', 'Активирован')),
        );
    }

    public static function statusToGridValue($value, $withformatting = true)
    {
        if ($value == PromoCode::STATUS_FREE) {
            return $withformatting?TbHtml::labelTb(Yii::t('application', 'Свободен'), array('color' => TbHtml::LABEL_COLOR_DEFAULT)):Yii::t('application', 'Свободен');
        }
        return $withformatting?TbHtml::labelTb(Yii::t('application', 'Активирован'), array('color' => TbHtml::LABEL_COLOR_SUCCESS)):Yii::t('application', 'Активирован');
    }
    
    public static function userToGridValue($userId)
    {
        if ($userId) {
            $user = User::model()->findByPk($userId);
            if ($user) {
                return $user->name;
            }
        }
        return '';
    }
}