<?php

    class Api
    {
        const CODE_OK = 200;
        const CODE_BAD_REQUEST = 400;
        const CODE_UNAUTHORIZED = 401;
        const CODE_FORBIDDEN = 403;
        const CODE_NOT_FOUND = 404;
        const CODE_METHOD_NOT_ALLOWED = 405;
        const CODE_INTERNAL_SERVER_ERROR = 500;
        const CODE_NOT_IMPLEMENTED = 501;
        const CODE_VALIDATION_ERROR = 1000;
        const CODE_TOO_MUCH_PURCHASES = 1001;

        public static function getParams($names)
        {
            $params = array();
            foreach($names as $name)
            {
                $params[$name] = Yii::app()->request->getParam($name);
            }
            return $params;
        }
        
        public static function getParam($name, $default = null)
        {
            return Yii::app()->request->getParam($name, $default);
        }
        
        public static function getToken()
        {
            return Yii::app()->request->getParam('token');
        }

        public static function respondSuccess($data = null)
        {
            $response = array();
            $response['data'] = $data?$data:array('success' => 1);
            self::send($response);
        }
        
        public static function respondError($code, $message, $data = null)
        {
            $response = array();
            $response['error'] = array(
                'code' => $code,
                'message' => $message
            );
            
            if($data)
            {
                $response['error']['data'] = $data;
            }
            
            self::send($response);
        }
        
        public static function respondValidationError(CModel $model)
        {
            self::respondError(self::CODE_VALIDATION_ERROR, Yii::t('application', 'Validation error.'), $model->getErrors());
        }
        
        private static function send($data)
        {
            header('Content-Type: application/json; charset=utf-8');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: x-requested-with, content-type');
            print CJSON::encode($data);
            Yii::app()->end();
        }
    }
    