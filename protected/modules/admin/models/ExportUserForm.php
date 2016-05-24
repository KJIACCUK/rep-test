<?php

    require Yii::getPathOfAlias('application.vendor').'/autoload.php';
    require Yii::getPathOfAlias('application.vendor').'/os/php-excel/PHPExcel/CachedObjectStorageFactory.php';
    require Yii::getPathOfAlias('application.vendor').'/os/php-excel/PHPExcel/Settings.php';

    class ExportUserForm extends CFormModel
    {

        const STATUS_ALL = 'all';
        const STATUS_NEW = 'new';
        const STATUS_OLD = 'old';

        public $registrationDateStart;
        public $registrationDateEnd;
        public $registrationDatIn;
        public $registrationDatOut;
        public $status = self::STATUS_ALL;
        public $isVerified = false;
        public $isFilled = false;
        public $isDigestSubscribed = false;
        public $fields = array('userId', 'firstName', 'surname', 'email', 'phone', 'birthday', 'login', 'verificationDate', 'last_access', 'answers_count');
        public $offset = 0;
        public $limit = 10000;
        public $filename = 'budutam_users';
        public $writeHeaders = true;

        public function rules()
        {
            return array(
                array('status, filename', 'required'),
                array('filename', 'length', 'max' => 100),
                array('status', 'in', 'range' => array(self::STATUS_ALL, self::STATUS_NEW, self::STATUS_OLD)),
                array('fields', 'type', 'type' => 'array', 'allowEmpty' => false),
                array('registrationDateStart, registrationDateEnd, registrationDatIn, registrationDatOut', 'date', 'format' => 'd.M.yyyy', 'allowEmpty' => true),
                array('offset', 'numerical', 'integerOnly' => true, 'max' => 10000),
                array('limit', 'numerical', 'integerOnly' => true),
                array('isFilled, isVerified, isDigestSubscribed, writeHeaders', 'boolean')
            );
        }

        public function attributeLabels()
        {
            return array(
                'status' => Yii::t('application', 'Статус'),
                'isVerified' => Yii::t('application', 'Верифицирован'),
                'isFilled' => Yii::t('application', 'Профиль заполнен'),
                'isDigestSubscribed' => Yii::t('application', 'Подписан на рассылку дайджеста новостей'),
                'registrationDateStart' => Yii::t('application', 'От'),
                'registrationDateEnd' => Yii::t('application', 'До'),
                'registrationDatIn' => Yii::t('application', 'От'),
                'registrationDatOut' => Yii::t('application', 'До'),
                'fields' => Yii::t('application', 'Поля'),
                'offset' => Yii::t('application', 'От'),
                'limit' => Yii::t('application', 'До'),
                'filename' => Yii::t('application', 'Имя файла'),
                'writeHeaders' => Yii::t('application', 'Заголовки колонок')
            );
        }

        public function getStatusesList()
        {
            return array(
                self::STATUS_ALL => Yii::t('application', 'Все'),
                self::STATUS_OLD => Yii::t('application', 'Пользователи Bluestone.by'),
                self::STATUS_NEW => Yii::t('application', 'Новые пользователи'),
            );
        }

        public function getFieldsList()
        {
            return array(
                'userId' => Yii::t('application', 'ID'),
                'firstName' => Yii::t('application', 'Имя'),
                'surname' => Yii::t('application', 'Фамилия'),
                'email' => Yii::t('application', 'E-mail'),
                'phone' => Yii::t('application', 'Телефон'),
                'birthday' => Yii::t('application', 'Дата рождения'),
                'messenger' => Yii::t('application', 'Мессенджер'),
                'favoriteMusicGenre' => Yii::t('application', 'Любимая музыка'),
                'favoriteCigaretteBrand' => Yii::t('application', 'Любимый табачный бренд'),
                'login' => Yii::t('application', 'Логин'),
                'points' => Yii::t('application', 'Баллы'),
                'isFilled' => Yii::t('application', 'Профиль полностью заполнен'),
                'isVerified' => Yii::t('application', 'Профиль верифицирован'),
                'verificationDate' => Yii::t('application', 'Дата верификации'),
                'last_access' => Yii::t('application', 'Последнее посещение ПРО-раздела'),
                'answers_count' => Yii::t('application', 'Количество ответов на вопросы PRO-раздела'),
                'firstTimeActivated' => Yii::t('application', 'Первый раз активирован'),
            );
        }

        public function export()
        {
            set_time_limit(0);
            $criteria = new CDbCriteria();
            $criteria->alias = 't';

            if(in_array('phone', $this->fields))
            {
                $this->fields[] = 'phoneCode';
            }

            if(in_array('messenger', $this->fields))
            {
                $this->fields[] = 'messengerLogin';
            }
            
            if(in_array('verificationDate', $this->fields))
            {
                $criteria->join = 'LEFT JOIN '.UserVerification::model()->tableName().' v ON(t.userId = v.userId)';
            }

            $headers = array();
            $fieldsList = $this->getFieldsList();
            $select = array();

            foreach($this->fields as $i => $field)
            {
                if (in_array($field, array('firstName', 'surname'))) {
                    $select[$i] = 't.name';
                } elseif ($field == 'verificationDate') {
                    $select[$i] = 'v.dateCreated AS verificationDate';
                } elseif ($field == 'last_access') {
                    // Join last_access data from PointUser
                    $criteria->join .= ' LEFT JOIN '.PointUser::model()->tableName().' pu ON(t.userId = pu.userId)';
                    $criteria->addCondition('pu.pointId = 3');

                    $select[$i] = 'MAX(pu.dateCreated) AS last_access';
                } elseif ($field == 'answers_count') {
                    // Join last_access data from PointUser
                    $criteria->join .= ' LEFT JOIN '.MarketingResearchUserAnswer::model()->tableName().' ua ON(t.userId = ua.userId)';

                    $select[$i] = 'COUNT(DISTINCT ua.marketingResearchId) as answers_count';
                } else {
                    $select[$i] = 't.'.$field;
                }
                
                if(isset($fieldsList[$field]))
                {
                    $headers[] = $fieldsList[$field];
                }
            }

            $criteria->select = implode(',', $select);

            if($this->status == self::STATUS_NEW)
            {
                $criteria->addCondition('t.isBluestone = 0');
            }
            elseif($this->status == self::STATUS_OLD)
            {
                $criteria->addCondition('t.isBluestone = 1');
            }

            if($this->registrationDateStart || $this->registrationDateEnd)
            {
                $criteria->join .= ' INNER JOIN '.Account::model()->tableName().' a ON (t.accountId = a.accountId)';
            }

            if($this->registrationDateStart)
            {
                $criteria->addCondition('a.dateCreated >= :registrationDateStart');
                $criteria->params[':registrationDateStart'] = strtotime($this->registrationDateStart.' midnight');
            }

            if($this->registrationDateEnd)
            {
                $criteria->addCondition('a.dateCreated <= :registrationDateEnd');
                $criteria->params[':registrationDateEnd'] = strtotime($this->registrationDateEnd.' 23:59:59');
            }

            if($this->registrationDatIn)
            {
                $criteria->addCondition('t.firstTimeActivated >= :registrationDatIn');
                $criteria->params[':registrationDatIn'] = strtotime($this->registrationDatIn.' midnight');
            }

            if($this->registrationDatOut)
            {
                $criteria->addCondition('t.firstTimeActivated <= :registrationDatOut');
                $criteria->params[':registrationDatOut'] = strtotime($this->registrationDatOut.' 23:59:59');
            }

            if($this->isFilled)
            {
                $criteria->addColumnCondition(array('t.isFilled' => 1));
            }

            if($this->isVerified)
            {
                $criteria->addColumnCondition(array('t.isVerified' => 1));
            }

            if($this->isDigestSubscribed)
            {
                $criteria->addCondition('t.userId IN (SELECT uns.userId FROM '.UserNotificationSetting::model()->tableName().' uns WHERE uns.settingKey = "'.UserNotificationSetting::SETTING_MONTLY_DIGEST.'" AND isChecked = 1)');
            }

            $criteria->order = 't.userId ASC';
            $criteria->offset = $this->offset;
            $criteria->group = 't.userId';
            $criteria->limit = 100;


            $row = 1;

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cachecache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '512MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $phpExcel = new PHPExcel();
            $phpExcel->getProperties()->setCreator(Yii::t('application', 'Будутам'))
                    ->setLastModifiedBy(Yii::t('application', 'Будутам'))
                    ->setTitle(Yii::t('application', 'Экспорт пользователей Будутам'));
            
            $phpExcel->getDefaultStyle()
                    ->getNumberFormat()
                    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            $phpExcel->setActiveSheetIndex(0);
            $sheet = $phpExcel->getActiveSheet();
            $sheet->setTitle(Yii::t('application', 'Пользователи'));

            if($this->writeHeaders)
            {
                $sheet->fromArray($headers, null, 'A'.$row);
                $row++;
            }

            $schema = Yii::app()->db->schema;
            $builder = $schema->commandBuilder;
            $tableName = User::model()->tableName();
            $isFound = true;
            $repeatsCount = ceil($this->limit / 100);

            while($isFound && ($repeatsCount > 0))
            {
                $command = $builder->createFindCommand($schema->getTable($tableName), $criteria);
                //Yii::log($command->getText(), CLogger::LEVEL_WARNING, 'ExportuserForm.export()');
                $users = $command->queryAll();

                if($users)
                {
                    foreach($users as $i => $item)
                    {
                        $users[$i] = $this->prepareUserFields($item);
                    }

                    $sheet->fromArray($users, null, 'A'.$row);
                    $row += count($users);

                    $criteria->offset += 100;
                    $repeatsCount--;
                }
                else
                {
                    $isFound = false;
                }
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$this->filename.'.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
            $objWriter->setPreCalculateFormulas(false);
            $objWriter->save('php://output');
        }

        private function prepareUserFields($user)
        {
            $data = $user;
            $nameArr = array('', '');
            if (isset($data['name'])) {
                $tmp = explode(' ', $data['name'], 2);
                $nameArr = array(isset($tmp[0])?trim($tmp[0]):'', isset($tmp[1])?trim($tmp[1]):'');
                unset($data['name']);
            }

            if (in_array('firstName', $this->fields)) {
                $data['firstName'] = $nameArr[0];
            }
            if (in_array('surname', $this->fields)) {
                $data['surname'] = $nameArr[1];
            }
            if(isset($data['phone']))
            {
                $data['phone'] = '('.$data['phoneCode'].') '.$data['phone'];
                unset($data['phoneCode']);
            }

            if(isset($data['birthday']))
            {
                $data['birthday'] = date(Yii::app()->params['dateFormat'], $data['birthday']);
            }
            if(isset($data['messenger']))
            {
                if($data['messenger'] && $data['messengerLogin'])
                {
                    $data['messenger'] = Yii::app()->params['messengers'][$data['messenger']].': '.$data['messengerLogin'];
                }
                else
                {
                    $data['messenger'] = '';
                }
                unset($data['messengerLogin']);
            }
            if(isset($data['isFilled']))
            {
                $data['isFilled'] = $data['isFilled']?Yii::t('application', 'Да'):Yii::t('application', 'Нет');
            }
            if(isset($data['isVerified']))
            {
                $data['isVerified'] = $data['isVerified']?Yii::t('application', 'Да'):Yii::t('application', 'Нет');
            }
            if (isset($data['last_access']))
            {
                //Additional Export Fields: last_access, answers_count
                $data['last_access'] = date(Yii::app()->params['dateFormat'], $data['last_access']);
            }
            if(isset($data['verificationDate']))
            {
                $data['verificationDate'] = date(Yii::app()->params['dateFormat'], $data['verificationDate']);
            }

            $row = array();
            foreach ($this->fields as $field) {
                if (in_array($field, array('phoneCode', 'messengerLogin'))) {
                    continue;
                }
                if (array_key_exists($field, $data)) {
                    $row[] = $data[$field];
                }
            }

            return $row;
        }

    }
    