<?php
    /* @var $this EventController */
    /* @var $events array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Мои Мероприятия'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('my_events', "
        var offset = ".$this->myEventsLimit.";
        var limit = ".$this->myEventsLimit.";
        var allLoaded = false;
 
        function loadEvents()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('event/myEvents')."',
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
        
        $('.redact_m').click(function(){
            if($(this).hasClass('status_waiting'))
            {
                alertError('".Yii::t('application', 'Нельзя редактировать мероприятие, пока оно не прошло модерацию.')."');
                return false;
            }
            return true;
        });
        
        $('.del_m').click(function(){
            return confirm('".Yii::t('application', 'Вы действительно хотите удалить мероприятие?')."');
        });
    ");
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('event/index'); ?>"><?php print Yii::t('application', 'Мероприятия'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Мои Мероприятия'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('events/add'); ?>"><?php print Yii::t('application', 'Создать мероприятие'); ?></a>
    </li>
</ul>
<?php if(count($events)): ?>
    <ul id="eventsList" class="list_mir">
        <?php print $this->renderPartial('_my_events_items', array('events' => $events)); ?>
    </ul>
<?php else: ?>
    <p><?php print Yii::t('application', 'Вы не создали еще ни одного мероприятия'); ?></p>
<?php endif; ?>