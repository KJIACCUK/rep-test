<?php

class ApiRegistration extends CFormModel
{
    public $email;
    public $password;
    public $firstname;
    public $lastname;
    public $birthday;
    public $phoneCode;
    public $phone;
    public $favoriteCigaretteBrand;
    public $verificationPhotoFile;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            // step 1
            ApiValidatorHelper::required('email, password', 'step1'),
            ApiValidatorHelper::length('email', null, 255, 'step1'),
            ApiValidatorHelper::email('email', 'step1'),
            array('email', 'validateEmailUnique', 'on' => 'step1'),
            ApiValidatorHelper::length('password', 6, 255, 'step1'),
            // step 2
            ApiValidatorHelper::required('firstname, lastname', 'step2'),
            ApiValidatorHelper::length('firstname, lastname', null, 125, 'step2'),
            // step 3
            ApiValidatorHelper::required('birthday, phoneCode, phone', 'step3'),
            ApiValidatorHelper::date('birthday', 'dd.MM.yyyy', 'step3'),
            array('birthday', 'validateAge', 'on' => 'step3'),
            ApiValidatorHelper::in('phoneCode', array_keys(Yii::app()->params['phoneCodes']), 'step3'),
            ApiValidatorHelper::type('phone', 'integer', 'step3'),
            ApiValidatorHelper::length('phone', 7, 7, 'step3'),
//            array('favoriteCigaretteBrand', 'in', 'range' => Yii::app()->params['cigaretteBrands'], 'allowEmpty' => true, 'on' => 'step3'),
            // step 4
            ApiValidatorHelper::file('verificationPhotoFile', 'png, jpg, jpe, jpeg', null, 5 * 1024 * 1024, 1, 'step4') + array('allowEmpty' => false),
        );
    }

    public function validateEmailUnique()
    {
        if ($this->email) {
            $criteria = new CDbCriteria();
            $criteria->addCondition('email = :email OR login = :email');
            $criteria->params[':email'] = $this->email;
            if (User::model()->exists($criteria)) {
                $this->addError('email', ValidationMessageHelper::NOT_UNIQUE);
            }
        }
    }

    public function validateAge()
    {
        $tz = new DateTimeZone(date_default_timezone_get());
        $datetime = DateTime::createFromFormat(Yii::app()->params['dateFormat'], $this->birthday, $tz);
        if ($datetime) {
            $age = $datetime->diff(new DateTime('now', $tz))->y;
            if ($age < 18) {
                $this->addError('birthday', ValidationMessageHelper::INVALID_AGE);
            }
        }
    }
}   