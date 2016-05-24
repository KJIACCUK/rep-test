<?php
    /* @var $this SiteController */
    /* @var $cs CClientScript */
    
    $this->layout = '//layouts/unauthorized';
    $this->setPageTitle(Yii::t('application', 'Восстановление пароля'))->setPageName(Yii::t('application', 'Восстановление пароля'));
?>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'forgot_password-form',
    'enableAjaxValidation' => false,
    'focus' => array($model, 'name'),
)); ?>

<?php /* @var $form CActiveForm */ ?>
<?php print $form->errorSummary($model); ?>

<div class="min_tx">
    <p><?php print $form->label($model, 'email'); ?>*</p>
    <div class="inp_tx customtx">
        <div class="inp_txr"></div>
        <div class="inp_txl"></div>
        <?php print $form->textField($model, 'email'); ?>
    </div>
</div>

<div class="block_sog"></div>

<a href="<?php print $this->createUrl('site/login'); ?>" class="pass_trable"><?php print Yii::t('application', 'Вернуться к логину'); ?></a>
    
<?php print CHtml::submitButton(Yii::t('application', 'Восстановить'), array('id' => 'btnReset', 'class' => 'but_light')); ?>
<div class="clr"></div>

<?php $this->endWidget(); ?>