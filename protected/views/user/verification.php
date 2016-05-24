<?php
    /* @var $cs CClientScript */
    /* @var $this UserController */    
    
    $this->layout = '//layouts/empty';
    $this->setPageTitle(Yii::t('application', 'Верификация аккаунта'));
    $cs = Yii::app()->clientScript;
    
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');

    $cs->registerScript('verification_terms_popup', "
        $('.verification_terms').click(function(){
            $('#verificationTermsDialog').dialog('open');
            return false;
        });
    ");
    
    $cs->registerScript('verification_example_popup', "
        $('.verification_example_btn').click(function(){
            $('#verificationExampleDialog').dialog('open');
            return false;
        });
        
        $('#verificationExampleCloseBtn').click(function(){
            $('#verificationExampleDialog').dialog('close');
        });
        
        $('.make_photo_btn').click(function(){
            if($('#isSmoking').prop('checked') === false) {
                return false;
            }
            $.post('".$this->createUrl('user/verificationSetFavoriteBrand')."', {favoriteCigaretteBrand: $('#favoriteCigaretteBrand').val()}, function(){
                ga('send', 'event', 'Web', 'Button_Make_photo_Click', 'Verification');
                window.location.href='".$this->createUrl('user/verificationMakePhoto')."';
            });
            return false;
        });
        
        $('.upload_photo_btn').click(function(){
            if($('#isSmoking').prop('checked') === false) {
                return false;
            }
            $.post('".$this->createUrl('user/verificationSetFavoriteBrand')."', {favoriteCigaretteBrand: $('#favoriteCigaretteBrand').val()}, function(){
                ga('send', 'event', 'Web', 'Button_Load_photo_Click', 'Verification');
                window.location.href='".$this->createUrl('user/verificationUploadPhoto')."';
            });
            return false;
        });
        
        $('#isSmoking').change(function(){
            if($(this).prop('checked') === true) {
                $('.make_photo_btn').prop('disabled', false);
                $('.upload_photo_btn').prop('disabled', false);
            } else {
                $('.make_photo_btn').prop('disabled', true);
                $('.upload_photo_btn').prop('disabled', true);
            }
            return true;
        });
    ");
        
    $cs->registerCss('dialogs', "
        .ui-dialog {top: 50px!important;}
    ");
?>
<div class="osn_b login_b">
    <div class="bl_login verification">
        <h1><?php print Yii::t('application', 'Верификация аккаунта'); ?></h1>
        <h2><?php print Yii::t('application', 'Верификация по фото'); ?></h2>
        <a href="#" class="verification_terms"><?php print Yii::t('application', 'Условия конфедициальности'); ?></a>
        <hr />
        <p class="instruction">
            <?php print Yii::t('application', 'Для этого'); ?>:<br /> 
            1. <?php print Yii::t('application', 'Заполни данные о себе'); ?><br /> 
            2. <?php print Yii::t('application', 'Отправь своё фото с паспортом,'); ?><br /> 
            <?php print Yii::t('application', 'как в примере'); ?>
        </p>
        <button class="but_light verification_example_btn" type="submit"><?php print Yii::t('application', 'Посмотреть пример'); ?></button>
        <button class="but_light make_photo_btn" type="submit" disabled="disabled"><?php print Yii::t('application', 'Сделать фото сейчас'); ?></button>
        <button class="but_light upload_photo_btn" type="submit" disabled="disabled"><?php print Yii::t('application', 'Загрузить фото'); ?></button>
        
        <div class="love_l" style="line-height: inherit; float: none; margin-bottom: 20px;">
            <label for="isSmoking">
                <?php echo CHtml::checkBox('isSmoking', false, array('id' => 'isSmoking', 'class' => 'checkbox-field')); ?>
                <?php echo Yii::t('application', 'Вы курите?'); ?>
            </label>
            <?php print CHtml::dropDownList('favoriteCigaretteBrand', null, array_combine(Yii::app()->params['cigaretteBrands'], Yii::app()->params['cigaretteBrands']), array('class' => 'select-field black_t verification-favoriteCigaretteBrand')); ?>
        </div>
        
        <div class="clr"></div>
        
        <div style="text-align: center">
            <a style="color: #ffffff; font-size: 16px;" href="<?php print $this->createUrl('event/index'); ?>"><?php print Yii::t('application', 'Отменить'); ?></a>
        </div>
    </div>
    <div class="footer_b"></div>
</div>
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'verificationTermsDialog',
        'scriptFile' => false,
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => Yii::t('application', 'Условия конфидециальности'),
            'autoOpen' => false,
            'modal' => true,
            'resizable' => false,
            'draggable' => false,
            'width' => 500
        ),
    ));
    ?>

<p><?php print Yii::t('application', 'Настоящим соглашением регулируются отношения Пользователя с приложением БУДУТАМ в области конфиденциальности предоставленной информации.'); ?></p>
<p>1. <?php print Yii::t('application', 'Загружаемые данные (фото с паспортом) строго конфиденциальны и предназначены для проверки совершеннолетия пользователя.'); ?></p>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'verificationExampleDialog',
        'scriptFile' => false,
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => Yii::t('application', 'Фотоверификация аккаунта'),
            'autoOpen' => false,
            'modal' => true,
            'resizable' => false,
            'draggable' => false,
            'width' => 465
        ),
    ));
    ?>
<img src="/images/verification_example.png" />
<p>
    <?php print Yii::t('application', 'К участию допускаются граждане Республики Беларусь старше 18 лет'); ?>
</p>
<button id="verificationExampleCloseBtn" class="but_blue" type="submit"><?php print Yii::t('application', 'Регистрация'); ?></button>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>