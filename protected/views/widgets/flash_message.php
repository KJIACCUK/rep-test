<?php
    /* @var $cs CClientScript */
    /* @var $this WebController */
    $cs = Yii::app()->clientScript;    
    
    $cs->registerScriptFile('/js/notifIt.js');
    $cs->registerCssFile('/css/notifIt.css');

    $cs->registerScript('js_flash_message', "

        window.alertSuccess = function(message){
            notif({
                msg: message,
                type: 'success',
                position: 'center',
                multiline: true
            });
        };

        window.alertError = function(message){
            notif({
                msg: message,
                type: 'error',
                position: 'center',
                multiline: true
            });
        };  
    ");
    
    if(Yii::app()->user->hasFlash('error'))
    {
        $cs->registerScript('flash_message', "
            notif({
                msg: '".Yii::app()->user->getFlash('error')."',
                type: 'error',
                position: 'center',
                multiline: true
            });
        ");
    }
    elseif(Yii::app()->user->hasFlash('success'))
    {
        $cs->registerScript('flash_message', "
            notif({
                msg: '".Yii::app()->user->getFlash('success')."',
                type: 'success',
                position: 'center',
                multiline: true
            });
        ");
    }
    
?>