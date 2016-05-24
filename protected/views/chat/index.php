<?php
    /* @var $this ChatController */
    /* @var $cs CClientScript */
    /* @var $friends array */
    /* @var $unreadedMessagesByUserCount UserMessage[] */
   
    $this->setPageTitle(Yii::t('application', 'Чат'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('chat', "
        $('#navi_menu_all a').click(function(){
            var parent = $(this).parent();
            $('#navi_menu_online').removeClass('act');
            if(!parent.hasClass('act'))
            {
                parent.addClass('act');
            }
            
            $('#friendsList li').show();
            return false;
        });
        
        $('#navi_menu_online a').click(function(){
            var parent = $(this).parent();
            $('#navi_menu_all').removeClass('act');
            if(!parent.hasClass('act'))
            {
                parent.addClass('act');
            }
            
            $('#friendsList .ico_online').each(function(){
                if(!$(this).hasClass('act'))
                {
                    $(this).parent().hide();
                }
            });
            
            return false
        });

    ");
    
    SocketIoHelper::onUserOnline("
        var user = $('.user_'+userId+' .ico_online');
        if(user.length)
        {
            if(!$(user).hasClass('act'))
            {
                $(user).addClass('act');
                if($('#navi_menu_online').hasClass('act'))
                {
                    $(user).parent().show();
                }
            }
        }
    ");
    
    SocketIoHelper::onUserOffline("
        var user = $('.user_'+userId+' .ico_online');
        if(user.length)
        {
            if($(user).hasClass('act'))
            {
                $(user).removeClass('act');
                if($('#navi_menu_online').hasClass('act'))
                {
                    $(user).parent().hide();
                }
            }
        }
    ");
    
    SocketIoHelper::onUpdateCounters("
        $('#friendsList li').each(function(){
            var userId = $(this).attr('class').substring(5);
            if(typeof data.messages.list[userId] !== 'undefined')
            {
                $('#friendsList .user_'+userId+' .ico_frend span').text(data.messages.list[userId]).show();
            }
            else
            {
                $('#friendsList .user_'+userId+' .ico_frend span').text('').hide();
            }
        });
    ");
    
?>
<ul class="navi_menu dop_st">
    <li id="navi_menu_all" class="act">
        <a href="#"><?php print Yii::t('application', 'Все'); ?></a> 
    </li>
    <li id="navi_menu_online">
        <a href="#"><?php print Yii::t('application', 'Online'); ?></a>
    </li>
</ul>
<ul id="friendsList" class="list_frend">
    <?php foreach($friends as $user): ?>
        <li class="user_<?php print $user['userId']; ?>">
            <div class="ico_online<?php print $user['isOnline']?' act':''; ?>"></div>
            <span class="ico_frend" style="background-image:url(<?php print $user['image']; ?>);">
                <a href="<?php print $this->createUrl('user/detail', array('userId' => $user['userId'])); ?>"></a>
                <?php if(isset($unreadedMessagesByUserCount[$user['userId']])): ?>
                <span><?php print $unreadedMessagesByUserCount[$user['userId']]; ?></span>
                <?php else: ?>
                <span style="display: none;"></span>
                <?php endif; ?>
            </span>
            <a href="<?php print $this->createUrl('chat/dialog', array('userId' => $user['userId'])); ?>" class="mess_frend"></a>
            <div class="inf_frend">
                <p><?php print CHtml::encode($user['name']); ?></p>
                <p class="frend_age"><?php print UserHelper::getAge($user['birthday']); ?></p>
            </div>
        </li>
    <?php endforeach; ?>
</ul>