<?php
    /* @var $this EventController */
    /* @var $event array */
    /* @var $cs CClientScript */
    
    $currentUser = $this->getUser();
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('comments', "
        
        var offset = 0;
        var limit = ".$this->commentsShortLimit.";
        var allLoaded = false;
        
        function loadComments()
        {
            if(allLoaded)
            {
                return false;
            }
            
            $.ajax({
                url: '".$this->createUrl('event/comments')."',
                type: 'GET',
                data: {'eventId': ".$event['eventId'].", 'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#commentsList').append(data);
                    offset = $('#commentsList li').length;
                    $('#noComments').remove();

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

        $('#commentsList').scroll(function()
        {
            if(this.scrollHeight - $(this).scrollTop()  <= $(this).height())
            {
                loadComments();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadComments();
            }
        });

        $('#btnAddComment').click(function(){
            var content = $('#commentContent').val();
            if(content)
            {
                $.ajax({
                    url: '".$this->createUrl('event/addComment', array('eventId' => $event['eventId']))."',
                    type: 'POST',
                    data: {'eventId': ".$event['eventId'].", 'content': content},
                    dataType: 'json',
                    success: function(data, status, xhr)
                    {
                        if(typeof(data['success']) != 'undefined')
                        {
                            if (data.success)
                            {
                                alertSuccess('".Yii::t('application', 'Комментарий добавлен.')."');
                                offset = 0;
                                allLoaded = false;
                                $('#commentsList').empty();
                                $('#commentContent').val('');
                                loadComments();
                            }
                            else
                            {
                                alertError(data.message);
                            }
                        }
                        else
                        {
                            alertError('".Yii::t('application', 'Что-то произошло при добавлении комментария. Попробуйте перезагрузить страницу.')."');
                        }
                    },
                    error: function(xhr, status)
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при добавлении комментария. Попробуйте перезагрузить страницу.')."');
                    }
                });
            }
        });

    ");
?>
<div class="title_l"><?php print Yii::t('application', 'Свежие комментарии'); ?>
    <?php if($event['commentsCount']): ?>
    <a href="<?php print $this->createUrl('event/comments', array('eventId' => $event['eventId'])); ?>" class="read_all">
        <?php print Yii::t('application', 'Читать все комментарии ({count})', array('{count}' => $event['commentsCount'])); ?>
    </a>
    <?php endif; ?>
</div>
<div class="line_reg" style="margin-bottom:10px;"></div>

<?php if(count($event['comments'])): ?>
<ul id="commentsList" class="mir_comment">
    <?php print $this->renderPartial('_comments_items', array('comments' => $event['comments'])); ?>
</ul>
<?php else: ?>
<ul id="commentsList" class="mir_comment"></ul>
<p id="noComments"><?php print Yii::t('application', 'Комментариев нет'); ?></p>
<?php endif; ?>

<div class="otvet_mes">
    <div class="one_mes">
        <span class="ico_frend" style="background-image:url(<?php print CommonHelper::getImageLink($currentUser->image, '82x80'); ?>);">
            <a href="<?php print $this->createUrl('user/index'); ?>"></a>
        </span>
        <div class="inf_frend">
            <p><?php print CHtml::encode($currentUser->name); ?></p>
            <p class="frend_age"><?php print UserHelper::getAge(date(Yii::app()->params['dateFormat'], $currentUser->birthday)); ?></p>
        </div>
    </div>
    <textarea id="commentContent" rows="5" cols="50"></textarea>
    <button id="btnAddComment" class="but_light add_one"><?php print Yii::t('application', 'Отправить'); ?></button>
</div>