<?php
    /* @var $cs CClientScript */
    /* @var $this SiteController */
    /* @var $model User */
    
    $this->layout = '//layouts/unauthorized';
    $this->setPageTitle(Yii::t('application', 'Регистрация'));
    $cs = Yii::app()->clientScript;
    $cs->registerScript('terms_check', '
        $("#User_termsAgree").change(function(){
            if(this.checked)
            {
                $("#btnRegister").attr("disabled", false);
            }
            else
            {
                $("#btnRegister").attr("disabled", true);
            }
            
            return true;
        });
    ');
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'registration-form',
    'enableAjaxValidation' => false,
    'focus' => array($model, 'firstname'),
)); ?>

<?php /* @var $form CActiveForm */ ?>

    <?php print $form->errorSummary($model); ?>

    <div class="min_tx">
        <p><?php print $form->label($model, 'firstname'); ?>*</p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'firstname'); ?>
        </div>
    </div>

    <div class="min_tx">
        <p><?php print $form->label($model, 'lastname'); ?>*</p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'lastname'); ?>
        </div>
    </div>

    <div class="date_r min_tx">
        <p><?php print $form->label($model, 'birthday', array('for' => 'User_birthdayDay')); ?>*</p>
        <?php print $form->dropDownList($model, 'birthdayDay', array_combine(range(1, 31), range(1, 31)), array('class' => 'select-field')); ?>
        <?php print $form->dropDownList($model, 'birthdayMonth', Yii::app()->locale->getMonthNames('wide', true), array('class' => 'select-field')); ?>
        <?php print $form->dropDownList($model, 'birthdayYear', array_combine(range(1900, date('Y')), range(1900, date('Y'))), array('class' => 'select-field')); ?>
    </div>

    <div class="phone_c min_tx">
        <p><?php print $form->label($model, 'phone'); ?>*</p>
        <?php print $form->dropDownList($model, 'phoneCode', Yii::app()->params['phoneCodes'], array('class' => 'select-field')); ?>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'phone', array('placeholder' => Yii::t('application', 'Номер'))); ?>
        </div>
    </div>
    
    <div class="clr"></div>
    <div class="line_reg"></div>
	
    <div class="min_tx">
        <p><?php print $form->label($model, 'email'); ?>*</p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'email'); ?>
        </div>
    </div>
    
    <div class="min_tx">
        <p><?php print $form->label($model, 'password'); ?>*</p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->passwordField($model, 'password'); ?>
        </div>
    </div>
		
    <div class="min_tx">
        <p><?php print $form->label($model, 'passwordConfirm'); ?>*</p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->passwordField($model, 'passwordConfirm'); ?>
        </div>
    </div>
    
    <div class="block_sog">
        <?php print CHtml::checkBox('termsAgree', false, array('id' => 'User_termsAgree', 'class' => 'check_b checkbox-field')); ?>
        <p><label id="label-for-User_termsAgree" for="User_termsAgree">согласен с условиями</label> <a href="<?php print $this->createUrl('site/terms'); ?>">пользовательского соглашения</a></p>
    </div>
    
    <a href="<?php print $this->createUrl('site/login'); ?>" class="pass_trable"><?php print Yii::t('application', 'Вернуться к логину'); ?></a>
    
    <?php print CHtml::submitButton(Yii::t('application', 'Зарегистрироваться'), array('id' => 'btnRegister', 'class' => 'but_light', 'disabled' => true)); ?>
    <div class="clr"></div>

<?php $this->endWidget(); ?>