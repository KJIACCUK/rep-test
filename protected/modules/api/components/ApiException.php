<?php

    class ApiException extends CHttpException
    {
        public function __construct($status, $message = null, $code = 0)
        {
            parent::__construct($status, $message, $code);
        }
    }
    