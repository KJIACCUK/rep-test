<?php

class AddPointsForm extends CFormModel
{

    public $points;
    public $comment;

    public function rules()
    {
        return array(
          array('points', 'numerical', 'integerOnly' => true, 'min' => 0),
          array('comment', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(
          'points' => Yii::t('application', 'Баллы'),
          'comment' => Yii::t('application', 'Причина добавления'),
        );
    }
    
}
