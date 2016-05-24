<?php

    class Web
    {

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

        public static function jsonSuccess($data = null)
        {
            $response = array(
                'success' => true,
                'message' => ''
            );

            if($data)
            {
                if(is_array($data))
                {
                    $response += $data;
                }
                else
                {
                    $response['data'] = $data;
                }
            }
            
            print CJSON::encode($response);
            Yii::app()->end();
        }

        public static function jsonError($message)
        {
            $response = array(
                'success' => false,
                'message' => $message
            );

            print CJSON::encode($response);
            Yii::app()->end();
        }
        
        public static function flashSuccess($message)
        {
            Yii::app()->user->setFlash('success', $message);
        }
        
        public static function flashError($message)
        {
            Yii::app()->user->setFlash('error', $message);
        }
    }
    