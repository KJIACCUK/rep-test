<?php

class PromoForm extends CFormModel
{
    public $code;

    /**
     * @var PromoCode 
     */
    private $_promoCode;

    public function rules()
    {
        return array(
            array('code', 'required'),
            array('code', 'length', 'max' => 255),
            array('code', 'filter', 'filter' => 'trim'),
            array('code', 'validateExists'),
        );
    }

    public function validateExists()
    {
        if ($this->code) {
            $this->_promoCode = PromoCode::model()->findByAttributes(array('code' => $this->code, 'status' => PromoCode::STATUS_FREE));
            if (!$this->_promoCode) {
                $this->addError('code', Yii::t('application', 'Введенный код не найден. Возможно он уже был использован'));
            }
        }
    }

    public function attributeLabels()
    {
        return array(
            'code' => Yii::t('application', 'Промо-код'),
        );
    }

    /**
     * 
     * @return PromoCode
     */
    public function getPromoCode()
    {
        return $this->_promoCode;
    }

    /**
     * 
     * @param User $currentUser
     * @return bool
     */
    public function activateCode($currentUser)
    {
        if ($this->_promoCode) {

            $setting = GlobalSetting::model()->findByAttributes(array('name' => GlobalSetting::PROMO_POINTS_PER_CODE));

            $this->_promoCode->userId = $currentUser->userId;
            $this->_promoCode->status = PromoCode::STATUS_ACTIVATED;
            $this->_promoCode->pointsActivated = $setting->value;
            $this->_promoCode->dateActivated = time();
            if ($this->_promoCode->save()) {
                User::model()->updateCounters(array('points' => $setting->value), 'userId = :userId', array(':userId' => $currentUser->userId));
                $currentUser->points += $setting->value;
                return true;
            }
        }

        return false;
    }
}