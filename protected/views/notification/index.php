<?php
    /* @var $this NotificationController */
    /* @var $cs CClientScript */
    /* @var $notifications UserNotification[] */
   
    $this->setPageTitle(Yii::t('application', 'Уведомления'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('notifications', "
        var offset = ".$this->notificationsLimit.";
        var limit = ".$this->notificationsLimit.";
        var allLoaded = false;
 
        function loadNotifications()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('notification/index')."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#notificationsList').append(data);
                    offset = $('#notificationsList li').length;
                    socket.emit('getCounters', {});
                    if(data.length == 0)
                    {
                        allLoaded = true;
                    }
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                }
            });
        }

        $(window).scroll(function()
        {
            if (document.body.scrollHeight - $(this).scrollTop()  <= $(this).height())
            {
                loadNotifications();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadNotifications();
            }
        });
        
        socket.emit('getCounters', {});

    ");
    
?>
<div class="opros">
    <div class="title_l top_pad">
        <?php print Yii::t('application', 'Уведомления'); ?>
    </div>
    <ul id="notificationsList" class="list_opros">
        <?php print $this->renderPartial('_notification_items', array('notifications' => $notifications)); ?>
    </ul>
    <div class="clr"></div>
</div>