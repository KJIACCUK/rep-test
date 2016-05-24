<?php

/**
 * This is the model class for table "promo_code".
 *
 * The followings are the available columns in table 'promo_code':
 * @property integer $promoCodeId
 * @property string $code
 * @property integer $userId
 * @property string $status
 * @property integer $pointsActivated
 * @property integer $dateCreated
 * @property integer $dateActivated
 */
class PromoCode extends CActiveRecord
{
    const STATUS_FREE = 'free';
    const STATUS_ACTIVATED = 'activated';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'promo_code';
    }
    
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PromoCode the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('code, status, dateCreated, dateActivated', 'required'),
            array('code', 'length', 'max' => 255),
            array('userId, pointsActivated', 'length', 'max' => 11),
            array('status', 'length', 'max' => 9),
            array('dateCreated, dateActivated', 'length', 'max' => 10),
            array('promoCodeId, code, userId, status, pointsActivated, dateCreated, dateActivated', 'safe', 'on' => 'search'),
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
            'promoCodeId' => Yii::t('application', 'ID'),
            'code' => Yii::t('application', 'Код'),
            'userId' => Yii::t('application', 'Активировавший пользователь'),
            'status' => Yii::t('application', 'Статус'),
            'pointsActivated' => Yii::t('application', 'Баллов начислено'),
            'dateCreated' => Yii::t('application', 'Дата создания'),
            'dateActivated' => Yii::t('application', 'Дата активации'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('promoCodeId', $this->promoCodeId, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('userId', $this->userId, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('pointsActivated', $this->pointsActivated, true);
        $criteria->compare('dateCreated', $this->dateCreated, true);
        $criteria->compare('dateActivated', $this->dateActivated, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort'=>array(
              'defaultOrder'=>'dateCreated DESC',
            )
        ));
    }
}