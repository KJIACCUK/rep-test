<?php
    /* @var $this EventController */
    /* @var $event Event */
    /* @var $users array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Участники мероприятия'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $onlyFriends = Web::getParam('friends');
    
    $title = $onlyFriends?Yii::t('application', 'Участники (друзья) мероприятия'):Yii::t('application', 'Участники мероприятия');

    $cs->registerScript('subscribers', "
        var offset = ".$this->subscribersLimit.";
        var limit = ".$this->subscribersLimit.";
        var allLoaded = false;
 
        function loadUsers()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('event/subscribers', array('eventId' => $event->eventId))."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#usersList').append(data);
                    offset = $('#usersList li').length;
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
                loadUsers();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadUsers();
            }
        });

    ");
    
    if(!$onlyFriends)
    {
        $cs->registerScript('add_to_friends', "
        
        $('#usersList').on('click', '.add_frend', function() {
            var self = this;
            var userId = $(this).attr('id');
            userId = userId.replace('invite_user_', '');
            $.ajax({
                url: '".$this->createUrl('user/addFriendshipRequest')."',
                type: 'GET',
                data: {'userId': userId},
                dataType: 'json',
                success: function(data, status, xhr)
                {
                    if(typeof(data['success']) != 'undefined')
                    {
                        if (data.success)
                        {
                            alertSuccess('".Yii::t('application', 'Запрос на добавление в друзья отправлен.')."');
                            $(self).replaceWith('<div class=\"add_frend_invited\">".Yii::t('application', 'Отправлено')."</div>');
                        }
                        else
                        {
                            alertError(data.message);
                        }
                    }
                    else
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при добавлении в друзья. Попробуйте перезагрузить страницу.')."');
                    }
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при добавлении в друзья. Попробуйте перезагрузить страницу.')."');
                }
            });

            return true;
        });

    ");
    }
?>
<?php $this->widget('application.widgets.EventSubpageTitle', array('title' => $title, 'eventId' => $event->eventId, 'eventName' => $event->name)); ?>

<ul id="usersList" class="list_frend">
    <?php print $this->renderPartial('_subscribers_items', array('users' => $users)); ?>
</ul>