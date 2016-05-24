<?php
    /* @var $controller WebController */
    /* @var $this HeaderCounters */
    /* @var $cs CClientScript */
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('header_counters', "
        setInterval(function(){
            socket.emit('getCounters', {});
        }, 120000);
    ");
    
    SocketIoHelper::onUpdateCounters("

        if(data.messages.total != 0)
        {
            $('#mess span').text(data.messages.total).show();
        }
        else
        {
            $('#mess span').text('').hide();
        }
        
        if(data.notifications.total != 0)
        {
            $('#napom span').text(data.notifications.total).show();
        }
        else
        {
            $('#napom span').text('').hide();
        }
    ");
?>
<div class="header_up"<?php print in_array($this->getController()->getId(), array('marketingResearch'))?' style="background-image:url(\'../images/fon_f2.png") no-repeat\'':''?>>
    <a href="<?php print $controller->createUrl('event/index'); ?>" id="logo"></a>
    <a href="<?php print $controller->createUrl('promo/index'); ?>" class="up_soc" id="promo"></a>
    <a href="<?php print $controller->createUrl('chat/index'); ?>" class="up_soc" id="mess">
        <?php if($unreadedMessagesCount): ?>
            <span><?php print $unreadedMessagesCount; ?></span>
        <?php else: ?>    
            <span style="display: none;"></span>
        <?php endif; ?>
    </a>
    <a href="<?php print $controller->createUrl('notification/index'); ?>" class="up_soc" id="napom">
        <?php if($unreadedNotificationsCount): ?>
            <span><?php print $unreadedNotificationsCount; ?></span>
        <?php else: ?>    
            <span style="display: none;"></span>
        <?php endif; ?>
    </a>
</div>