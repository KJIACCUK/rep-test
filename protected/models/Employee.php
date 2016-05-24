<?php

    /**
     * This is the model class for table "employee".
     *
     * The followings are the available columns in table 'employee':
     * @property string $employeeId
     * @property string $accountId
     * @property string $name
     * @property string $email
     * @property string $login
     * @property string $password
     *
     * The followings are the available model relations:
     * @property Account $account
     * @property UserVerification[] $userVerifications
     */
    class Employee extends CActiveRecord
    {

        /**
         *
         * @var Account
         */
        public $searchAccount;
        public $type;
        public $isActive;
        public $passwordConfirm;
        public $isChangePassword;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return Employee the static model class
         */
        public static function model($className = __CLASS__)
        {
            return parent::model($className);
        }

        /**
         * @return string the associated database table name
         */
        public function tableName()
        {
            return 'employee';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                array('accountId, name, email, login', 'required'),
                array('type', 'required', 'on' => 'insert'),
                array('password, passwordConfirm', 'required', 'on' => array('insert', 'update_with_password')),
                array('accountId', 'length', 'max' => 11),
                array('name, email, login', 'length', 'max' => 255),
                array('email', 'email'),
                array('login', 'unique'),
                array('isChangePassword, isActive', 'boolean'),
                array('type', 'in', 'range' => array_keys(UserHelper::getAdminTypes()), 'on' => 'insert'),
                array('password', 'length', 'min' => 6, 'max' => 255, 'on' => array('insert', 'update_with_password')),
                array('passwordConfirm', 'compare', 'compareAttribute' => 'password', 'allowEmpty' => false, 'on' => array('insert', 'update_with_password')),
                array('type, password', 'unsafe', 'on' => array('update')),
                array('employeeId, accountId, name, email, login', 'safe', 'on' => 'search'),
            );
        }
        
        public function beforeSave()
        {
            if(in_array($this->scenario, array('insert', 'update_with_password')))
            {
                $this->password = CommonHelper::md5($this->password);
            }
            return parent::beforeSave();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'account' => array(self::BELONGS_TO, 'Account', 'accountId'),
                'userVerifications' => array(self::HAS_MANY, 'UserVerification', 'employeeId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'employeeId' => Yii::t('application', 'ID'),
                'accountId' => Yii::t('application', 'Аккаунт'),
                'type' => Yii::t('application', 'Тип сотрудника'),
                'name' => Yii::t('application', 'Имя'),
                'email' => Yii::t('application', 'E-mail'),
                'login' => Yii::t('application', 'Логин'),
                'password' => Yii::t('application', 'Пароль'),
                'passwordConfirm' => Yii::t('application', 'Повторить пароль'),
                'isChangePassword' => Yii::t('application', 'Сменить пароль')
                
            );
        }

        /**
         * Retrieves a list of models based on the current search/filter conditions.
         * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
         */
        public function search($type)
        {
            $criteria = new CDbCriteria();
            $criteria->with = array('account');
            $criteria->group = 't.employeeId';
            $criteria->together = true;

            switch($type)
            {
                case 'administrators':
                    $criteria->addColumnCondition(array('account.type' => Account::TYPE_ADMIN));
                    break;

                case 'moderators':
                    $criteria->addColumnCondition(array('account.type' => Account::TYPE_MODERATOR));
                    break;

                case 'operators':
                    $criteria->addColumnCondition(array('account.type' => Account::TYPE_OPERATOR));
                    break;
            }

            $criteria->compare('employeeId', $this->employeeId, true);
            $criteria->compare('name', $this->name, true);
            $criteria->compare('email', $this->email, true);
            $criteria->compare('login', $this->login, true);
            $criteria->compare('account.isActive', $this->searchAccount->isActive);
            $criteria->compare('account.dateCreated', strtotime($this->searchAccount->dateCreated.' midnigth'));

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }

    }
    