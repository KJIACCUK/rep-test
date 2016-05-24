<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $userId
 * @property integer $accountId
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $phoneCode
 * @property string $birthday
 * @property string $image
 * @property string $messenger
 * @property string $messengerLogin
 * @property string $favoriteMusicGenre
 * @property string $favoriteCigaretteBrand
 * @property string $login
 * @property string $password
 * @property string $points
 * @property integer $isFilled
 * @property integer $isVerified
 * @property integer $isBluestone
 *
 * The followings are the available model relations:
 * @property Account $account
 * @property UserApiToken[] $apiTokens
 * @property UserFriend[] $userFriends
 * @property UserFriendRequest[] $userFriendRequests
 * @property User[] $friends
 * @property User[] $friendRequests
 * @property UserPushToken[] $pushTokens
 * @property UserSocial[] $socials
 * @property UserVerification $verification
 * @property UserVerificationRequest[] $verificationRequests
 * @property UserNotificationSetting[] $settings
 * @property integer $friendsCount
 * @property integer $friendRequestsCount
 * @property integer $unreadedMessagesCount
 * @property integer $isOnline
 */
class User extends CActiveRecord
{
    public $birthdayYear;
    public $birthdayMonth;
    public $birthdayDay;
    public $passwordConfirm;
    public $hasPassword;
    public $imageFile;
    public $oldPassword;
    public $oldLogin;
    public $newPassword;
    public $unreadedNotificationsCount;
    public $subscriptionsCount;
    public $firstname;
    public $lastname;
    public $needChangeLogin = false;

    /**
     *
     * @var Event[]
     */
    public $eventsToDelete;

    /**
     *
     * @var Account
     */
    public $searchAccount;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
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
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            // registration
            array('firstname, lastname, email, birthday, birthdayDay, birthdayMonth, birthdayYear, phoneCode, phone, password', 'required', 'on' => 'registration'),
            array('email', 'length', 'max' => 255, 'on' => 'registration'),
            array('firstname, lastname', 'length', 'max' => 125, 'on' => 'registration'),
            array('password', 'length', 'min' => 6, 'max' => 255, 'on' => 'registration'),
            array('passwordConfirm', 'compare', 'compareAttribute' => 'password', 'on' => 'registration', 'message' => Yii::t('application', 'Введенные пароли не совпадают')),
            array('birthday', 'date', 'format' => 'd.M.yyyy', 'on' => 'registration'),
            array('birthday', 'validateAge', 'on' => 'registration'),
            array('phone', 'length', 'min' => 7, 'max' => 7, 'on' => 'registration'),
            array('email', 'email', 'on' => 'registration'),
            array('email', 'validateEmailUnique', 'on' => 'registration'),
            array('phone', 'numerical', 'integerOnly' => true, 'min' => 0, 'on' => 'registration', 'tooSmall' => Yii::t('application', 'Неверное значение номера телефона')),
            array('phoneCode', 'in', 'range' => array_keys(Yii::app()->params['phoneCodes']), 'on' => 'registration'),
            array('name, login, accountId, image, isFilled, isVerified', 'safe', 'on' => 'registration'),
            // profile_complete
            array('firstname, lastname, email, birthday, birthdayDay, birthdayMonth, birthdayYear, phoneCode, phone', 'required', 'on' => 'profile_complete'),
            array('firstname, lastname', 'length', 'max' => 125, 'on' => 'profile_complete'),
            array('email', 'length', 'max' => 255, 'on' => 'profile_complete'),
            array('password', 'validatePasswordExists', 'on' => 'profile_complete'),
            array('password', 'length', 'min' => 6, 'max' => 255, 'on' => 'profile_complete', 'allowEmpty' => true),
            array('passwordConfirm', 'compare', 'compareAttribute' => 'password', 'allowEmpty' => true, 'on' => 'profile_complete', 'message' => Yii::t('application', 'Введенные пароли не совпадают')),
            array('birthday', 'date', 'format' => 'd.M.yyyy', 'on' => 'profile_complete'),
            array('birthday', 'validateAge', 'on' => 'profile_complete'),
            array('phone', 'length', 'min' => 7, 'max' => 7, 'on' => 'profile_complete'),
            array('email', 'email', 'on' => 'profile_complete'),
            array('email', 'validateEmailUnique', 'on' => 'profile_complete'),
            array('phone', 'numerical', 'integerOnly' => true, 'min' => 0, 'on' => 'profile_complete', 'tooSmall' => Yii::t('application', 'Неверное значение номера телефона')),
            array('phoneCode', 'in', 'range' => array_keys(Yii::app()->params['phoneCodes']), 'on' => 'profile_complete'),
            array('name, login, isFilled', 'safe', 'on' => 'profile_complete'),
            // update
            array('firstname, lastname, email, birthday, birthdayDay, birthdayMonth, birthdayYear, phoneCode, phone', 'required', 'on' => 'update'),
            array('firstname, lastname', 'length', 'max' => 125, 'on' => 'update'),
            array('email, messengerLogin', 'length', 'max' => 255, 'on' => 'update'),
            array('newPassword', 'length', 'min' => 6, 'max' => 255, 'allowEmpty' => true, 'on' => 'update'),
            array('oldPassword', 'validateOldPassword', 'on' => 'update'),
            array('birthday', 'date', 'format' => 'd.M.yyyy', 'on' => 'update'),
            array('birthday', 'validateAge', 'on' => 'update'),
            array('phone', 'length', 'min' => 7, 'max' => 7, 'on' => 'update'),
            array('email', 'email', 'on' => 'update'),
            array('email', 'validateEmailUnique', 'on' => 'update'),
            array('phone', 'numerical', 'integerOnly' => true, 'min' => 0, 'on' => 'update', 'tooSmall' => Yii::t('application', 'Неверное значение номера телефона')),
            array('phoneCode', 'in', 'range' => array_keys(Yii::app()->params['phoneCodes']), 'on' => 'update'),
            array('messenger', 'in', 'range' => array_keys(Yii::app()->params['messengers']), 'on' => 'update'),
            array('favoriteMusicGenre', 'in', 'range' => Yii::app()->params['musicGenres'], 'allowEmpty' => true, 'on' => 'update'),
            //array('favoriteCigaretteBrand', 'in', 'range' => Yii::app()->params['cigaretteBrands'], 'allowEmpty' => true, 'on' => 'update'),
            array('isFilled', 'safe', 'on' => 'update'),
            // update_messenger
            array('messenger, messengerLogin', 'required', 'on' => 'update_messenger'),
            array('messengerLogin', 'length', 'max' => 255, 'on' => 'update_messenger'),
            array('messenger', 'in', 'range' => array_keys(Yii::app()->params['messengers']), 'on' => 'update_messenger'),
            // update_image
            array('imageFile', 'file', 'types' => 'png, jpg, jpe, jpeg', 'maxSize' => 2 * 1024 * 1024, 'maxFiles' => 1, 'allowEmpty' => false, 'on' => 'update_image'),
            // api_registration
            ApiValidatorHelper::required('firstname, lastname, email, birthday, phoneCode, phone, password', 'api_registration'),
            ApiValidatorHelper::length('email', null, 255, 'api_registration'),
            ApiValidatorHelper::length('firstname, lastname', null, 125, 'api_registration'),
            ApiValidatorHelper::length('password', 6, 255, 'api_registration'),
            ApiValidatorHelper::length('phone', 7, 7, 'api_registration'),
            ApiValidatorHelper::email('email', 'api_registration'),
            array('email', 'validateEmailUnique', 'on' => 'api_registration'),
            ApiValidatorHelper::date('birthday', 'dd.MM.yyyy', 'api_registration'),
            array('birthday', 'validateAge', 'on' => 'api_registration'),
            ApiValidatorHelper::type('phone', 'integer', 'api_registration'),
            ApiValidatorHelper::in('phoneCode', array_keys(Yii::app()->params['phoneCodes']), 'api_registration'),
            //ApiValidatorHelper::in('favoriteCigaretteBrand', Yii::app()->params['cigaretteBrands'], 'api_registration') + array('allowEmpty' => true),
            ApiValidatorHelper::safe('name, login, accountId, image, isFilled, isVerified', 'api_registration'),
            // api_social_registration
            array('name', 'required', 'on' => 'api_social_registration'),
            array('name', 'filter', 'filter' => array($this, 'filterSubstr255'), 'on' => 'api_social_registration'),
            array('email', 'filter', 'filter' => array($this, 'filterEmail'), 'on' => 'api_social_registration'),
            array('birthday', 'filter', 'filter' => array($this, 'filterBirthday'), 'on' => 'api_social_registration'),
            array('birthday', 'validateAge', 'on' => 'api_social_registration'),
            array('phone', 'filter', 'filter' => array($this, 'filterPhone'), 'on' => 'api_social_registration'),
            array('phoneCode', 'filter', 'filter' => array($this, 'filterPhoneCode'), 'on' => 'api_social_registration'),
            array('image', 'safe', 'on' => 'api_social_registration'),
            // api_profile_complete
            ApiValidatorHelper::required('firstname, lastname, email, birthday, phoneCode, phone', 'api_profile_complete'),
            ApiValidatorHelper::length('email', null, 255, 'api_profile_complete'),
            ApiValidatorHelper::length('firstname, lastname', null, 125, 'api_profile_complete'),
            ApiValidatorHelper::length('password', 6, 255, 'api_profile_complete') + array('allowEmpty' => true),
            array('password', 'validatePasswordExists', 'on' => 'api_profile_complete'),
            ApiValidatorHelper::length('phone', 7, 7, 'api_profile_complete'),
            ApiValidatorHelper::email('email', 'api_profile_complete'),
            array('email', 'validateEmailUnique', 'on' => 'api_profile_complete'),
            ApiValidatorHelper::date('birthday', 'dd.MM.yyyy', 'api_profile_complete'),
            array('birthday', 'validateAge', 'on' => 'api_profile_complete'),
            ApiValidatorHelper::type('phone', 'integer', 'api_profile_complete'),
            ApiValidatorHelper::in('phoneCode', array_keys(Yii::app()->params['phoneCodes']), 'api_profile_complete'),
            ApiValidatorHelper::safe('name, login', 'api_profile_complete'),
            // api_update_messenger
            ApiValidatorHelper::required('messenger, messengerLogin', 'api_update_messenger'),
            ApiValidatorHelper::length('messengerLogin', null, 255, 'api_update_messenger'),
            ApiValidatorHelper::in('messenger', array_keys(Yii::app()->params['messengers']), 'api_update_messenger'),
            // api_update
            ApiValidatorHelper::required('firstname, lastname, email, birthday, phoneCode, phone', 'api_update'),
            ApiValidatorHelper::length('firstname, lastname', null, 125, 'api_update'),
            ApiValidatorHelper::length('email, messengerLogin', null, 255, 'api_update'),
            ApiValidatorHelper::length('phone', 7, 7, 'api_update'),
            ApiValidatorHelper::email('email', 'api_update'),
            array('email', 'validateEmailUnique', 'on' => 'api_update'),
            ApiValidatorHelper::date('birthday', 'dd.MM.yyyy', 'api_update'),
            array('birthday', 'validateAge', 'on' => 'api_update'),
            ApiValidatorHelper::type('phone', 'integer', 'api_update'),
            ApiValidatorHelper::in('phoneCode', array_keys(Yii::app()->params['phoneCodes']), 'api_update'),
            ApiValidatorHelper::in('messenger', array_keys(Yii::app()->params['messengers']), 'api_update') + array('allowEmpty' => true),
            ApiValidatorHelper::in('favoriteMusicGenre', Yii::app()->params['musicGenres'], 'api_update') + array('allowEmpty' => true),
            //ApiValidatorHelper::in('favoriteCigaretteBrand', Yii::app()->params['cigaretteBrands'], 'api_update') + array('allowEmpty' => true),
            // api_update_image
            ApiValidatorHelper::file('imageFile', 'png, jpg, jpe, jpeg', null, 1 * 1024 * 1024, 1, 'api_update_image') + array('allowEmpty' => false),
            // import
            array('name', 'required', 'on' => 'import'),
            array('name, login', 'length', 'max' => 255, 'on' => 'import'),
            array('birthday', 'date', 'format' => 'd.M.yy', 'on' => 'import'),
            array('login', 'unique', 'on' => 'import'),
            array('accountId, image, password, isFilled, isVerified', 'safe', 'on' => 'import'),
            // import_update
            array('login, password', 'required', 'on' => 'import_update'),
            array('name, login', 'length', 'max' => 255, 'on' => 'import_update'),
            array('birthday', 'date', 'format' => 'd.M.yy', 'on' => 'import_update'),
            // search
            array('userId, name, email, login, isFilled, isVerified', 'safe', 'on' => 'search'),
            // generate_verified
            array('login, password', 'required', 'on' => 'generate_verified'),
            array('login', 'unique', 'on' => 'generate_verified'),
            array('password', 'length', 'min' => 6, 'max' => 255, 'on' => 'generate_verified'),
        );
    }

    public function validateEmailUnique()
    {
        if ($this->email) {
            $criteria = new CDbCriteria();
            if (!$this->isNewRecord) {
                $criteria->addNotInCondition('userId', array($this->userId));
            }
            $criteria->addCondition('email = :email OR login = :email');
            $criteria->params[':email'] = $this->email;
            if (User::model()->exists($criteria)) {
                if (in_array($this->scenario, array('registration', 'profile_complete', 'update'))) {
                    $this->addError('email', Yii::t('application', 'Данный E-mail использовать нельзя'));
                } elseif (in_array($this->scenario, array('api_registration', 'api_profile_complete', 'api_update'))) {
                    $this->addError('email', ValidationMessageHelper::NOT_UNIQUE);
                }
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
                if (in_array($this->scenario, array('registration', 'profile_complete', 'update'))) {
                    $this->addError('birthday', Yii::t('application', 'Использование приложения разрешено лицам, достигшим 18 лет'));
                } elseif (in_array($this->scenario, array('api_registration', 'api_social_registration', 'api_profile_complete'))) {
                    $this->addError('birthday', ValidationMessageHelper::INVALID_AGE);
                }
            }
        }
    }

    public function filterSubstr255($value)
    {
        return mb_substr($value, 0, 255);
    }

    public function filterEmail($value)
    {
        $value = mb_substr($value, 0, 255);
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            if (!User::model()->countByAttributes(array('email' => $value))) {
                return $value;
            }
        }

        return null;
    }

    public function filterBirthday($value)
    {
        if ($value) {
            $value = strtotime($value);
            if ($value) {
                return $value;
            }
        }

        return null;
    }

    public function filterPhone($value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        if ($value) {
            return $value;
        }
        return null;
    }

    public function filterPhoneCode($value)
    {
        return null;
    }

    public function validatePasswordExists()
    {
        if (!$this->oldPassword && empty($this->password)) {
            if ($this->scenario == 'api_profile_complete') {
                $this->addError('password', ValidationMessageHelper::REQUIRED);
            } elseif ($this->scenario == 'profile_complete') {
                $this->addError('newPassword', Yii::t('application', 'Необходимо заполнить поле "Пароль"'));
            }
        }
    }

    public function validateOldPassword()
    {
        if ($this->newPassword) {
            if (!$this->oldPassword) {
                $this->addError('oldPassword', Yii::t('application', 'Введите старый пароль для смены пароля'));
            } elseif ($this->password != CommonHelper::md5($this->oldPassword)) {
                $this->addError('oldPassword', Yii::t('application', 'Неверное значение старого пароля'));
            }
        }
    }

    protected function beforeValidate()
    {
        if (in_array($this->scenario, array('registration', 'api_registration', 'profile_complete', 'api_profile_complete', 'update', 'api_update'))) {
            $this->name = $this->lastname.' '.$this->firstname;
        }

        if (in_array($this->scenario, array('registration', 'api_registration', 'import'))) {
            $this->image = UserHelper::getDefaultImage();
        }

        if (in_array($this->scenario, array('registration', 'update', 'profile_complete'))) {
            $this->birthday = $this->birthdayDay.'.'.$this->birthdayMonth.'.'.$this->birthdayYear;
        }

        if (in_array($this->scenario, array('registration', 'api_registration', 'api_social_registration'))) {
            $this->isVerified = 0;
        }

        if (in_array($this->scenario, array('generate_verified'))) {
            $this->isVerified = 1;
        }
        
        if (in_array($this->scenario, array('registration', 'api_registration'))) {
            $this->login = $this->email;
        }
        
        if (in_array($this->scenario, array('profile_complete', 'api_profile_complete', 'update', 'api_update'))) {
            if ($this->needChangeLogin) {
                $this->login = $this->email;
            } else {
                $this->login = $this->oldLogin;
            }
        }

        return parent::beforeValidate();
    }

    protected function afterValidate()
    {
        if (in_array($this->scenario, array('registration', 'api_registration', 'import', 'import_update', 'generate_verified'))) {
            $this->password = CommonHelper::md5($this->password);
            if ($this->birthday) {
                $this->birthday = strtotime($this->birthday.' midnight');
            }
        }

        if (in_array($this->scenario, array('profile_complete'))) {
            $this->birthday = strtotime($this->birthday.' midnight');
        }

        if ($this->scenario == 'api_social_registration') {
            if ($this->image) {
                if (($img = WideImage::load($this->image))) {
                    $imagePath = Yii::getPathOfAlias('webroot.content.images.users').'/'.CommonHelper::generateImageName($this->accountId).'.png';
                    $imagePath = str_replace('\\', '/', $imagePath);

                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }

                    $img->saveToFile($imagePath);

                    $this->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
                } else {
                    $this->image = UserHelper::getDefaultImage();
                }
            } else {
                $this->image = UserHelper::getDefaultImage();
            }
        }

        if (in_array($this->scenario, array('update'))) {
            if ($this->newPassword) {
                $this->password = CommonHelper::md5($this->newPassword);
            }
        }

        if (in_array($this->scenario, array('profile_complete', 'api_profile_complete'))) {
            if ($this->password) {
                $this->password = CommonHelper::md5($this->password);
            } else {
                $this->password = $this->oldPassword;
            }
        }

        if (in_array($this->scenario, array('api_profile_complete', 'update', 'api_update'))) {
            $this->birthday = strtotime($this->birthday.' midnight');
        }

        if (in_array($this->scenario, array('update_image', 'api_update_image'))) {
            if ($this->imageFile) {
                $imagePath = Yii::getPathOfAlias('webroot.content.images.users').'/'.CommonHelper::generateImageName($this->accountId).'.'.$this->imageFile->getExtensionName();
                $imagePath = str_replace('\\', '/', $imagePath);
                $this->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
            }
        }

        $this->isFilled = UserHelper::getIsFilled($this);

        parent::afterValidate();
    }

    public function afterSave()
    {
        if (in_array($this->scenario, array('update_image', 'api_update_image'))) {
            if ($this->imageFile) {
                $imagePath = Yii::getPathOfAlias('webroot').$this->image;

                if ((UserHelper::getDefaultImage() != $this->image) && file_exists($imagePath)) {
                    unlink($imagePath);
                }

                $this->imageFile->saveAs($imagePath);
            }
        }

        parent::afterSave();
    }

    public function beforeDelete()
    {
        $criteria = new CDbCriteria();
        $criteria->index = 'eventId';
        $criteria->addColumnCondition(array('userId' => $this->userId));
        $this->eventsToDelete = Event::model()->findAll($criteria);
        return parent::beforeDelete();
    }

    public function afterDelete()
    {
        if ($this->image && file_exists(Yii::getPathOfAlias('webroot').$this->image) && ($this->image != UserHelper::getDefaultImage())) {
            $userDir = Yii::getPathOfAlias('webroot.content.images.users').'/';
            unlink(Yii::getPathOfAlias('webroot').$this->image);
            ImageHelper::cleanCacheDir($userDir, str_replace('/content/images/users/', '', $this->image));

            foreach ($this->eventsToDelete as $item) {
                $item->delete();
            }
        }
        parent::afterDelete();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'account' => array(self::BELONGS_TO, 'Account', 'accountId'),
            'apiTokens' => array(self::HAS_MANY, 'UserApiToken', 'userId'),
            'userFriends' => array(self::HAS_MANY, 'UserFriend', 'userId'),
            'userFriendRequests' => array(self::HAS_MANY, 'UserFriendRequest', 'userId'),
            'friends' => array(self::HAS_MANY, 'User', array('friendId' => 'userId'), 'through' => 'userFriends', 'order' => 'friends.name ASC'),
            'friendRequests' => array(self::HAS_MANY, 'User', array('recipientId' => 'userId'), 'through' => 'userFriendRequests', 'order' => 'friendRequests.name ASC'),
            'pushTokens' => array(self::HAS_MANY, 'UserPushToken', 'userId'),
            'socials' => array(self::HAS_MANY, 'UserSocial', 'userId', 'order' => 'socials.dateCreated DESC'),
            'verification' => array(self::HAS_ONE, 'UserVerification', 'userId'),
            'verificationRequests' => array(self::HAS_MANY, 'UserVerificationRequest', 'userId'),
            'settings' => array(self::HAS_MANY, 'UserNotificationSetting', 'userId'),
            // counters
            'friendsCount' => array(self::STAT, 'UserFriend', 'userId'),
            'friendRequestsCount' => array(self::STAT, 'UserFriendRequest', 'userId'),
            'unreadedMessagesCount' => array(self::STAT, 'UserMessage', 'recipientId', 'condition' => 'isReaded = 0'),
            // is online
            'isOnline' => array(self::STAT, 'UserOnline', 'userId', 'condition' => 'isOnline = 1'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'userId' => Yii::t('application', 'ID'),
            'accountId' => Yii::t('application', 'Аккаунт'),
            'name' => Yii::t('application', 'Имя и Фамилия'),
            'firstname' => Yii::t('application', 'Имя'),
            'lastname' => Yii::t('application', 'Фамилия'),
            'email' => Yii::t('application', 'E-mail'),
            'phone' => Yii::t('application', 'Телефон'),
            'phoneCode' => Yii::t('application', 'Код оператора'),
            'birthday' => Yii::t('application', 'Дата рождения'),
            'birthdayYear' => Yii::t('application', 'Год'),
            'birthdayMonth' => Yii::t('application', 'Месяц'),
            'birthdayDay' => Yii::t('application', 'День'),
            'image' => Yii::t('application', 'Аватар'),
            'messenger' => Yii::t('application', 'Мессенджер'),
            'messengerLogin' => Yii::t('application', 'Логин мессенджера'),
            'favoriteMusicGenre' => Yii::t('application', 'Любимая музыка'),
            'favoriteCigaretteBrand' => Yii::t('application', 'Любимый табачный бренд'),
            'login' => Yii::t('application', 'Логин'),
            'password' => Yii::t('application', 'Пароль'),
            'points' => Yii::t('application', 'Баллы'),
            'passwordConfirm' => Yii::t('application', 'Подтвердить пароль'),
            'oldPassword' => Yii::t('application', 'Старый пароль'),
            'newPassword' => Yii::t('application', 'Новый пароль'),
            'isFilled' => Yii::t('application', 'Профиль полностью заполнен'),
            'isVerified' => Yii::t('application', 'Профиль верифицирован'),
            'imageFile' => Yii::t('application', 'Изображение')
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->with = array('account');

        $criteria->compare('userId', $this->userId, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('login', $this->login, true);
        $criteria->compare('isFilled', $this->isFilled);
        $criteria->compare('isVerified', $this->isVerified);
        $criteria->compare('account.isActive', $this->searchAccount->isActive);
        $criteria->compare('account.dateCreated', strtotime($this->searchAccount->dateCreated.' midnigth'));

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort'=>array(
              'defaultOrder'=>'account.dateCreated DESC',
            )
        ));
    }

    public function history()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $this->userId);
        $dataProvider = new CActiveDataProvider('PointUser', array(
          'criteria' => $criteria,
          'sort'=>array(
            'defaultOrder'=>'dateCreated DESC',
          )
        ));

        return $dataProvider;
    }
}