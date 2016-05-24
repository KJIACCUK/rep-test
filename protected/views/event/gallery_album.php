<?php
    /* @var $this EventController */
    /* @var $event array */
    /* @var $album array */
    /* @var $cs CClientScript */

    $this->setPageTitle(Yii::t('application', 'Фотогалерея'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    $cs->registerCssFile('/css/jquery-ui-1.10.4.css');
    
    $cs->registerScriptFile('/js/jquery.fancybox.js');
    $cs->registerCssFile('/css/jquery.fancybox.css');
    $cs->registerScript('fancybox', '
        $(".fancybox").fancybox();
    ');
    
    if($event['isMine'])
    {
        $cs->registerScriptFile('/js/jquery.iframe-transport.js');
        $cs->registerScriptFile('/js/jquery.fileupload.js');

        $cs->registerScript('gallery_management', "
            
            $('#btnUploadPhoto').click(function(){
                $('#uploadPhotoDialog').dialog('open');
            });
            
            $('#btnRenameAlbum').click(function(){
                $('#EventGalleryAlbum_name').val($('#albumName').text());
                $('#renameAlbumDialog').dialog('open');
            });
            
            $('#renameAlbumDialog-close').click(function(){
                $('#renameAlbumDialog').dialog('close');
                return false;
            });
            
            $('#uploadPhotoDialog-close').click(function(){
                $('#uploadPhotoDialog').dialog('close');
                return false;
            });
            
            $('#renameAlbumDialog-save').click(function(){
                
                $.ajax({
                    url: '".$this->createUrl('event/renameAlbum', array('eventId' => $event['eventId'], 'albumId' => $album['albumId']))."',
                    type: 'POST',
                    data: {'name': $('#EventGalleryAlbum_name').val()},
                    dataType: 'json',
                    success: function(data, status, xhr)
                    {
                        if(typeof(data['success']) != 'undefined')
                        {
                            if (data.success)
                            {
                                alertSuccess('".Yii::t('application', 'Альбом переименован.')."');
                                $('#albumName').text($('#EventGalleryAlbum_name').val());
                                $('#renameAlbumDialog').dialog('close');
                            }
                            else
                            {
                                alertError(data.message);
                            }
                        }
                        else
                        {
                            alertError('".Yii::t('application', 'Что-то произошло при переименовании альбома. Попробуйте перезагрузить страницу.')."');
                            $('#renameAlbumDialog').dialog('close');
                        }
                    },
                    error: function(xhr, status)
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при переименовании альбома. Попробуйте перезагрузить страницу.')."');
                        $('#renameAlbumDialog').dialog('close');
                    }
                });

                return false;
            });

            $('#btnDeleteAlbum').click(function(){
                
                if (confirm('".Yii::t('application', 'Удалить альбом?')."'))
                {
                    window.location.href = '".$this->createUrl('event/deleteAlbum', array('albumId' => $album['albumId']))."';
                }

                return false;
            });

            $('#btnUploadImage').click(function(){
                $('#uploadImageInput').click();
                return false;
            });

            $('#uploadImageInput').fileupload({
                url: '".$this->createUrl('event/addImage', array('eventId' => $event['eventId'], 'albumId' => $album['albumId']))."',
                dataType: 'json',
                acceptFileTypes: /(\.|\/)(jpg|jpe|jpeg|png)$/i,
                maxFileSize: 2 * 1024 * 1024,
                singleFileUploads: false,
                multipart: true,
                start: function (e) {
                    $('#btnUploadImage').hide();
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
                            if(result.status == 'all')
                            {
                                if(result.savedCount == 1)
                                {
                                    alertSuccess('".Yii::t('application', 'Фото добавлено.')."');
                                }
                                else
                                {
                                    alertSuccess('".Yii::t('application', 'Фото добавлены.')."');
                                }
                            }
                            else if(result.status == 'partial')
                            {
                                if(result.savedCount == 0)
                                {
                                    alertError(result.errors[0]);
                                }
                                else
                                {
                                    alertSuccess('".Yii::t('application', 'Фото добавлены частично. Не добавлены фото:')."<br />'+result.errors.join('<br />'));
                                }
                            }
                            else
                            {
                                alertError('".Yii::t('application', 'Ни одно фото не добавлено:')."<br />'+result.errors.join('<br />'));
                            }
                            
                            var lastImage = '';
                            for(var i in result.saved)
                            {
                                lastImage = result.saved[i].previewImage;
                                
                                $('#photosList ul.photo_mir').append( '<li>'+
                                            '<a class=\"fancybox\" rel=\"event_".$event['eventId']."_photo\" href=\"'+result.saved[i].originalImage+'\"><img src=\"'+result.saved[i].image+'\" alt=\"\"/></a>'+
                                            '</li>');
                            }
                            
                            if(lastImage.length)
                            {
                                $('#eventGalleryImageView').css('background-image', 'url('+lastImage+')').css('background-repeat', 'no-repeat');
                            }
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
                    $('#btnUploadImage').show();
                },
            });
            
        ");
    }
    
?>
<div class="title_l top_pad event-subpage">
    <div class="event-subpage-title"><span><?php print Yii::t('application', 'Альбом'); ?></span>
        <span id="albumName"><?php print CHtml::encode($album['name']); ?></span>
    </div>
    
    <a href="<?php print $this->createUrl('event/gallery', array('eventId' => $event['eventId'])); ?>" class="read_all">
        <?php print Yii::t('application', 'Назад в фотогалерею'); ?>
    </a>
    
    <div class="clr"></div>
</div>
<div class="line_reg" style="margin-bottom:10px;">&nbsp;</div>

<?php if($event['isMine']): ?>
        <div id="event-manage" class="title_l">
            <div class="event-manage-button" style="margin-bottom: 0px;">
                <?php if(!$album['isDefault']): ?>
                    <?php print CHtml::button(Yii::t('application', 'Переименовать'), array('id' => 'btnRenameAlbum', 'class' => 'but_light')); ?>
                <?php endif; ?>
                
                <?php print CHtml::button(Yii::t('application', 'Загрузить фото в альбом'), array('id' => 'btnUploadPhoto', 'class' => 'but_light')); ?>
                <?php if(!$album['isDefault']): ?>
                    <?php print CHtml::button(Yii::t('application', 'Удалить альбом'), array('id' => 'btnDeleteAlbum', 'class' => 'but_red')); ?>
                <?php endif; ?>
            </div>
            <div class="clr"></div>
        </div>
<?php endif; ?>

<div id="photosList">
    <?php print $this->renderPartial('_gallery_photo_items', array('event' => $event, 'photos' => $album['images'])); ?>
</div>

<?php if($event['isMine']): ?>

        <?php
        $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
            'id' => 'renameAlbumDialog',
            'scriptFile' => false,
            // additional javascript options for the dialog plugin
            'options' => array(
                'title' => Yii::t('application', 'Переименовать альбом'),
                'autoOpen' => false,
                'modal' => true,
                'resizable' => false,
                'draggable' => false,
                'width' => 500
            ),
        ));
        ?>

        <div class="line_reg" style="margin-top: 0px;"></div>

        <?php
        $albumForm = $this->beginWidget('CActiveForm', array(
            'id' => 'event_gallery_album-form',
            'enableAjaxValidation' => false,
            'focus' => array($albumModel, 'name'),
        ));
        ?>

        <?php /* @var $albumForm CActiveForm */ ?>

        <?php print $albumForm->errorSummary($albumModel); ?>

        <div class="min_tx">
            <p><?php print $albumForm->label($albumModel, 'name'); ?></p>
            <div class="inp_tx customtx">
                <div class="inp_txr"></div>
                <div class="inp_txl"></div>
        <?php print $albumForm->textField($albumModel, 'name'); ?>
            </div>
        </div>
        <div class="clr"></div>

        <div class="mir_text">&nbsp;
        </div>

        <?php print CHtml::submitButton(Yii::t('application', 'Сохранить'), array('id' => 'renameAlbumDialog-save', 'class' => 'but_blue')); ?>
        <a href="#" id="renameAlbumDialog-close" class="btn-cancel"><?php print Yii::t('application', 'Отмена'); ?></a>
        <div class="clr"></div>

        <?php $this->endWidget('CActiveForm'); ?>


        <?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

        <?php
        $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
            'id' => 'uploadPhotoDialog',
            'scriptFile' => false,
            // additional javascript options for the dialog plugin
            'options' => array(
                'title' => Yii::t('application', 'Добавить фотографии в альбом'),
                'autoOpen' => false,
                'modal' => true,
                'resizable' => false,
                'draggable' => false,
                'width' => 530
            ),
        ));
        ?>

        <div class="line_reg" style="margin-top: 0px;"></div>

        <div>
            <div id="eventGalleryImageView"></div>
            <div id="upladImage">
                <a id="btnUploadImage" href="#"><?php print Yii::t('application', 'Загрузить изображения'); ?></a>
                <input id="uploadImageInput" style="display: none;" type="file" name="imageFiles[]" multiple>
                <div id="uploadImageProgressBar" class="meter" style="display: none; margin: auto;">
                    <span style="width: 0%"></span>
                </div>
            </div>
        </div>


        <div class="mir_text">&nbsp;</div>

        <a href="#" id="uploadPhotoDialog-close" class="btn-cancel"><?php print Yii::t('application', 'Закрыть'); ?></a>
        <div class="clr"></div>

        <?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

<?php endif; ?>