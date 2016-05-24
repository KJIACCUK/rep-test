<?php
    /* @var $this ChatController */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    /* @var $recipient User */
    /* @var $messages UserMessage[] */

    $this->setPageTitle(Yii::t('application', 'Чат'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;

    $cs->registerScriptFile('/js/smiles.config.js');

    $cs->registerScriptFile('/js/emoticons.js');
    $cs->registerCssFile('/css/emoticons.css');
    
    $cs->registerScriptFile('/js/jquery.mCustomScrollbar.concat.min.js');
    $cs->registerCssFile('/css/jquery.mCustomScrollbar.css');

    $currentUser = $this->getUser();

    $cs->registerScript('chat', "
        $.emoticons.define(window.smilesDefinition);

        var offset = ".$this->messagesLimit.";
        var limit = ".$this->messagesLimit.";
        var allLoaded = false;
        
        function htmlEncode(value){
          return $('<div/>').text(value).html();
        }
        
        function emoteMessages()
        {
            $('#messagesList .messageText:not(.emoted)').each(function(){
                var message = $(this).html();
                $(this).html($.emoticons.replace(message)).addClass('emoted');
            });
        }
 
        function loadMessages()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('chat/dialog', array('userId' => $recipient->userId))."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#messagesList .mCSB_container').prepend(data);
                    emoteMessages();
                    offset = $('#messagesList li').length;
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
        
        $('#messagesList').mCustomScrollbar({
            setTop:'-9999999%',
            scrollInertia: 150,
            mouseWheel:{preventDefault: true},
            callbacks: {
                onTotalScrollBack: function(){
                    loadMessages();
                }
            }
        });
        
        function scrollChat()
        {
            $('#messagesList').mCustomScrollbar('scrollTo', 'last');
        }
        
        emoteMessages();

        var typing = false;
        var lastTypingTime;

        function updateTyping(forceStop) {
            forceStop = forceStop || false;
            if (socketConnected) {
            
                if(forceStop)
                {
                    typing = false;
                    return;
                }

                if (!typing) {
                    typing = true;
                    socket.emit('startTyping', ".$recipient->userId.");
                }
                lastTypingTime = (new Date()).getTime();

                setTimeout(function() {
                    var typingTimer = (new Date()).getTime();
                    var timeDiff = typingTimer - lastTypingTime;
                    if (timeDiff >= 3000 && typing) {
                        socket.emit('endTyping', ".$recipient->userId.");
                        typing = false;
                    }
                }, 3000);
            }
        }
        
        $('#chatTextarea').on('keypress', function(event) {
            var currentVal = $(this).val();
            if(event.keyCode == 13)
            {
                if(event.altKey || event.ctrlKey || event.shiftKey || event.metaKey)
                {
                    $(this).val(currentVal+\"\\n\");
                    return true;
                }
                else
                {
                    $('#chatSendButton').trigger('click');
                    return false;
                }
            }
            return true;
        });

        $('#chatTextarea').on('input', function() {
            updateTyping();
            if($(this).val().length <= 2000)
            {
                return true;
            }
            return false;
        });
        
        $('#chatSendButton').on('click', function() {
            var message = $('#chatTextarea').val();
            if(message.length == 0 || message.length > 2000)
            {
                return false;
            }
            
            updateTyping(true);
            socket.emit('message', {userId: ".$recipient->userId.", message: message});
            $('#chatTextarea').val('');
            return true;
        });

    ");

    SocketIoHelper::onStartTyping("
        $('#chatTypingMessage span').fadeIn(150);
    ");

    SocketIoHelper::onEndTyping("
        $('#chatTypingMessage span').fadeOut(150);
    ");

    SocketIoHelper::onMessage("
        var messageHtml;
        var d = new Date(data.dateCreated);
        var month = d.getMonth() + 1;
        if(month < 10)
        {
            month = '0'+String(month);
        }
        var day = d.getDate();
        if(day < 10)
        {
            day = '0'+String(day);
        }
        var dateCreated = day+'.'+month+'.'+d.getFullYear()+' '+d.getHours()+':'+d.getMinutes();
        if(data.userId == ".$recipient->userId.")
        {
            messageHtml = '<li>'+
                '<div class=\"mini_ico1\" style=\"background-image:url(".CommonHelper::getImageLink($recipient->image, '65x65').")\">'+
                    '<a href=\"".$this->createUrl('user/detail', array('userId' => $recipient->userId))."\"></a>'+
                '</div>'+
                '<div class=\"text_ico1\">'+
                    '<div class=\"ico_m\"></div>'+
                    '<p class=\"messageText\">'+htmlEncode(data.message)+'</p>'+
                    '<div class=\"date\">'+dateCreated+'</div>'+
                '</div>'+
            '</li>';
        }
        else if(data.userId == ".$currentUser->userId.")
        {
            messageHtml = '<li class=\"otvet_m\">'+
                '<div class=\"mini_ico1\" style=\"background-image:url(".CommonHelper::getImageLink($currentUser->image, '65x65').")\">'+
                    '<a href=\"".$this->createUrl('user/index')."\"></a>'+
                '</div>'+
                '<div class=\"text_ico1\">'+
                    '<p class=\"messageText\">'+htmlEncode(data.message)+'</p>'+
                    '<div class=\"date\">'+dateCreated+'</div>'+
                '</div>'+
            '</li>';
        }
        else
        {
            return;
        }
        $('#messagesList .mCSB_container').append(messageHtml);
        emoteMessages();
        scrollChat();
        if(data.userId == ".$recipient->userId.")
        {
            setTimeout(function(){
                $('#mess span').text('').hide();
            }, 1000);
            
            socket.emit('readMessage', {userId: ".$recipient->userId.", messageIds: [data.userMessageId]});
        }
    ");


    if($currentUser->isVerified)
    {
        $cs->registerScriptFile('/js/jquery.emojiarea.js');
        $cs->registerCssFile('/css/jquery.emojiarea.css');
        
        $cs->registerScript('smiles_editor', "
        
            $.emojiarea.path = '/images/smiles';
            $.emojiarea.icons = {};
            
            for(var id in window.smilesDefinition)
            {
                var emote = window.smilesDefinition[id];
                $.emojiarea.icons[emote.title] = id+'.png';
            }
            
            $('#chatTextarea').emojiarea({button: '#smileBtn'});
            
            $('.emoji-wysiwyg-editor').on('input', function() {
                updateTyping();
                if($(this).val().length <= 2000)
                {
                    return true;
                }
                return false;
            });
            
            $('.emoji-wysiwyg-editor').on('keypress', function(event) {
                var currentVal = $('#chatTextarea').val();
                if(event.keyCode == 13)
                {
                    if(event.altKey || event.ctrlKey || event.shiftKey || event.metaKey)
                    {
                        return true;
                    }
                    else
                    {
                        $(this).html('');
                        $('#chatSendButton').trigger('click');
                        return false;
                    }
                }
                return true;
            });
            
            $('#chatSendButton').on('click', function() {
                $('.emoji-wysiwyg-editor').html('');
                return true;
            });
        ");
    }
?>
<div class="one_mes">
    <a id="chatBackBtn" href="<?php print $this->createUrl('chat/index'); ?>">&nbsp;</a>
    <span class="ico_frend" style="background-image:url(<?php print CommonHelper::getImageLink($recipient->image, '80x82'); ?>);">
        <a href="<?php print $this->createUrl('user/detail', array('userId' => $recipient->userId)); ?>"></a>
    </span>
    <div class="inf_frend">
        <p><?php print CHtml::encode($recipient->name); ?></p>
        <p class="frend_age"><?php print UserHelper::getAge(date(Yii::app()->params['dateFormat'], $recipient->birthday)); ?></p>
    </div>
</div>
<ul id="messagesList" class="mes_list" style="overflow-y: auto; height:400px;">
<?php print $this->renderPartial('_dialog_items', array('messages' => $messages, 'recipient' => $recipient)); ?>
</ul>
<div id="chatTypingMessage"><span style="display: none;"><?php print Yii::t('application', 'Пишет сообщение...'); ?></span></div>
<div class="otvet_mes">
    <div>
        <?php if($currentUser->isVerified): ?>
        <a id="smileBtn" href="javascript:void(0)"><img src="/images/smile.png" /></a>
        <?php endif; ?>
        <textarea id="chatTextarea" rows="5" cols="50"></textarea>
    </div>
    <button id="chatSendButton" class="but_light add_one" type="button"><?php print Yii::t('application', 'Отправить'); ?></button>
</div>