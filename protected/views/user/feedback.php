<?php
    /* @var $this UserController */
    $this->setPageTitle(Yii::t('application', 'Справка'));
    $this->layout = '//layouts/inner';
?>
<div class="title_l top_pad">
    <?php print Yii::t('application', 'Сообщить о проблеме'); ?>
    <a href="<?php print $this->createUrl('user/settings'); ?>" class="read_all">
        <?php print Yii::t('application', 'Назад к настройкам'); ?>
    </a>
</div>
<div class="line_reg"></div>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'feedback-form',
    'enableAjaxValidation' => false
)); ?>

<?php /* @var $form CActiveForm */ ?>
<?php print $form->errorSummary($model); ?>

    <div class="min_tx">
        <p><?php print $form->label($model, 'title'); ?></p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'title'); ?>
        </div>
    </div>

    <div class="mir_text">
        <p><?php print $form->label($model, 'description'); ?></p>
        <?php print $form->textArea($model, 'description', array('rows' => 5, 'cols' => 50)); ?>
    </div>

    <?php print CHtml::submitButton(Yii::t('application', 'Отправить'), array('id' => 'btnSend', 'class' => 'but_light')); ?>
    <div class="clr"></div>

<?php $this->endWidget(); ?>
