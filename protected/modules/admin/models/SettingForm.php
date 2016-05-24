<?php

class SettingForm extends CFormModel
{

    public $pointSocialInvite;
    public $pointVerification;
    public $pointResearchVisit;
    public $pointResearchAnswer;
    public $pointEventCreate;
    public $pointTenEventsSubscribed;
    public $pointSocialShare;
    public $operatorMondayStartTime;
    public $operatorMondayEndTime;
    public $operatorTuesdayStartTime;
    public $operatorTuesdayEndTime;
    public $operatorWednesdayStartTime;
    public $operatorWednesdayEndTime;
    public $operatorThursdayStartTime;
    public $operatorThursdayEndTime;
    public $operatorFridayStartTime;
    public $operatorFridayEndTime;
    public $operatorSaturdayStartTime;
    public $operatorSaturdayEndTime;
    public $operatorSundayStartTime;
    public $operatorSundayEndTime;
    public $promoPointsPerCode;

    public function rules()
    {
        return array(
            array('pointSocialInvite, pointVerification, pointResearchVisit, pointResearchAnswer, pointEventCreate, promoPointsPerCode, pointTenEventsSubscribed, pointSocialShare', 'numerical', 'integerOnly' => true, 'min' => 0),
            array('operatorMondayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorMondayEndTime', 'date', 'format' => 'hh:mm'),
            array('operatorTuesdayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorTuesdayEndTime', 'date', 'format' => 'hh:mm'),
            array('operatorWednesdayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorWednesdayEndTime', 'date', 'format' => 'hh:mm'),
            array('operatorThursdayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorThursdayEndTime', 'date', 'format' => 'hh:mm'),
            array('operatorFridayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorFridayEndTime', 'date', 'format' => 'hh:mm'),
            array('operatorSaturdayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorSaturdayEndTime', 'date', 'format' => 'hh:mm'),
            array('operatorSundayStartTime', 'date', 'format' => 'hh:mm'),
            array('operatorSundayEndTime', 'date', 'format' => 'hh:mm'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'pointSocialInvite' => Yii::t('application', 'Количество баллов за приглашение в социальной сети'),
            'pointVerification' => Yii::t('application', 'Количество баллов за верификацию'),
            'pointResearchVisit' => Yii::t('application', 'Количество баллов за посещение раздела МИ'),
            'pointResearchAnswer' => Yii::t('application', 'Количество баллов за ответ в разделе МИ'),
            'pointEventCreate' => Yii::t('application', 'Количество баллов за создание мероприятия'),
            'pointTenEventsSubscribed' => Yii::t('apllication', 'Количество баллов за подписку на каждые 10 мероприятий'),
            'pointSocialShare' => Yii::t('apllication', 'Количество баллов за шэринг в соцсети'),
            'operatorMondayStartTime' => Yii::t('application', 'Время начала работы оператора (Понедельник)'),
            'operatorMondayEndTime' => Yii::t('application', 'Время окончания работы оператора (Понедельник)'),
            'operatorTuesdayStartTime' => Yii::t('application', 'Время начала работы оператора (Вторник)'),
            'operatorTuesdayEndTime' => Yii::t('application', 'Время окончания работы оператора (Вторник)'),
            'operatorWednesdayStartTime' => Yii::t('application', 'Время начала работы оператора (Среда)'),
            'operatorWednesdayEndTime' => Yii::t('application', 'Время окончания работы оператора (Среда)'),
            'operatorThursdayStartTime' => Yii::t('application', 'Время начала работы оператора (Четверг)'),
            'operatorThursdayEndTime' => Yii::t('application', 'Время окончания работы оператора (Четверг)'),
            'operatorFridayStartTime' => Yii::t('application', 'Время начала работы оператора (Пятница)'),
            'operatorFridayEndTime' => Yii::t('application', 'Время окончания работы оператора (Пятница)'),
            'operatorSaturdayStartTime' => Yii::t('application', 'Время начала работы оператора (Суббота)'),
            'operatorSaturdayEndTime' => Yii::t('application', 'Время окончания работы оператора (Суббота)'),
            'operatorSundayStartTime' => Yii::t('application', 'Время начала работы оператора (Воскресение)'),
            'operatorSundayEndTime' => Yii::t('application', 'Время окончания работы оператора (Воскресение)'),
            'promoPointsPerCode' => Yii::t('application', 'Количество баллов за один промо-код')
        );
    }

    public function loadSettings()
    {
        $points = Point::model()->findAll();
        /* @var $points Point[] */
        foreach ($points as $item) {
            switch ($item->pointKey) {
                case Point::KEY_SOCIAL_INVITE:
                    $this->pointSocialInvite = $item->pointsCount;
                    break;

                case Point::KEY_VERIFICATION:
                    $this->pointVerification = $item->pointsCount;
                    break;

                case Point::KEY_MARKETING_RESEARCH_VISIT:
                    $this->pointResearchVisit = $item->pointsCount;
                    break;

                case Point::KEY_MARKETING_RESEARCH_ANSWER:
                    $this->pointResearchAnswer = $item->pointsCount;
                    break;

                case Point::KEY_EVENT_CREATE:
                    $this->pointEventCreate = $item->pointsCount;
                    break;
                case Point::KEY_TEN_EVENTS_SUBSCRIBED:
                    $this->pointTenEventsSubscribed = $item->pointsCount;
                    break;
                case Point::KEY_SOCIAL_SHARE:
                    $this->pointSocialShare = $item->pointsCount;
                    break;
            }
        }

        $globalSettings = GlobalSetting::model()->findAll();
        /* @var $globalSettings GlobalSetting[] */
        foreach ($globalSettings as $item) {
            switch ($item->name) {
                case GlobalSetting::OPERATOR_MONDAY_START_TIME:
                    $this->operatorMondayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_MONDAY_END_TIME:
                    $this->operatorMondayEndTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_TUESDAY_START_TIME:
                    $this->operatorTuesdayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_TUESDAY_END_TIME:
                    $this->operatorTuesdayEndTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_WEDNESDAY_START_TIME:
                    $this->operatorWednesdayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_WEDNESDAY_END_TIME:
                    $this->operatorWednesdayEndTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_THURSDAY_START_TIME:
                    $this->operatorThursdayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_THURSDAY_END_TIME:
                    $this->operatorThursdayEndTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_FRIDAY_START_TIME:
                    $this->operatorFridayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_FRIDAY_END_TIME:
                    $this->operatorFridayEndTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_SATURDAY_START_TIME:
                    $this->operatorSaturdayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_SATURDAY_END_TIME:
                    $this->operatorSaturdayEndTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_SUNDAY_START_TIME:
                    $this->operatorSundayStartTime = $item->value;
                    break;

                case GlobalSetting::OPERATOR_SUNDAY_END_TIME:
                    $this->operatorSundayEndTime = $item->value;
                    break;
                case GlobalSetting::PROMO_POINTS_PER_CODE:
                    $this->promoPointsPerCode = $item->value;
                    break;
            }
        }
    }

    public function saveSettings()
    {
        $points = Point::model()->findAll();
        /* @var $points Point[] */
        foreach ($points as $item) {
            switch ($item->pointKey) {
                case Point::KEY_SOCIAL_INVITE:
                    $item->pointsCount = $this->pointSocialInvite;
                    $item->save();
                    break;

                case Point::KEY_VERIFICATION:
                    $item->pointsCount = $this->pointVerification;
                    $item->save();
                    break;

                case Point::KEY_MARKETING_RESEARCH_VISIT:
                    $item->pointsCount = $this->pointResearchVisit;
                    $item->save();
                    break;

                case Point::KEY_MARKETING_RESEARCH_ANSWER:
                    $item->pointsCount = $this->pointResearchAnswer;
                    $item->save();
                    break;

                case Point::KEY_EVENT_CREATE:
                    $item->pointsCount = $this->pointEventCreate;
                    $item->save();
                    break;
                case Point::KEY_TEN_EVENTS_SUBSCRIBED:
                    $item->pointsCount = $this->pointTenEventsSubscribed;
                    $item->save();
                    break;
                case Point::KEY_SOCIAL_SHARE:
                    $item->pointsCount = $this->pointSocialShare;
                    $item->save();
                    break;
            }
        }

        $globalSettings = GlobalSetting::model()->findAll();
        /* @var $globalSettings GlobalSetting[] */
        foreach ($globalSettings as $item) {
            switch ($item->name) {
                case GlobalSetting::OPERATOR_MONDAY_START_TIME:
                    $item->value = $this->operatorMondayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_MONDAY_END_TIME:
                    $item->value = $this->operatorMondayEndTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_TUESDAY_START_TIME:
                    $item->value = $this->operatorTuesdayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_TUESDAY_END_TIME:
                    $item->value = $this->operatorTuesdayEndTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_WEDNESDAY_START_TIME:
                    $item->value = $this->operatorWednesdayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_WEDNESDAY_END_TIME:
                    $item->value = $this->operatorWednesdayEndTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_THURSDAY_START_TIME:
                    $item->value = $this->operatorThursdayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_THURSDAY_END_TIME:
                    $item->value = $this->operatorThursdayEndTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_FRIDAY_START_TIME:
                    $item->value = $this->operatorFridayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_FRIDAY_END_TIME:
                    $item->value = $this->operatorFridayEndTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_SATURDAY_START_TIME:
                    $item->value = $this->operatorSaturdayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_SATURDAY_END_TIME:
                    $item->value = $this->operatorSaturdayEndTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_SUNDAY_START_TIME:
                    $item->value = $this->operatorSundayStartTime;
                    $item->save();
                    break;

                case GlobalSetting::OPERATOR_SUNDAY_END_TIME:
                    $item->value = $this->operatorSundayEndTime;
                    $item->save();
                    break;
                case GlobalSetting::PROMO_POINTS_PER_CODE:
                    $item->value = $this->promoPointsPerCode;
                    $item->save();
                    break;
            }
        }
    }

}
