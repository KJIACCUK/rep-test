<?php

    date_default_timezone_set('Europe/Minsk');
    header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

    // change the following paths if necessary
    $yii = dirname(__FILE__).'/framework/yii.php';
    $config = dirname(__FILE__).'/protected/config/main.php';

    // remove the following lines when in production mode
    //defined('YII_DEBUG') or define('YII_DEBUG', true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 1);
    
    if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && !strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https'))
    {
        $_SERVER['HTTPS'] = 'on';
    }
    
    require_once($yii);
    Yii::createWebApplication($config)->run();
    