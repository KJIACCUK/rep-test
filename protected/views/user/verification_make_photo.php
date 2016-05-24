<?php
/* @var $cs CClientScript */
/* @var $this UserController */
/* @var $errors array */

$this->layout = '//layouts/empty';
$title = Yii::t('application', 'Верификация по фото');
$this->setPageTitle($title);
$cs = Yii::app()->clientScript;

$cs->registerScriptFile('/js/webcam.js');
$cs->registerScript('photo_upload', "
    webcam.set_swf_url('/js/webcam.swf');
    webcam.set_shutter_sound(true, '/js/shutter.mp3');
    webcam.set_api_url('".$this->createUrl('user/verificationMakePhotoSave')."');
    webcam.set_quality(90);
    webcam.set_hook('onComplete', function(response){
        response = jQuery.parseJSON(response);
        if(typeof response.success !== 'undefined')
        {
            if(response.success)
            {
                window.location.href = '".$this->createUrl('user/index')."';
            }
            else
            {
                alertError(response.message);
            }
        }
        else
        {
            alertError('".Yii::t('application', 'Произошел сбой при отправке Вашего фото. Пожалуйста, повторите попытку.')."');
        }
    });
    webcam.set_hook('onError', function(msg){
        if(msg == 'No camera was detected.')
        {
            alertError('".Yii::t('application', 'У данного устройства нет камеры. Воспользуйтесь другим устройством или выберите загрузку фото.')."');
        }
        else
        {
            alertError('".Yii::t('application', 'Произошел сбой. Пожалуйста, повторите попытку.')."');
        }
    });

    $('#camera').append(webcam.get_html(720, 540));
    
    $('#btnMakePhoto').click(function(){
        webcam.freeze();
        $(this).hide();
        $('#btnResetPhoto').show();
        $('#btnSavePhoto').prop('disabled', false);
        return false;
    });
    
    $('#btnResetPhoto').click(function(){
        webcam.reset();
        $(this).hide();
        $('#btnMakePhoto').show();
        $('#btnSavePhoto').prop('disabled', true);
        return false;
    });
    
    $('#btnSavePhoto').click(function(){
        webcam.upload();
        return false;
    });

");
?>
<div class="osn_b login_b">
    <div class="bl_login verif_p verification_make_photo">
        <h1 style="margin-top:70px;"><?php print $title; ?></h1>

        <?php
        print CHtml::beginForm('', 'post', array(
            'id' => 'verification_make_photo-form',
        ));
        ?>

        <div style="margin-top: 20px;">
            <div id="camera"></div>
            <?php print CHtml::button(Yii::t('application', 'Сделать фото'), array('id' => 'btnMakePhoto', 'class' => 'but_blue')); ?>
            <a id="btnResetPhoto" style="display: none;" href="#"><?php print Yii::t('application', 'Переснять'); ?></a>
        </div>

        <?php print CHtml::button(Yii::t('application', 'Отправить'), array('id' => 'btnSavePhoto', 'class' => 'but_light zak_zv', 'disabled' => 'disabled')); ?>
        <a id="btnBack" href="<?php print $this->createUrl('user/verification'); ?>"><?php print Yii::t('application', 'Отменить'); ?></a>
        <?php print CHtml::endForm(); ?>
    </div>
    <div class="footer_b"></div>
</div>