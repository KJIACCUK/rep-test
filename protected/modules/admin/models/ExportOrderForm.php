<?php

    require Yii::getPathOfAlias('application.vendor').'/autoload.php';
    require Yii::getPathOfAlias('application.vendor').'/os/php-excel/PHPExcel/CachedObjectStorageFactory.php';
    require Yii::getPathOfAlias('application.vendor').'/os/php-excel/PHPExcel/Settings.php';

    class ExportOrderForm extends CFormModel
    {

        public $createDateStart;
        public $createDateEnd;
        public $type;
        public $productId;
        public $fields = array('t.productPurchaseId', 'p.name', 'p.articleCode', 'u.name', 't.pointsCount',
            't.purchaseCode', 't.comment', 't.dateCreated', 'deliveryAddress', 'deliveryEmail', 'deliveryPhone', 'passportPhoto');
        public $offset = 0;
        public $limit = 10000;
        public $filename = 'budutam_orders';
        public $writeHeaders = true;

        public function rules()
        {
            return array(
                array('filename', 'required'),
                array('filename', 'length', 'max' => 100),
                array('type', 'in', 'range' => array(Product::TYPE_WITH_SERTIFICATE, Product::TYPE_WITH_RECEIPT_ADDRESS, Product::TYPE_WITH_RECEIPT_ADDRESS, 'allowEmpty' => true)),
                array('productId', 'length', 'max' => 11),
                array('fields', 'type', 'type' => 'array', 'allowEmpty' => false),
                array('createDateStart, createDateEnd', 'date', 'format' => 'd.M.yyyy', 'allowEmpty' => true),
                array('offset', 'numerical', 'integerOnly' => true, 'max' => 10000),
                array('productId, limit', 'numerical', 'integerOnly' => true),
                array('writeHeaders', 'boolean')
            );
        }

        public function attributeLabels()
        {
            return array(
                'type' => Yii::t('application', 'Тип заказа'),
                'productId' => Yii::t('application', 'Товар'),
                'createDateStart' => Yii::t('application', 'От'),
                'createDateEnd' => Yii::t('application', 'До'),
                'fields' => Yii::t('application', 'Поля'),
                'offset' => Yii::t('application', 'От'),
                'limit' => Yii::t('application', 'До'),
                'filename' => Yii::t('application', 'Имя файла'),
                'writeHeaders' => Yii::t('application', 'Заголовки колонок')
            );
        }

        public function getTypesList()
        {
            $data = array(
                '' => Yii::t('application', 'Все'),
            );
            return $data + ProductHelper::typesToEdit();
        }

        public function getProductsList()
        {
            $data = array(
                '' => Yii::t('application', 'Все'),
            );

            $products = Product::model()->findAll();
            foreach($products as $item)
            {
                $data[$item->productId] = $item->name;
            }
            return $data;
        }

        public function getFieldsList()
        {
            return array(
                't.productPurchaseId' => Yii::t('application', 'ID'),
                'p.productId' => Yii::t('application', 'ID товара'),
                'p.name' => Yii::t('application', 'Название товара'),
                'p.articleCode' => Yii::t('application', 'Код товара'),
                'p.receiptAddress' => Yii::t('application', 'Адрес получения товара'),
                'u.userId' => Yii::t('application', 'ID покупателя'),
                'u.name' => Yii::t('application', 'Имя и Фамилия покупателя'),
                't.pointsCount' => Yii::t('application', 'Количество баллов'),
                't.purchaseCode' => Yii::t('application', 'Код покупки'),
                't.comment' => Yii::t('application', 'Комментарий'),
                't.dateCreated' => Yii::t('application', 'Дата заказа'),
                'deliveryAddress' => Yii::t('application', 'Адрес доставки'),
                'deliveryEmail' => Yii::t('application', 'E-mail получателя'),
                'deliveryPhone' => Yii::t('application', 'Телефон получателя'),
                'passportPhoto' => Yii::t('application', 'Фото паспорта'),
            );
        }

        public function export()
        {
            set_time_limit(0);
            $criteria = new CDbCriteria();
            $criteria->alias = 't';

            $headers = array();
            $fieldsList = $this->getFieldsList();

            foreach($this->fields as $i => $field)
            {
                if(isset($fieldsList[$field]))
                {
                    $headers[] = $fieldsList[$field];
                }
            }
            
            $fields = $this->fields;
            
            if(in_array('p.name', $fields))
            {
                $fields[array_search('p.name', $fields)] = 'p.name AS productName';
            }
            
            if(in_array('u.name', $fields))
            {
                $fields[array_search('u.name', $fields)] = 'u.name AS userName';
            }

            if(in_array('deliveryAddress', $fields))
            {
                $fields[] = 'd.postIndex';
                $fields[] = 'd.city';
                $fields[] = 'd.street';
                $fields[] = 'd.home';
                $fields[] = 'd.corp';
                $fields[] = 'd.apartment';
                unset($fields[array_search('deliveryAddress', $fields)]);
            }

            if(in_array('deliveryEmail', $fields))
            {
                $fields[] = 'd.email';
                unset($fields[array_search('deliveryEmail', $fields)]);
            }

            if(in_array('deliveryPhone', $fields))
            {
                $fields[] = 'd.phone';
                unset($fields[array_search('deliveryPhone', $fields)]);
            }

            if(in_array('passportPhoto', $fields))
            {
                $fields[] = 'vr.photoAttachment';
                unset($fields[array_search('passportPhoto', $fields)]);
            }

            $criteria->select = implode(',', $fields);
            $criteria->join = ' INNER JOIN '.Product::model()->tableName().' p ON (t.productId = p.productId)';
            $criteria->join .= ' INNER JOIN '.User::model()->tableName().' u ON (t.userId = u.userId)';
            $criteria->join .= ' LEFT JOIN '.DeliveryAddress::model()->tableName().' d ON (t.productPurchaseId = d.productPurchaseId)';
            $criteria->join .= ' LEFT JOIN '.UserVerificationRequest::model()->tableName().' vr ON (t.userId = vr.userId)';

            if($this->type)
            {
                $criteria->addColumnCondition(array('p.type' => $this->type));
            }
            
            if($this->productId)
            {
                $criteria->addColumnCondition(array('t.productId' => $this->productId));
            }

            if($this->createDateStart)
            {
                $criteria->addCondition('t.dateCreated >= :createDateStart');
                $criteria->params[':createDateStart'] = strtotime($this->createDateStart.' midnight');
            }

            if($this->createDateEnd)
            {
                $criteria->addCondition('t.dateCreated <= :createDateEnd');
                $criteria->params[':createDateEnd'] = strtotime($this->createDateEnd.' 23:59:59');
            }

            $criteria->order = 't.userId ASC';
            $criteria->offset = $this->offset;
            $criteria->limit = 100;

            $row = 1;

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '512MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

            $phpExcel = new PHPExcel();
            $phpExcel->getProperties()->setCreator(Yii::t('application', 'Будутам'))
                    ->setLastModifiedBy(Yii::t('application', 'Будутам'))
                    ->setTitle(Yii::t('application', 'Экспорт заказов Будутам'));
            
            $phpExcel->getDefaultStyle()
                    ->getNumberFormat()
                    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            $phpExcel->setActiveSheetIndex(0);
            $sheet = $phpExcel->getActiveSheet();
            $sheet->setTitle(Yii::t('application', 'Заказы'));

            if($this->writeHeaders)
            {
                $sheet->fromArray($headers, null, 'A'.$row);
                $row++;
            }

            $schema = Yii::app()->db->schema;
            $builder = $schema->commandBuilder;
            $tableName = ProductPurchase::model()->tableName();
            $isFound = true;
            $repeatsCount = ceil($this->limit / 100);
            $passportPhotos = array();

            while($isFound && ($repeatsCount > 0))
            {
                $command = $builder->createFindCommand($schema->getTable($tableName), $criteria);
                //Yii::log($command->getText(), CLogger::LEVEL_WARNING, 'ExportOrderForm.export()');
                $orders = $command->queryAll();

                if($orders)
                {
                    //Yii::log(print_r($orders, TRUE), CLogger::LEVEL_WARNING, 'ExportOrderForm.export()');
                    //Yii::app()->end();
                    $processedOrders = array();
                    foreach($orders as $i => $item)
                    {
                        if ($i > 0 && (($orders[$i - 1]['dateCreated'] == $item['dateCreated']) && (isset($orders[$i - 1]) && isset($item['userId']) && $orders[$i - 1]['userId'] == $item['userId'])))
                            continue;
                        if ($item['photoAttachment'])
                        {
                            $passportPhotos[$i] = array('name' => $item['userName'], 'photo' => '/home/budutam/public_html'.$item['photoAttachment']);
                        }
                        else
                        {
                            if ($i > 0 && (($orders[$i - 1]['productPurchaseId'] == $item['productPurchaseId']) || ($orders[$i - 1]['purchaseCode'] == $item['purchaseCode'])))
                                continue;
                        }
                        $processedOrders[] = $this->prepareOrderFields($item);
                    }

                    $sheet->fromArray($processedOrders, null, 'A'.$row);
                    $row += count($orders);

                    $criteria->offset += 100;
                    $repeatsCount--;
                }
                else
                {
                    $isFound = false;
                }
            }
            //Yii::log(print_r($passportPhotos, TRUE), CLogger::LEVEL_WARNING, 'ExportOrderForm.export().passportPhotosArray');
            // Create Zip archive
            $zip = new ZipArchive();
            $destination = "/home/budutam/public_html/content/photos_{$this->filename}.zip";
            if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                Yii::log('Error create ZIP in '.$destination, CLogger::LEVEL_ERROR, 'ExportOrderForm.export().createZipFile');
            }

            foreach($passportPhotos as $photo)
            {
                $zip->addFile($photo['photo'], iconv('UTF-8', 'CP866', $photo['name']).'.jpg');
            }
            $zip->close();

            //header('Content-Type: application/vnd.ms-excel');
            //header('Content-Disposition: attachment;filename="'.$this->filename.'.xls"');
            //header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
            $objWriter->setPreCalculateFormulas(false);
            $objWriter->save("/home/budutam/public_html/content/{$this->filename}.xls");

            $result_zip = new ZipArchive();
            $destination = "/home/budutam/public_html/content/{$this->filename}.zip";
            if ($result_zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                Yii::log('Error create ZIP in '.$destination, CLogger::LEVEL_ERROR, 'ExportOrderForm.export().createZipFile');
            }

            $result_zip->addFile("/home/budutam/public_html/content/photos_{$this->filename}.zip","photos_{$this->filename}.zip");
            $result_zip->addFile("/home/budutam/public_html/content/{$this->filename}.xls","{$this->filename}.xls");
            $result_zip->close();

            unlink("/home/budutam/public_html/content/photos_{$this->filename}.zip");
            unlink("/home/budutam/public_html/content/{$this->filename}.xls");

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename='.$this->filename.".zip");
            header('Content-Length: ' . filesize($destination));
            header('Cache-Control: max-age=0');
            readfile($destination);
        }
        
        private function prepareOrderFields($order)
        {
            if(in_array('deliveryAddress', $this->fields))
            {
                $order['deliveryAddress'] = '';
                if($order['postIndex'])
                {
                    $order['deliveryAddress'] .= $order['postIndex'];
                }
                if($order['city'])
                {
                    $order['deliveryAddress'] .= ' '.$order['city'];
                }
                if($order['street'])
                {
                    $order['deliveryAddress'] .= ', '.$order['street'];
                }
                if($order['home'])
                {
                    $order['deliveryAddress'] .= ' '.$order['home'];
                }
                if($order['corp'])
                {
                    $order['deliveryAddress'] .= ' '.$order['corp'];
                }
                if($order['apartment'])
                {
                    $order['deliveryAddress'] .= '-'.$order['apartment'];
                }

                unset($order['postIndex']);
                unset($order['city']);
                unset($order['street']);
                unset($order['home']);
                unset($order['corp']);
                unset($order['apartment']);
            }
            
            if(in_array('deliveryEmail', $this->fields))
            {
                $order['deliveryEmail'] = $order['email'];
                unset($order['email']);
            }
            
            if(in_array('deliveryPhone', $this->fields))
            {
                $order['deliveryPhone'] = $order['phone'];
                unset($order['phone']);
            }

            if (in_array('passportPhoto', $this->fields)) {
                if($order['photoAttachment'])
                    $order['passportPhoto'] = 'https://budutam.by' . $order['photoAttachment'];
                unset($order['photoAttachment']);
            }

            if(isset($order['dateCreated']))
            {
                $order['dateCreated'] = date(Yii::app()->params['dateTimeFormat'], $order['dateCreated']);
            }

            return array_values($order);
        }

    }
    