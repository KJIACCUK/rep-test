<?php
$this->layout = '//layouts/unauthorized';
$this->setPageTitle(Yii::t('application', 'Авторизация'))->setPageName(Yii::t('application', 'Авторизация'));
$cs = Yii::app()->clientScript;
/* @var $cs CClientScript */
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
?>

<p><?php print Yii::t('application', 'Вы можете использовать логин/пароль с bluestone.by или с карты, полученной во время вечеринок или опросов.'); ?></p>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'login-form',
    'enableAjaxValidation' => false,
    'focus' => array($model, 'login'),
));
?>

<?php
/* @var $form CActiveForm */
/* @var $model UserLoginForm */
?>

<?php print $form->errorSummary($model); ?>

<p><?php print $form->label($model, 'login'); ?></p>
<?php print $form->textField($model, 'login', array('placeholder' => Yii::t('application', 'Логин или E-mail'))); ?>

<p><?php print $form->label($model, 'password'); ?></p>
<?php print $form->passwordField($model, 'password', array('placeholder' => Yii::t('application', 'Пароль'))); ?>

<?php print CHtml::submitButton(Yii::t('application', 'Войти'), array('class' => 'but_light open_p')); ?>

<a href="<?php print $this->createUrl('site/forgot_password'); ?>" class="pass_trable"><?php print Yii::t('application', 'Забыли пароль?'); ?></a>

<button class="but_blue regist" type="button" onclick="window.location.href = '<?php print $this->createUrl('site/registration'); ?>'"><?php print Yii::t('application', 'Регистрация'); ?></button>
<div class="clr"></div>
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'authPopupDialog',
        'scriptFile' => false,
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => false,
            'autoOpen' => false,
            'modal' => true,
            'resizable' => false,
            'draggable' => false,
            'width' => 600,
            'height' => 600,
        ),
    ));
    ?>

<a href="#" id="authPopupBtnClose"><img src="/images/close.png" /></a>
<img id="authPopupContent" src="/images/auth_popup.png" />

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

<?php $this->endWidget(); ?>