<?php

/**
 * This is the model class for table "event_relax_deleted".
 *
 * The followings are the available columns in table 'event_relax_deleted':
 * @property integer $eventRelaxDeletedId
 * @property string $relaxId
 */
class EventRelaxDeleted extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_relax_deleted';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('relaxId', 'required'),
            array('relaxId', 'length', 'max' => 255)
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
            'eventRelaxDeletedId' => 'ID',
            'relaxId' => 'Relax ID',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventRelaxDeleted the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}