<?php
require Yii::getPathOfAlias('application.vendor').'/autoload.php';
require Yii::getPathOfAlias('application.vendor').'/os/php-excel/PHPExcel/CachedObjectStorageFactory.php';
require Yii::getPathOfAlias('application.vendor').'/os/php-excel/PHPExcel/Settings.php';

class PromoController extends AdminController
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index', 'import', 'export'),
                'roles' => array('admin', 'moderator'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new PromoCode('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['PromoCode'])) {
            $model->attributes = $_GET['PromoCode'];
        }

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionImport()
    {
        $model = new PromoImport();
        $importStats = array(
            'imported' => 0,
            'error' => 0,
            'exists' => 0,
            'total' => 0
        );
        $doImport = false;

        if (isset($_POST['PromoImport'])) {
            $model->importFile = CUploadedFile::getInstance($model, 'importFile');

            if ($model->validate()) {

                $doImport = true;

                $file = fopen($model->importFile->getTempName(), 'r');

                while (!feof($file)) {
                    $codeRow = fgetcsv($file, 0, ';');
                    if (isset($codeRow[0]) && !empty($codeRow[0])) {
                        $importStats['total'] ++;
                        $code = trim($codeRow[0]);
                        if (preg_match('/^[a-zA-Z0-9]+$/', $code)) {
                            if (!PromoCode::model()->exists('code = :code', array(':code' => trim($codeRow[0])))) {

                                $promoCode = new PromoCode();
                                $promoCode->code = trim($codeRow[0]);
                                $promoCode->status = PromoCode::STATUS_FREE;
                                $promoCode->dateCreated = time();
                                $promoCode->dateActivated = 0;

                                if ($promoCode->save()) {
                                    $importStats['imported'] ++;
                                } else {
                                    $importStats['error'] ++;
                                }
                            } else {
                                $importStats['exists'] ++;
                            }
                        } else {
                            $importStats['error'] ++;
                        }
                    }
                }

                fclose($file);
            }
        }

        $this->render('import', array(
            'model' => $model,
            'doImport' => $doImport,
            'importStats' => $importStats
        ));
    }

    public function actionExport()
    {
        set_time_limit(0);
        $criteria = new CDbCriteria();
        $criteria->order = 'promoCodeId ASC';
        $criteria->offset = 0;
        $criteria->limit = 100;

        ;


        $row = 1;

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize' => '512MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $phpExcel = new PHPExcel();
        $phpExcel->getProperties()->setCreator(Yii::t('application', 'Будутам'))
        ->setLastModifiedBy(Yii::t('application', 'Будутам'))
        ->setTitle(Yii::t('application', 'Экспорт промо-кодов'));

        $phpExcel->getDefaultStyle()
        ->getNumberFormat()
        ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

        $phpExcel->setActiveSheetIndex(0);
        $sheet = $phpExcel->getActiveSheet();
        $sheet->setTitle(Yii::t('application', 'Промо-коды'));

        $sheet->fromArray(array(
            Yii::t('application', 'Код'),
            Yii::t('application', 'Статус'),
            Yii::t('application', 'Активировавший пользователь'),
            Yii::t('application', 'Баллов начислено'),
            Yii::t('application', 'Дата создания'),
            Yii::t('application', 'Дата активации')
        ), null, 'A'.$row);
        $row++;

        while (($promoCodes = PromoCode::model()->findAll($criteria))) {
            /* @var $promoCodes PromoCode[] */
            foreach ($promoCodes as $item) {
                $sheet->fromArray(array(
                    $item->code,
                    PromoHelper::statusToGridValue($item->status, false),
                    PromoHelper::userToGridValue($item->userId),
                    ($item->pointsActivated?$item->pointsActivated:''),
                    date(Yii::app()->params['dateTimeFormat'], $item->dateCreated),
                    ($item->dateActivated?date(Yii::app()->params['dateTimeFormat'], $item->dateActivated):'')
                ), null, 'A'.$row);
                $row++;
            }
            $criteria->offset += 100;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="promo_codes_'.date('YmdHi').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
        $objWriter->setPreCalculateFormulas(false);
        $objWriter->save('php://output');
    }
}