<?php

    class CommonHelper
    {

        public static function md5($string)
        {
            return md5($string.Yii::app()->params['salt']);
        }

        public static function randomString($symbolsCount = 8)
        {
            $characters = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $numbers = "0123456789";
            $upperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $lastCharNum = strlen($characters) - 1;
            $password = "";

            $password .= $numbers[mt_rand(0, strlen($numbers) - 1)];
            $password .= $upperCase[mt_rand(0, strlen($upperCase) - 1)];

            $symbolsCount -= 2;

            for($i = 0; $i < $symbolsCount; $i++)
            {
                $password .= $characters[mt_rand(0, $lastCharNum)];
            }

            return str_shuffle($password);
        }

        public static function generateImageName($id)
        {
            return $id.'_'.time();
        }

        public static function parseSchema($schema)
        {
            $schema = explode(',', $schema);
            return array_map('trim', $schema);
        }

        public static function getImageLink($path, $image)
        {
            $image = explode('x', $image);
            return ImageHelper::getThumbLink($path, $image[0], $image[1]);
        }

        public static function getRange($start, $end, $step = 1)
        {
            $data = array();
            for($i = $start; $i <= $end; $i = $i + $step)
            {
                $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                $data[$val] = $val;
            }

            return $data;
        }
        
        public static function yesnoToGridList()
        {
            return array(
                array('id' => 1, 'title' => Yii::t('application', 'Да')),
                array('id' => 0, 'title' => Yii::t('application', 'Нет')),
            );
        }
        
        public static function yesnoToList()
        {
            return array(
                '' => '',
                1 => Yii::t('application', 'Да'),
                0 => Yii::t('application', 'Нет')
            );
        }
        
        public static function yesnoToGridValue($value)
        {
            if($value)
            {
                return TbHtml::labelTb(Yii::t('application', 'Да'), array('color' => TbHtml::LABEL_COLOR_SUCCESS));
            }
            return TbHtml::labelTb(Yii::t('application', 'Нет'), array('color' => TbHtml::LABEL_COLOR_IMPORTANT));
        }

        public static function decodeParams($params)
        {
            $params_object = CJSON::decode($params, true);
            $output = "";
            foreach ($params_object as $key => $value)
            {
                $output .= "$key: $value <br>";
            }
            return $output;
        }

        public static function encodeLogin($source) {
            $key = 'E!5FgS9%';
            $s = "";
            //$source = iconv('UTF-8', 'CP866', $source);
            // Открывает модуль
            $td = mcrypt_module_open('des', '', 'ecb', '');
            $key = substr($key, 0, mcrypt_enc_get_key_size($td));
            $iv_size = mcrypt_enc_get_iv_size($td);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

            // Инициализирует дескриптор шифрования и шифруем
            if (mcrypt_generic_init($td, $key, $iv) != -1) {
                $s = mcrypt_generic($td, $source);
                mcrypt_generic_deinit($td);
                mcrypt_module_close($td);
            }
            return $s;
        }
    }
    