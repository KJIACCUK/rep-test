<?php
    /* @var $this EventController */
    /* @var $event array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Мероприятие'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    $cs->registerScriptFile('/js/cropper.min.js');
    $cs->registerCssFile('/css/jquery-ui-1.10.4.css');
    $cs->registerCssFile('/css/cropper.min.css');
    
    if($event['isMine'])
    {
        $cs->registerScriptFile('/js/jquery.iframe-transport.js');
        $cs->registerScriptFile('/js/jquery.fileupload.js');
        
        $cs->registerScript('event_owner_management', "
            
            $('.but_budu').click(function(){
                return false;
            });

            $('#btnInvite').click(function(){
                window.location.href = '".$this->createUrl('event/invites', array('eventId' => $event['eventId']))."';
            });
            
            $('#btnGallery').click(function(){
                window.location.href = '".$this->createUrl('event/gallery', array('eventId' => $event['eventId']))."';
            });

            $('#btnDelete').click(function(){
                if(confirm('".Yii::t('application', 'Вы действительно хотите удалить мероприятие?')."'))
                {
                    window.location.href = '".$this->createUrl('event/delete', array('eventId' => $event['eventId']))."';
                }
            });

            $('#btnUploadImageOpenDialog').click(function(){
                $('#btnUploadImage').remove();
                $('#uploadImageControls').hide();
                if(isCropperCreated)
                {
                    $('#imagePreview > img').cropper('destroy');
                    isCropperCreated = false;
                }
                $('#imagePreview > img').attr('src', '').hide();
                $('#uploadImageDialog').dialog('open');
                return false;
            });
            
            $('#btnSelectImage').click(function(){
                $('#uploadImageInput').click();
                return false;
            });
            
            var isCropperCreated = false;
            
            $('body').on('click', '#btnRotateLeft', function(){
                $('#imagePreview > img').cropper('rotate', -90);
                return false;
            });
            
            $('body').on('click', '#btnRotateRight', function(){
                $('#imagePreview > img').cropper('rotate', 90);
                return false;
            });

            $('#uploadImageInput').fileupload({
                url: '".$this->createUrl('event/saveImage', array('eventId' => $event['eventId']))."',
                dataType: 'json',
                acceptFileTypes: /(\.|\/)(jpg|jpe|jpeg|png)$/i,
                maxFileSize: 2 * 1024 * 1024,
                add: function (e, data) {
                    if (data.files && data.files[0]){
                        var selectedImage = data.files[0];
                        if(selectedImage.type == 'image/jpeg' || selectedImage.type == 'image/png' )
                        {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $('#imagePreview > img').attr('src', e.target.result).show();
                                $('#uploadImageControls').show();
                                
                                if(isCropperCreated)
                                {
                                    $('#imagePreview > img').cropper('destroy');
                                    $('#btnUploadImage').remove();
                                }
                                $('#imagePreview > img').cropper({
                                    aspectRatio: 740 / 555,
                                    minWidth: 740,
                                    minHeight: 555,
                                    zoomable: false,
                                    done: function(cropperData) {
                                        data.formData = {
                                            'cropper[x]': cropperData.x,
                                            'cropper[y]': cropperData.y,
                                            'cropper[width]': cropperData.width,
                                            'cropper[height]': cropperData.height,
                                            'cropper[rotate]': cropperData.rotate,
                                        };
                                    }
                                });
                                isCropperCreated = true;
                                
                                $('<button id=\"btnUploadImage\" class=\"but_blue\" type=\"button\">".Yii::t('application', 'Сохранить')."</button>')
                                    .appendTo('#uploadImageButtons')
                                    .click(function(){
                                        data.submit();
                                        return false;
                                    });
                            }
                            reader.readAsDataURL(data.files[0]);
                        }
                        else
                        {
                            alertError('".Yii::t('application', 'Выбранный файл не является изображением. Разрешены файлы с расширением png, jpg, jpe, jpeg')."');
                        }
                    }
                },
                start: function (e) {
                    $('#uploadImageControls').hide();
                    $('#uploadImageButtons').hide();
                    $('#uploadImageProgressBar span').css('width', '0%');
                    $('#uploadImageProgressBar').show();
                },
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#uploadImageProgressBar span').css('width', progress + '%');
                },
                done: function (e, data) {

                    result = data.result;

                    if(typeof(result['success']) != 'undefined')
                    {
                        if(result.success)
                        {
                            alertSuccess('".Yii::t('application', 'Изображение обновлено.')."');
                            $('#eventImageView').attr('src', result.image);
                        }
                        else
                        {
                            alertError(result.message);
                        }
                    }
                    else
                    {
                        alertError('".Yii::t('application', 'Во время загрузки произошла ошибка. Попробуйте еще раз.')."');
                    }
                },
                fail: function (e, data) {
                    alertError('".Yii::t('application', 'Во время загрузки произошла ошибка. Попробуйте еще раз.')."');
                },
                always: function (e, data) {
                    $('#uploadImageProgressBar').hide();
                    $('#uploadImageControls').hide();
                    $('#uploadImageButtons').show();
                    $('#imagePreview > img').cropper('destroy');
                    isCropperCreated = false;
                    $('#imagePreview > img').attr('src', '').hide();
                    $('#uploadImageDialog').dialog('close');
                }
            });
            
        ");
    }
    else
    {
        $cs->registerScript('subscribe', "
            $('.but_budu').click(function(){
                
                var self = this;
                var eventId = ".$event['eventId'].";
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
    
    if($event['isPublic'])
    {
        $cs->registerScript('fb_share', "
            $.getScript('//connect.facebook.net/ru_RU/all.js', function(){

                FB.init({
                    appId: '".Yii::app()->params['facebook']['appId']."',
                    version: 'v2.0'
                });

                $('#fbShare').click(function(){

                    FB.login(function(){

                        FB.ui({method: 'share',
                            href: '".Yii::app()->params['facebookLink'].$this->createUrl('event/share', array('eventId' => $event['eventId']))."'
                        });

                    });

                    return false;
                });

            });
        ");
    }

    if($event['status'] == Event::STATUS_WAITING)
    {
        $cs->registerScript('block_edit', "
            $('#btnEdit').click(function(){
                alertError('".Yii::t('application', 'Нельзя редактировать мероприятие, пока оно не прошло модерацию.')."');
                return false;
            });

        ");
    }
    else
    {
        $cs->registerScript('block_edit', "
            $('#btnEdit').click(function(){
                window.location.href = '".$this->createUrl('event/edit', array('eventId' => $event['eventId']))."';
            });
        ");
    }
    
?>
<?php if($event['isMine']): ?>
<div id="event-manage" class="title_l">
    <div class="event-manage-button">
        <?php print CHtml::button(Yii::t('application', 'Редактировать'), array('id' => 'btnEdit', 'class' => 'but_light')); ?>
        <?php print CHtml::button(Yii::t('application', 'Пригласить'), array('id' => 'btnInvite', 'class' => 'but_light')); ?>
        <?php print CHtml::button(Yii::t('application', 'Фотогалерея'), array('id' => 'btnGallery', 'class' => 'but_light')); ?>
        <?php print CHtml::button(Yii::t('application', 'Удалить'), array('id' => 'btnDelete', 'class' => 'but_red')); ?>
        
    </div>
    <div class="clr"></div>
</div>
<?php endif; ?>
<div class="mirop">
    <img id="eventImageView" src="<?php print $event['image']; ?>" alt=""/>
    <?php if($event['isMine']): ?>
    <div id="upladImage">
        <a id="btnUploadImageOpenDialog" href="#"><?php print Yii::t('application', 'Загрузить изображение'); ?></a>
    </div>
    <?php endif; ?>
    
    <div class="kratk_inf">
        <div class="mest_mir">
            <p class="lf_f"><?php print CHtml::encode($event['publisherName']); ?></p>
            <p class="rg_f"><?php print CHtml::encode($event['city'].($event['street']?', '.$event['street']:'').($event['houseNumber']?', '.$event['houseNumber']:'')); ?></p>
        </div>
        <div class="name_mir">
            <?php if($event['isPublic']): ?>
            <a id="fbShare" class="faceb_p" href="#"><?php print Yii::t('application', 'Поделиться'); ?></a>
            <?php endif; ?>
            <p><?php print CHtml::encode($event['name']); ?></p>
        </div>
        <div class="date_m">
            <div class="lf_f"><?php print $event['dateStart'].', '.$event['timeStart'].($event['timeEnd']?' - '.$event['timeEnd']:''); ?></div>
            <div class="rg_f"><?php print Yii::t('application', 'Категория'); ?>: <span><?php print CHtml::encode($event['category']); ?></span></div>
        </div>
        <div class="frend_m">
            <a class="lf_f" href="<?php print $this->createUrl('event/subscribers', array('eventId' => $event['eventId'])); ?>"><?php print Yii::t('application', 'Подписано: {count} чел.', array('{count}' => $event['subscribersCount'])); ?></a>
            <a class="rg_f" href="<?php print $this->createUrl('event/subscribers', array('eventId' => $event['eventId'], 'friends' => 1)); ?>"><?php print Yii::t('application', 'Друзья ({count})', array('{count}' => $event['subscribersFriendsCount'])); ?></a>
        </div>
    </div>

    <div class="line_reg"></div>
    
    <p>
        <?php print CHtml::encode($event['description']); ?>
        <?php if($event['isRelax']): ?>
        <br /><?php print Yii::t('application', 'Источник - портал'); ?> <a href="http://www.relax.by" target="_blank">relax.by</a>
        <?php endif; ?>
    </p>
    <div class="nav_bl">
        <?php if($event['isSubscribe']): ?>
        <a href="#" class="but_budu act"></a>
        <?php else: ?>
        <a href="#" class="but_budu"></a>
        <?php endif; ?>
        <?php if($event['productId']): ?>
        <button onclick="window.location.href='<?php print $this->createUrl('store/detail', array('productId' => $event['productId'])); ?>'" class="but_blue" type="button"><?php print Yii::t('application', 'Приобрести билет в бонусном магазине'); ?></button>
        <?php endif; ?>
        <div class="clr"></div>
    </div>
    
    <?php print $this->renderPartial('_gallery_detail_block', array('event' => $event)); ?>
    <?php print $this->renderPartial('_comments_detail_block', array('event' => $event)); ?>
            
</div>

<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'uploadImageDialog',
        'scriptFile' => false,
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => Yii::t('application', 'Загрузить изображение'),
            'autoOpen' => false,
            'modal' => true,
            'resizable' => false,
            'draggable' => false,
            'width' => 760,
            'height' => 680
        ),
    ));
    ?>

<div id="uploadImageControls" style="display: none;">
    <a id="btnRotateLeft" src="#"><img src="/images/rotate-left.png"></a>
    <a id="btnRotateRight" src="#"><img src="/images/rotate-right.png"></a>
    <div style="clear: both;"></div>
</div>
<div id="imagePreview">
    <img style="display: none;" />
</div>
<div id="uploadImageButtons">
    <button id="btnSelectImage" class="but_blue" type="button"><?php print Yii::t('application', 'Выбрать изображение'); ?></button>
</div>
<div id="uploadImageLoading">
    <input id="uploadImageInput" style="display: none;" type="file" name="Event[imageFile]">
    <div id="uploadImageProgressBar" class="meter" style="display: none; margin: auto;">
        <span style="width: 0%"></span>
    </div>
</div>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>