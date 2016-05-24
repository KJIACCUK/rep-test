<?php

    class SocketIoHelper
    {
        public static $debug = false;

        private static $_onConnect;
        private static $_onDisconnect;
        private static $_onError;
        private static $_onUserOnline;
        private static $_onUserOffline;
        private static $_onStartTyping;
        private static $_onEndTyping;
        private static $_onMessage;
        private static $_onUpdateCounters;
        
        public static function onConnect($func)
        {
            self::$_onConnect[] = $func;
        }
        
        public static function onDisconnect($func)
        {
            self::$_onDisconnect[] = $func;
        }
        
        public static function onError($func)
        {
            self::$_onError[] = $func;
        }
        
        public static function onUserOnline($func)
        {
            self::$_onUserOnline[] = $func;
        }
        
        public static function onUserOffline($func)
        {
            self::$_onUserOffline[] = $func;
        }
        
        public static function onStartTyping($func)
        {
            self::$_onStartTyping[] = $func;
        }
        
        public static function onEndTyping($func)
        {
            self::$_onEndTyping[] = $func;
        }
        
        public static function onMessage($func)
        {
            self::$_onMessage[] = $func;
        }
        
        public static function onUpdateCounters($func)
        {
            self::$_onUpdateCounters[] = $func;
        }
        
        public static function registerScript()
        {
            /* @var $cs CClientScript */
            $cs = Yii::app()->clientScript;
            $cs->registerScriptFile('/js/jquery.cookie.js');
            $cs->registerScriptFile('/js/socket.io-0.9.16.js');
            //$cs->registerScriptFile(Yii::app()->params['nodeServerUrl'].'socket.io/socket.io.js');
            
            $cs->registerScript('initSocketIO', "
                var socketConnected = false;
                var socket = io.connect('".Yii::app()->params['nodeServerUrl']."', {secure: ".(Yii::app()->params['nodeServerSecure']?'true':'false').", 'query': 'token=' + $.cookie('PHPSESSID') + '&platform=web'});",
            CClientScript::POS_END);
            
            $js = "";
            
            $js .= "socket.on('connect', function() {"."\n";
            $js .= "    socketConnected = true;"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:connect');"."\n";
            }
            if(self::$_onConnect)
            {
                foreach(self::$_onConnect as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
                

            $js .= "socket.on('disconnect', function() {"."\n";
            $js .= "    socketConnected = false;"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:disconnect');"."\n";
            }
            if(self::$_onConnect)
            {
                foreach(self::$_onConnect as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $js .= "socket.on('error', function(reason) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:error, reason - '+reason);"."\n";
            }
            if(self::$_onError)
            {
                foreach(self::$_onError as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $js .= "socket.on('userOnline', function(userId) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:userOnline, userId - '+userId);"."\n";
            }
            if(self::$_onUserOnline)
            {
                foreach(self::$_onUserOnline as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $js .= "socket.on('userOffline', function(userId) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:userOffline, userId - '+userId);"."\n";
            }
            if(self::$_onUserOffline)
            {
                foreach(self::$_onUserOffline as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $js .= "socket.on('startTyping', function(userId) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:startTyping, userId - '+userId);"."\n";
            }
            if(self::$_onStartTyping)
            {
                foreach(self::$_onStartTyping as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $js .= "socket.on('endTyping', function(userId) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:endTyping, userId - '+userId);"."\n";
            }
            if(self::$_onEndTyping)
            {
                foreach(self::$_onEndTyping as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            
            $js .= "socket.on('message', function(data) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:message');"."\n";
                $js .= "    console.log(data);"."\n";
            }
            if(self::$_onMessage)
            {
                foreach(self::$_onMessage as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $js .= "socket.on('updateCounters', function(data) {"."\n";
            if(self::$debug)
            {
                $js .= "    console.log('socket:updateCounters');"."\n";
                $js .= "    console.log(data);"."\n";
            }
            if(self::$_onUpdateCounters)
            {
                foreach(self::$_onUpdateCounters as $func)
                {
                    $js .= "    ".$func."\n";
                }
            }
            $js .= "});"."\n";
            
            $cs->registerScript('initSocketIOHandlers', $js);
        }
    }
    