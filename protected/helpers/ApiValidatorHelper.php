<?php

    class ApiValidatorHelper
    {
        public static function required($fields, $scenario = null)
        {
            $rule = array($fields, 'required');
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::REQUIRED);
            return $rule;
        }
        
        public static function length($fields, $min = null, $max = null, $scenario = null)
        {
            $rule = array($fields, 'length');
            if($min)
            {
                $rule += array('min' => $min);
            }
            if($max)
            {
                $rule += array('max' => $max);
            }
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('tooShort' => ValidationMessageHelper::LENGTH_TOO_SHORT, 'tooLong' => ValidationMessageHelper::LENGTH_TOO_LONG);
            return $rule;
        }
        
        public static function email($fields, $scenario = null)
        {
            $rule = array($fields, 'email', 'allowName' => false);
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::INCORRECT_EMAIL);
            return $rule;
        }
        
        public static function unique($fields, $scenario = null)
        {
            $rule = array($fields, 'unique');
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::NOT_UNIQUE);
            return $rule;
        }
        
        public static function exists($fields, $scenario = null)
        {
            $rule = array($fields, 'exist');
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::NOT_EXISTS);
            return $rule;
        }
        
        public static function in($fields, $range, $scenario = null)
        {
            $rule = array($fields, 'in', 'range' => $range);
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::NOT_IN_RANGE);
            return $rule;
        }
        
        public static function date($fields, $format, $scenario = null)
        {
            $rule = array($fields, 'date', 'format' => $format);
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::INCORRECT_DATE);
            return $rule;
        }
        
        public static function type($fields, $type, $scenario = null)
        {
            $rule = array($fields, 'type', 'type' => $type);
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('message' => ValidationMessageHelper::INCORRECT_TYPE);
            return $rule;
        }
        
        public static function file($fields, $types, $minSize = null, $maxSize = null, $maxFiles = null,  $scenario = null)
        {
            $rule = array($fields, 'file', 'types' => $types);
            if($minSize)
            {
                $rule += array('minSize' => $minSize);
            }
            if($maxSize)
            {
                $rule += array('maxSize' => $maxSize);
            }
            if($maxFiles)
            {
                $rule += array('maxFiles' => $maxFiles);
            }
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            $rule += array('tooSmall' => ValidationMessageHelper::FILE_TOO_SMALL, 'tooLarge' => ValidationMessageHelper::FILE_TOO_LARGE, 'tooMany' => ValidationMessageHelper::FILE_TOO_MANY, 'wrongType' => ValidationMessageHelper::FILE_WRONG_TYPE);
            return $rule;
        }
        
        public static function safe($fields, $scenario = null)
        {
            $rule = array($fields, 'safe');
            if($scenario)
            {
                $rule += array('on' => $scenario);
            }
            return $rule;
        }
        
    }
    