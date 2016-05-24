<?php
    /* @var $this EventController */
    /* @var $events array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Предстоящие мероприятия'));
    $this->layout = '//layouts/main';
    $cs = Yii::app()->clientScript;
    
    $actionId = $this->getAction()->getId();
    
    $url = $this->createUrl('event/'.$actionId);
    
    $cs->registerScript('events', "
        var offset = ".$this->eventsLimit.";
        var limit = ".$this->eventsLimit.";
        var allLoaded = false;
 
        function loadEvents()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$url."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                
                    $('#eventsList').append(data);
                    offset = $('#eventsList li').length;

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
                loadEvents();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadEvents();
            }
        });
    ");
    
    if($actionId == 'comingEvents')
    {
        $title = Yii::t('application', 'Предстоящие мероприятия');
        
        $cs->registerScript('subscription', "
            $('#eventsList').on('click', '.but_budu', function() {
                var self = this;
                var eventId = $(this).attr('id').replace('eventSubscribe_', '');
                var url = '';
                if ($(this).hasClass('act'))
                {
                    url = '".$this->createUrl('event/unsubscribe')."';
                }
                else
                {
                    url = '".$this->createUrl('event/subscribe')."';
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {'eventId': eventId},
                    dataType: 'json',
                    success: function(data, status, xhr)
                    {
                        if(typeof(data['success']) != 'undefined')
                        {
                            if (data.success)
                            {
                                if ($(self).hasClass('act'))
                                {
                                    alertSuccess('".Yii::t('application', 'Вы отказались от участия в мероприятия.')."');
                                    $(self).removeClass('act'); 
                                }
                                else
                                {
                                    alertSuccess('".Yii::t('application', 'Вы подписались на участие в мероприятии.')."');
                                    $(self).addClass('act');
                                }
                            }
                            else
                            {
                                alertError(data.message);
                            }
                        }
                        else
                        {
                            alertError('".Yii::t('application', 'Что-то произошло при обновлении подписки. Попробуйте перезагрузить страницу.')."');
                        }
                    },
                    error: function(xhr, status)
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при обновлении подписки. Попробуйте перезагрузить страницу.')."');
                    }
                });

                return false;
            });

        ");
    }
    else
    {
        $title = Yii::t('application', 'Прошедшие мероприятия');
    }
    
?>
<div class="title_l">
    <div class="profile-edit-title"><?php print $title; ?></div>
    <div class="clr"></div>
</div>
<div class="line_reg"></div>
<?php if(count($events)): ?>
    <ul id="eventsList" class="list_mir">
        <?php print $this->renderPartial('_subscribed_events_items', array('events' => $events)); ?>
    </ul>
<?php else: ?>
    <p><?php print Yii::t('application', 'Мероприятий нет'); ?></p>
<?php endif; ?>
