<?php
    /* @var $this EventController */
    /* @var $event array */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    
    $cs = Yii::app()->clientScript;
    $currentUser = $this->getUser();
    
    $cs->registerScriptFile('/js/jquery.fancybox.js');
    $cs->registerCssFile('/css/jquery.fancybox.css');
    
    $manageButtons = '';
    if($currentUser && $currentUser->isVerified)
    {
        $manageButtons .= '<input id="download_image_\'+imageId+\'" type="button" value="'.Yii::t('application', 'Скачать').'" class="but_light manage-image btnDownloadImage">';
        $manageButtons .= '<span class="manage-image-separator">&nbsp;</span>';
        $manageButtons .= '<input id="share_image_\'+imageId+\'" type="button" value="'.Yii::t('application', 'Запостить в Facebook').'" class="but_light manage-image btnShareImage">';
        
        $cs->registerScript('download_image', "

            $('body').on('click', '.btnDownloadImage', function(){
                var imageId = $(this).attr('id').substring(15);
                window.location.href = $('#gallery_photo_'+imageId).attr('href');
            });
        ");
        
        $cs->registerScript('share_photo', "
        
            $.getScript('//connect.facebook.net/ru_RU/all.js', function(){

                FB.init({
                    appId: '".Yii::app()->params['facebook']['appId']."',
                    version: 'v2.0'
                });

                $('body').on('click', '.btnShareImage', function(){

                    var imageId = $(this).attr('id').substring(12);

                    FB.login(function(response){

                        var params = {
                            message: '".Yii::t('application', 'БУДУДТАМ - Фотография с мероприятия').' "'.CHtml::encode($event['name']).'"'."',
                            link: '".Yii::app()->params['facebookLink']."'+$('#gallery_photo_'+imageId).attr('href'),
                            actions:[{
                                name: '".Yii::t('application', 'Подробнее')."',
                                link: '".Yii::app()->params['facebookLink'].$this->createUrl('event/share', array('eventId' => $event['eventId']))."'
                            }]
                        };

                        FB.api('me/feed', 'POST', params, function(response2) {
                            if(typeof response2.id != 'undefined')
                            {
                                alertSuccess('".Yii::t('application', 'Фотография опубликована на вашей стене')."');
                            }
                            else
                            {
                                alertError('".Yii::t('application', 'Что-то произошло при публикации фотографии. Попробуйте перезагрузить страницу.')."');
                            }
                        });

                    }, {scope: 'publish_actions'});

                    return false;
                });

            });
        ");
    }
    
    if($event['isMine'])
    {
        if(!empty($manageButtons))
        {
            $manageButtons .= '<span class="manage-image-separator">&nbsp;</span>';
        }
        $manageButtons .= '<input id="delete_image_\'+imageId+\'" type="button" value="'.Yii::t('application', 'Удалить').'" class="but_red manage-image btnDeleteImage">';
        
        $cs->registerScript('delete_image', "

            $('body').on('click', '.btnDeleteImage', function(){
                var imageId = $(this).attr('id').substring(13);
                if (confirm('".Yii::t('application', 'Удалить изображение?')."'))
                {
                    $.ajax({
                        url: '".$this->createUrl('event/deleteImage')."',
                        type: 'GET',
                        data: {'imageId': imageId},
                        dataType: 'json',
                        success: function(data, status, xhr)
                        {
                            if(typeof(data['success']) != 'undefined')
                            {
                                if (data.success)
                                {
                                    alertSuccess('".Yii::t('application', 'Фотография удалена.')."');
                                    $('#gallery_photo_'+imageId).parent().remove();
                                    $.fancybox.close();
                                }
                                else
                                {
                                    alertError(data.message);
                                }
                            }
                            else
                            {
                                alertError('".Yii::t('application', 'Что-то произошло при удалении фотографии. Попробуйте перезагрузить страницу.')."');
                                $.fancybox.close();
                            }
                        },
                        error: function(xhr, status)
                        {
                            alertError('".Yii::t('application', 'Что-то произошло при удалении фотографии. Попробуйте перезагрузить страницу.')."');
                            $.fancybox.close();
                        }
                    });
                }
            });
        ");
        
    }
    
    if(empty($manageButtons))
    {
        $cs->registerScript('fancybox', '
            $(".fancybox").fancybox();
        ');
    }
    else
    {
        $cs->registerScript('fancybox', '
            $(".fancybox").fancybox({
                title: function (fancy) {
                    var imageId = $(fancy.element).attr(\'id\').substring(14);
                    return \''.$manageButtons.'\';
                }
            });
        ');
    }
    
    $allImages = array();
    $photos = array();
    foreach($event['gallery']['albums'] as $album)
    {
        if(count($album['images']))
        {
            $photos[] = array_shift($album['images']);
            $allImages += $album['images'];
        }
    }
    
    while((count($photos) <= 7) && count($allImages))
    {
        $photos[] = array_shift($allImages);
    }
    
?>
<?php if($event['gallery']['countPhotos']): ?>
    <div class="title_l"><?php print Yii::t('application', 'Фото мероприятия'); ?>
        <a href="<?php print $this->createUrl('event/gallery', array('eventId' => $event['eventId'])); ?>" class="read_all">
            <?php print Yii::t('application', 'Смотреть все фотографии'); ?>
        </a>
    </div>
    <div class="line_reg"></div>
    
    <ul class="photo_mir">
        <?php foreach($photos as $item): ?>
        <li>
            <a id="gallery_photo_<?php print $item['imageId']; ?>" class="fancybox" rel="event_<?php print $event['eventId']; ?>_photo" href="<?php print $item['originalImage']; ?>"><img src="<?php print $item['image']; ?>" alt=""/></a>
        </li>
        <?php endforeach; ?>
    </ul>
    <div class="clr"></div>
<?php endif; ?>