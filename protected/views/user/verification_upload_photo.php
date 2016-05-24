<?php
/* @var $cs CClientScript */
/* @var $this UserController */
/* @var $errors array */

$this->layout = '//layouts/empty';
$title = Yii::t('application', 'Верификация по фото');
$this->setPageTitle($title);
$cs = Yii::app()->clientScript;

$cs->registerScript('photo_upload', "
    $('#btnUpload, #textUpload').click(function(){
        $('#fileUpload').trigger('click');
        return false;
    });

    $('#fileUpload').change(function(){
        $('#textUpload').val($(this).val());
        return true;
    });

");
?>
<div class="osn_b login_b">
    <div class="bl_login verif_p">
        <h1 style="margin-top:70px;"><?php print $title; ?></h1>

        <?php
        print CHtml::beginForm('', 'post', array(
            'id' => 'verification_upload_photo-form',
            'enctype' => 'multipart/form-data'
        ));
        ?>

        <?php if ($errors): ?>
            <div class="errorSummary"><p><?php print Yii::t('application', 'Необходимо исправить следующие ошибки:'); ?></p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php print $error; ?></li>
                    <?php endforeach; ?>
                </ul></div>
        <?php endif; ?>

        <div>
            <div class="skype_l min_tx">
                <p><?php print Yii::t('application', 'Прикрепить фото'); ?></p>
                <div class="inp_tx customtx">
                    <div class="inp_txr"></div>
                    <div class="inp_txl"></div>
                    <input id="textUpload" type="text" disabled="disabled" />
                    <?php print CHtml::fileField('imageFile', null, array('id' => 'fileUpload')); ?>
                </div>
            </div>
            <?php print CHtml::button(Yii::t('application', 'Выбрать файл'), array('id' => 'btnUpload', 'class' => 'but_blue zak_zv', 'style' => 'float: left; position: absolute; margin-left: -20px; margin-top: 50px;')); ?>
            <div style="clear: both;"></div>
        </div>

        <?php print CHtml::submitButton(Yii::t('application', 'Загрузить'), array('class' => 'but_light zak_zv')); ?>
        <a style="color: #ffffff; font-size: 16px; margin-left: 20px;" href="<?php print $this->createUrl('user/verification'); ?>"><?php print Yii::t('application', 'Отменить'); ?></a>
        <?php print CHtml::endForm(); ?>
    </div>
    <div class="footer_b"></div>
</div>