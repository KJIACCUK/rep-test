<?php
/* @var $cs CClientScript */
/* @var $this UserController */

$this->layout = '//layouts/empty';
$this->setPageTitle(Yii::t('application', 'Фотоверификация аккаунта'));
$cs = Yii::app()->clientScript;

$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');

$cs->registerScript('verification_example_popup', "
        $('.verification_example_btn').click(function(){
            $('#verificationExampleDialog').dialog('open');
            ga('send', 'event', 'Web', 'Button_View_example_Click', 'Photo_Verification');
            return false;
        });
        
        $('.get_full_access_btn').click(function(){
            ga('send', 'event', 'Web', 'Button_Get_full_access_Click', 'Photo_Verification');
            window.location.href='".$this->createUrl('user/verification')."';
            return false;
        });
        
        $('.cancel_btn').click(function(){
            ga('send', 'event', 'Web', 'Button_Cancel_Click', 'Photo_Verification');
            return true;
        });
    ");

$cs->registerCss('dialogs', "
        .ui-dialog {top: 50px!important;}
    ");
?>
<div class="osn_b login_b">
    <div class="bl_login ask_verification">
        <h1><?php print Yii::t('application', 'Фотоверификация аккаунта'); ?></h1>
        <p>
            <?php print Yii::t('application', 'Пройди верификацию, чтобы зарабатывать баллы, обменивать их на сувениры, сертификаты и пригласительные.'); ?></a>
        </p>
        <p class="instruction">
            <?php print Yii::t('application', 'Для этого'); ?>:<br /> 
            1. <?php print Yii::t('application', 'Заполни данные о себе'); ?><br /> 
            2. <?php print Yii::t('application', 'Отправь своё фото с паспортом,'); ?><br /> 
            <?php print Yii::t('application', 'как в примере'); ?>
        </p>
        <button class="but_light verification_example_btn" type="submit"><?php print Yii::t('application', 'Посмотреть пример'); ?></button>
        <p>
            <?php print Yii::t('application', 'К участию допускаются граждане Республики Беларусь старше 18 лет'); ?>
        </p>
        <button class="but_blue get_full_access_btn" type="submit"><?php print Yii::t('application', 'Получить полный доступ'); ?></button>
        <div style="text-align: center; margin-top: 10px;">
            <a style="color: #ffffff; font-size: 16px;" class="cancel_btn" href="<?php print $this->createUrl('event/index'); ?>"><?php print Yii::t('application', 'Отменить'); ?></a>
        </div>
    </div>
    <div class="footer_b"></div>
</div>
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
<button id="verificationExampleCloseBtn" onclick="window.location.href = '<?php print $this->createUrl('user/verification'); ?>'" class="but_blue" type="submit"><?php print Yii::t('application', 'Регистрация'); ?></button>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>