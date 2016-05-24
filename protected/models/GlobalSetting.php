<?php

/**
 * This is the model class for table "global_setting".
 *
 * The followings are the available columns in table 'global_setting':
 * @property integer $globalSettingId
 * @property string $name
 * @property string $value
 */
class GlobalSetting extends CActiveRecord
{
    const OPERATOR_MONDAY_START_TIME = 'operator_monday_start_time';
    const OPERATOR_MONDAY_END_TIME = 'operator_monday_end_time';
    const OPERATOR_TUESDAY_START_TIME = 'operator_tuesday_start_time';
    const OPERATOR_TUESDAY_END_TIME = 'operator_tuesday_end_time';
    const OPERATOR_WEDNESDAY_START_TIME = 'operator_wednesday_start_time';
    const OPERATOR_WEDNESDAY_END_TIME = 'operator_wednesday_end_time';
    const OPERATOR_THURSDAY_START_TIME = 'operator_thursday_start_time';
    const OPERATOR_THURSDAY_END_TIME = 'operator_thursday_end_time';
    const OPERATOR_FRIDAY_START_TIME = 'operator_friday_start_time';
    const OPERATOR_FRIDAY_END_TIME = 'operator_friday_end_time';
    const OPERATOR_SATURDAY_START_TIME = 'operator_saturday_start_time';
    const OPERATOR_SATURDAY_END_TIME = 'operator_saturday_end_time';
    const OPERATOR_SUNDAY_START_TIME = 'operator_sunday_start_time';
    const OPERATOR_SUNDAY_END_TIME = 'operator_sunday_end_time';
    const PROMO_POINTS_PER_CODE = 'promo_points_per_code';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'global_setting';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('name, value', 'length', 'max' => 255)
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'globalSettingId' => Yii::t('application', 'ID'),
            'name' => Yii::t('application', 'Название'),
            'value' => Yii::t('application', 'Значение'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GlobalSetting the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
