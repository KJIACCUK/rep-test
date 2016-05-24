<?php
    /* @var $this EventController */
    /* @var $event array */
    /* @var $cs CClientScript */
    /* @var $defaultAlbum EventGalleryAlbum */

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

    $allPhotos = array();
    foreach($event['gallery']['albums'] as $album)
    {
        $allPhotos += $album['images'];
    }

    function sortPhotos($a, $b)
    {
        $a = strtotime($a['dateCreated']);
        $b = strtotime($b['dateCreated']);
        if($a == $b)
        {
            return 0;
        }
        return ($a < $b)?-1:1;
    }
    
    usort($allPhotos, 'sortPhotos');
    
    $showAlbums = count($event['gallery']['albums']) > 1;

    if($event['isMine'])
    {
        $cs->registerScriptFile('/js/jquery.iframe-transport.js');
        $cs->registerScriptFile('/js/jquery.fileupload.js');

        $cs->registerScript('gallery_management', "
            
            $('#btnAddAlbum').click(function(){
                $('#addAlbumDialog').dialog('open');
            });
            
            $('#btnUploadPhoto').click(function(){
                $('#uploadPhotoDialog').dialog('open');
            });

            $('#addAlbumDialog-save').click(function(){
                
                $.ajax({
                    url: '".$this->createUrl('event/addAlbum', array('eventId' => $event['eventId']))."',
                    type: 'POST',
                    data: {'name': $('#EventGalleryAlbum_name').val()},
                    dataType: 'json',
                    success: function(data, status, xhr)
                    {
                        $('#EventGalleryAlbum_name').val('');
                        if(typeof(data['success']) != 'undefined')
                        {
                            if (data.success)
                            {
                                alertSuccess('".Yii::t('application', 'Альбом создан.')."');
                                $('#albumsList').html(data.data).show();
                                $('#albumsListTitle').show();
                                $('#addAlbumDialog').dialog('close');
                            }
                            else
                            {
                                alertError(data.message);
                            }
                        }
                        else
                        {
                            alertError('".Yii::t('application', 'Что-то произошло при добавлении альбома. Попробуйте перезагрузить страницу.')."');
                            $('#addAlbumDialog').dialog('close');
                        }
                    },
                    error: function(xhr, status)
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при добавлении альбома. Попробуйте перезагрузить страницу.')."');
                        $('#addAlbumDialog').dialog('close');
                        $('#EventGalleryAlbum_name').val('');
                    }
                });

                return false;
            });
            
            $('#addAlbumDialog-close').click(function(){
                $('#addAlbumDialog').dialog('close');
                return false;
            });
            
            $('#uploadPhotoDialog-close').click(function(){
                $('#uploadPhotoDialog').dialog('close');
                $('#eventGalleryImageView').css('background-image', 'none')
                return false;
            });
            
            $('#btnUploadImage').click(function(){
                $('#uploadImageInput').click();
                return false;
            });

            $('#uploadImageInput').fileupload({
                url: '".$this->createUrl('event/addImage', array('eventId' => $event['eventId'], 'albumId' => $defaultAlbum->eventGalleryAlbumId))."',
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
<?php $this->widget('application.widgets.EventSubpageTitle', array('title' => Yii::t('application', 'Фотогалерея'), 'eventId' => $event['eventId'], 'eventName' => $event['name'])); ?>

<?php if($event['isMine']): ?>
        <div id="event-manage" class="title_l">
            <div class="event-manage-button">
                <?php print CHtml::button(Yii::t('application', 'Создать альбом'), array('id' => 'btnAddAlbum', 'class' => 'but_light')); ?>
                <?php print CHtml::button(Yii::t('application', 'Загрузить фото'), array('id' => 'btnUploadPhoto', 'class' => 'but_light')); ?>
            </div>
            <div class="clr"></div>
        </div>
<?php endif; ?>

<h3 id="albumsListTitle" style="color: #FFF;<?php print ($showAlbums)?'':'display:none;'; ?>"><?php print Yii::t('application', 'Альбомы'); ?></h3>
<div id="albumsList">
    <?php print $this->renderPartial('_gallery_albums_items', array('eventId' => $event['eventId'], 'albums' => $event['gallery']['albums'])); ?>
</div>
<h3 id="photosListTitle" style="color: #FFF"><?php print Yii::t('application', 'Фото'); ?></h3>
<div id="photosList">
    <?php print $this->renderPartial('_gallery_photo_items', array('event' => $event, 'photos' => $allPhotos)); ?>
</div>

<?php if($event['isMine']): ?>

        <?php
        $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
            'id' => 'addAlbumDialog',
            'scriptFile' => false,
            // additional javascript options for the dialog plugin
            'options' => array(
                'title' => Yii::t('application', 'Создать альбом'),
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

        <?php print CHtml::submitButton(Yii::t('application', 'Создать'), array('id' => 'addAlbumDialog-save', 'class' => 'but_blue')); ?>
        <a href="#" id="addAlbumDialog-close" class="btn-cancel"><?php print Yii::t('application', 'Отмена'); ?></a>
        <div class="clr"></div>

        <?php $this->endWidget('CActiveForm'); ?>


        <?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

        <?php
        $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
            'id' => 'uploadPhotoDialog',
            'scriptFile' => false,
            // additional javascript options for the dialog plugin
            'options' => array(
                'title' => Yii::t('application', 'Добавить фотографии'),
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