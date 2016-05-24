<?php

    /**
     * This is the model class for table "user_verification_request".
     *
     * The followings are the available columns in table 'user_verification_request':
     * @property integer $userVerificationRequestId
     * @property integer $userId
     * @property integer $employeeId
     * @property string $messenger
     * @property string $messengerLogin
     * @property integer $callDate
     * @property string $callTime
     * @property string $status
     * @property string $comment
     * @property string $attachment
     * @property string $photoAttachment
     * @property integer $isVerified
     * @property integer $isMissed
     * @property integer $isPhotoVerification
     * @property integer $dateCreated
     * @property integer $dateClosed
     *
     * The followings are the available model relations:
     * @property User $user
     */
    class  UserVerificationRequest extends CActiveRecord
    {

        const STATUS_OPENED = 'opened';
        const STATUS_CLOSED = 'closed';

        public $callDateYear;
        public $callDateMonth;
        public $callDateDay;
        public $callTimeHours;
        public $callTimeMinutes;

        /**
         *
         * @var User
         */
        public $searchUser;
        public $attachmentFile;
        public $favoriteCigaretteBrand;

        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return UserVerificationRequest the static model class
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
            return 'user_verification_request';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            return array(
                // api_insert
                ApiValidatorHelper::required('messengerLogin, callDate, callTime', 'api_insert'),
                ApiValidatorHelper::length('messengerLogin', null, 255, 'api_insert'),
                ApiValidatorHelper::date('callDate', 'dd.MM.yyyy', 'api_insert'),
                ApiValidatorHelper::date('callTime', 'hh:mm', 'api_insert'),
                array('callDate', 'apiValidateDateStart', 'on' => 'api_insert'),
                ApiValidatorHelper::safe('userId, messenger, status, dateCreated, isMissed', 'api_insert'),
                array('messengerLogin, callDate, callDateYear, callDateMonth, callDateDay, callTime, callTimeHours, callTimeMinutes', 'required', 'on' => 'insert'),
                array('messengerLogin', 'length', 'max' => 255, 'on' => 'insert'),
                array('callDate', 'date', 'format' => 'dd.M.yyyy', 'on' => 'insert'),
                array('callTime', 'date', 'format' => 'hh:mm', 'on' => 'insert'),
                array('callDate', 'validateCallTime', 'on' => 'insert'),
                array('userId, messenger, status, dateCreated', 'safe', 'on' => 'insert'),
                array('employeeId, status, dateClosed', 'required', 'on' => 'update'),
                array('employeeId', 'length', 'max' => 11, 'on' => 'update'),
                array('attachment', 'length', 'max' => 255, 'on' => 'update'),
                array('attachmentFile', 'file', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => true, 'on' => 'update'),
                array('isVerified', 'boolean', 'on' => 'update'),
                array('comment', 'safe', 'on' => 'update'),
                array('messenger, messengerLogin, userId, callDate, status, dateCreated, isMissed', 'safe', 'on' => 'missing'),
                array('messenger, messengerLogin, userId, callDate, status, dateCreated, isPhotoVerification', 'safe', 'on' => array('api_photo_verification', 'photo_verification')),
                array('messenger, messengerLogin, userId, callDate, status, dateCreated, photoAttachment, isPhotoVerification', 'safe', 'on' => 'photo_verification_with_photo_string'),
                array('attachmentFile', 'file', 'types' => 'png, jpg, jpe, jpeg', 'maxSize' => 5 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => 'photo_verification'),
                ApiValidatorHelper::in('favoriteCigaretteBrand', Yii::app()->params['cigaretteBrands'], 'api_photo_verification') + array('allowEmpty' => true),
                ApiValidatorHelper::file('attachmentFile', 'png, jpg, jpe, jpeg', null, 5 * 1024 * 1024, 1, 'api_photo_verification') + array('allowEmpty' => false),
                array('userVerificationRequestId, messengerLogin, callDate, dateCreated', 'safe', 'on' => 'search'),
                array('userVerificationRequestId, userId, employeeId, messenger, messengerLogin, callDate, callTime, status, dateCreated, dateClosed, isVerified', 'safe', 'on' => 'search_history'),
            );
        }

        public function validateCallTime()
        {
            $callDate = strtotime($this->callDateDay.'.'.$this->callDateMonth.'.'.$this->callDateYear.' '.$this->callTimeHours.':'.$this->callTimeMinutes);
            if($callDate < time())
            {
                $this->addError('callDate', Yii::t('application', 'Дата и время начала запроса должны быть больше текущей даты и времени'));
            }
        }

        public function apiValidateDateStart()
        {
            $callDate = strtotime($this->callDate.' '.$this->callTime);
            if($callDate < time())
            {
                $this->addError('callDate', ValidationMessageHelper::DATE_IN_PAST);
            }
        }

        public function beforeValidate()
        {
            if(in_array($this->scenario, array('insert')))
            {
                $this->callDate = $this->callDateDay.'.'.$this->callDateMonth.'.'.$this->callDateYear;
                $this->callTime = $this->callTimeHours.':'.$this->callTimeMinutes;
            }

            return parent::beforeValidate();
        }

        public function afterValidate()
        {
            if(in_array($this->scenario, array('insert', 'api_insert', 'missing')))
            {
                UserVerificationRequest::model()->deleteAllByAttributes(array(
                    'userId' => $this->userId,
                    'messenger' => $this->messenger,
                    'messengerLogin' => $this->messengerLogin,
                    'status' => UserVerificationRequest::STATUS_OPENED
                ));
                $this->callDate = strtotime($this->callDate.' '.$this->callTime);
            }
            
            if(in_array($this->scenario, array('photo_verification', 'api_photo_verification', 'photo_verification_with_photo_string')))
            {
                UserVerificationRequest::model()->deleteAllByAttributes(array(
                    'userId' => $this->userId,
                    'status' => UserVerificationRequest::STATUS_OPENED
                ));
                $this->callDate = strtotime($this->callDate.' '.$this->callTime);
            }

            if(in_array($this->scenario, array('update')))
            {
                $this->attachmentFile = CUploadedFile::getInstance($this, 'attachmentFile');
                if($this->attachmentFile)
                {
                    $this->attachment = Yii::getPathOfAlias('webroot.content.validation_attachments').DIRECTORY_SEPARATOR.md5(time().$this->attachmentFile->getName()).'.'.$this->attachmentFile->getExtensionName();
                    $this->attachment = str_replace(Yii::getPathOfAlias('webroot'), '', $this->attachment);
                    $this->attachment = str_replace('\\', '/', $this->attachment);
                }
            }
            
            if(in_array($this->scenario, array('photo_verification', 'api_photo_verification')))
            {
                if($this->attachmentFile)
                {
                    $this->photoAttachment = Yii::getPathOfAlias('webroot.content.validation_attachments').DIRECTORY_SEPARATOR.md5(time().$this->attachmentFile->getName()).'.'.$this->attachmentFile->getExtensionName();
                    $this->photoAttachment = str_replace(Yii::getPathOfAlias('webroot'), '', $this->photoAttachment);
                    $this->photoAttachment = str_replace('\\', '/', $this->photoAttachment);
                }
            }

            parent::afterValidate();
        }

        public function afterSave()
        {
            if(in_array($this->scenario, array('update')) && $this->attachmentFile)
            {
                $this->attachmentFile->saveAs(Yii::getPathOfAlias('webroot').$this->attachment);
            }
            if(in_array($this->scenario, array('photo_verification', 'api_photo_verification')) && $this->attachmentFile)
            {
                $this->attachmentFile->saveAs(Yii::getPathOfAlias('webroot').$this->photoAttachment);
            }
            parent::afterSave();
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'user' => array(self::BELONGS_TO, 'User', 'userId'),
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'userVerificationRequestId' => Yii::t('application', 'ID'),
                'userId' => Yii::t('application', 'Пользователь'),
                'employeeId' => Yii::t('application', 'Сотрудник'),
                'messenger' => Yii::t('application', 'Мессенджер'),
                'messengerLogin' => Yii::t('application', 'Логин мессенджера'),
                'callDate' => Yii::t('application', 'Дата'),
                'callDateYear' => Yii::t('application', 'Год'),
                'callDateMonth' => Yii::t('application', 'Месяц'),
                'callDateDay' => Yii::t('application', 'День'),
                'callTime' => Yii::t('application', 'Время'),
                'callTimeHours' => Yii::t('application', 'Часы'),
                'callTimeMinutes' => Yii::t('application', 'Минуты'),
                'status' => Yii::t('application', 'Статус'),
                'comment' => Yii::t('application', 'Комментарий'),
                'attachment' => Yii::t('application', 'Приложение'),
                'attachmentFile' => Yii::t('application', 'Файл'),
                'photoAttachment' => Yii::t('application', 'Фото'),
                'favoriteCigaretteBrand' => Yii::t('application', 'Любимый табачный бренд'),
                'isVerified' => Yii::t('application', 'Верифицирован'),
                'isMissed' => Yii::t('application', 'Пропущенный вызов'),
                'isPhotoVerification' => Yii::t('application', 'Верификация по фото'),
                'dateCreated' => Yii::t('application', 'Дата создания'),
                'dateClosed' => Yii::t('application', 'Дата закрытия'),
            );
        }

        /**
         * Retrieves a list of models based on the current search/filter conditions.
         * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
         */
        public function search()
        {
            $criteria = new CDbCriteria();
            $criteria->with = array('user');

            $criteria->compare('userVerificationRequestId', $this->userVerificationRequestId, true);
            $criteria->compare('user.name', $this->searchUser->name, true);
            $criteria->compare('messengerLogin', $this->messengerLogin, true);
            $criteria->compare('callDate', strtotime($this->callDate), true);

            if($this->scenario == 'search')
            {
                $criteria->addColumnCondition(array('status' => UserVerificationRequest::STATUS_OPENED));
                $criteria->compare('dateCreated', strtotime($this->dateCreated), true);
            }
            else
            {
                $criteria->addColumnCondition(array('status' => UserVerificationRequest::STATUS_CLOSED));
                $criteria->compare('dateClosed', strtotime($this->dateClosed), true);
                if ($this->isVerified !== null && $this->isVerified !== '') {
                    $criteria->addColumnCondition(array('t.isVerified' => $this->isVerified));
                }
            }

            return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
                'sort'=>array(
                  'defaultOrder'=>'dateCreated DESC',
                )
            ));
        }

    }
    