<?php
    /* @var $cs CClientScript */
    /* @var $this UserController */ 
    /* @var $model UserVerificationRequest */
    
    $this->layout = '//layouts/empty';
    $this->setPageTitle(Yii::t('application', 'Заказать звонок'));
    $cs = Yii::app()->clientScript;
?>
<div class="osn_b login_b">
    <div class="bl_login verif_p">
        <h1 style="margin-top:70px;"><?php print Yii::t('application', 'Заказать звонок'); ?></h1>
        
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'add_verification_request-form',
            'enableAjaxValidation' => false,
            'focus' => array($model, 'messengerLogin'),
        )); ?>

        <?php /* @var $form CActiveForm */ ?>

        <?php print $form->errorSummary($model); ?>

        <div class="skype_l min_tx">
            <p><?php print Yii::app()->params['messengers'][$messenger]; ?></p>
            <div class="inp_tx customtx">
                <div class="inp_txr"></div>
                <div class="inp_txl"></div>
                <?php print $form->textField($model, 'messengerLogin', array('placeholder' => Yii::t('application', 'Логин'))); ?>
            </div>
        </div>
            <div class="time_c min_tx">
                <p><?php print $form->label($model, 'callTime'); ?></p>
                <?php print $form->dropDownList($model, 'callTimeHours', CommonHelper::getRange(0, 23), array('class' => 'select-field')); ?>
                <?php print $form->dropDownList($model, 'callTimeMinutes', CommonHelper::getRange(0, 59), array('class' => 'select-field')); ?>
            </div>
            <div class="clr"></div>
            <div class="date_c min_tx">
                <p><?php print $form->label($model, 'callDate'); ?></p>
                <?php print $form->dropDownList($model, 'callDateDay', CommonHelper::getRange(1, 31), array('class' => 'select-field')); ?>
                <?php print $form->dropDownList($model, 'callDateMonth', Yii::app()->locale->getMonthNames('wide', true), array('class' => 'select-field')); ?>
                <?php print $form->dropDownList($model, 'callDateYear', array_combine(range(1900, date('Y')), range(1900, date('Y'))), array('class' => 'select-field')); ?>
            </div>
            <?php print CHtml::submitButton(Yii::t('application', 'Заказать'), array('class' => 'but_light zak_zv')); ?>
            <a style="color: #ffffff; font-size: 16px; margin-left: 20px;" href="<?php print $this->createUrl('user/index'); ?>"><?php print Yii::t('application', 'Отменить'); ?></a>
        <?php $this->endWidget(); ?>
    </div>
    <div class="footer_b"></div>
</div>