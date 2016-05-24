<?php
    /* @var $this UserController */
    /* @var $user array */
    /* @var $model User */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Моя личная страница'));
    
    $cs = Yii::app()->clientScript;
    $cs->registerScript('profile_edit', '
        $("#btnSave").click(function(){
            $("#user-form").submit();
            return true;
        });
    ');
    
    $dateFormatter = Yii::app()->locale->getDateFormatter();
?>

<div id="profile-edit" class="title_l">
    <div class="profile-edit-title"><?php print CHtml::encode(Yii::t('application', 'Моя личная страница')); ?></div>
    <div class="profile-edit-button"><?php print CHtml::button(Yii::t('application', 'Сохранить'), array('id' => 'btnSave', 'class' => 'but_light')); ?><a class="mode-save" href="<?php print $this->createUrl('user/index'); ?>"><?php print CHtml::encode(Yii::t('application', 'отмена')); ?></a></div>
    <div class="clr"></div>
</div>
<div class="line_reg"></div>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'user-form',
    'enableAjaxValidation' => false,
)); ?>

<?php /* @var $form CActiveForm */ ?>

<?php print $form->errorSummary($model); ?>

<?php $this->widget('application.widgets.UserAvatar', array('image' => $user['image'], 'saveUrl' => 'user/saveAvatar')); ?>

<div class="inf_lich">		 
    <div class="inp_tx customtx black_t">
        <div class="inp_txr"></div>
        <div class="inp_txl"></div>
        <?php print $form->textField($model, 'firstname'); ?>
    </div>
    
    <div class="inp_tx customtx black_t">
        <div class="inp_txr"></div>
        <div class="inp_txl"></div>
        <?php print $form->textField($model, 'lastname'); ?>
    </div>
    
    <div class="inp_tx customtx black_t">
        <div class="inp_txr"></div>
        <div class="inp_txl"></div>
        <?php print $form->textField($model, 'email'); ?>
    </div>

    <div id="profile-date" class="date_r min_tx">
        <?php print $form->dropDownList($model, 'birthdayDay', array_combine(range(1, 31), range(1, 31)), array('class' => 'select-field black_t')); ?>
        <?php print $form->dropDownList($model, 'birthdayMonth', Yii::app()->locale->getMonthNames('wide', true), array('class' => 'select-field black_t')); ?>
        <?php print $form->dropDownList($model, 'birthdayYear', array_combine(range(1900, date('Y')), range(1900, date('Y'))), array('class' => 'select-field black_t')); ?>
    </div>

    <div class="login_m min_tx">
        <?php print $form->dropDownList($model, 'phoneCode', Yii::app()->params['phoneCodes'], array('class' => 'select-field black_t')); ?>
        <div class="inp_tx customtx black_t">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'phone', array('placeholder' => Yii::t('application', 'Номер'))); ?>
        </div>
    </div>
    
    <div class="clr"></div>
    
    <div class="login_m min_tx">
        <?php print $form->dropDownList($model, 'messenger', Yii::app()->params['messengers'], array('class' => 'select-field black_t')); ?>
        <div class="inp_tx customtx black_t">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'messengerLogin', array('placeholder' => Yii::t('application', 'Логин'))); ?>
        </div>
    </div>
    
    <div class="clr"></div>

</div>

<div class="clr"></div>
<div class="title_l"><?php print Yii::t('application', 'Интересы'); ?></div>			
<div class="line_reg"></div>
<div class="love_l"><?php print Yii::t('application', 'Любимая музыка'); ?>:</div>
<?php print $form->dropDownList($model, 'favoriteMusicGenre', array_combine(Yii::app()->params['musicGenres'], Yii::app()->params['musicGenres']), array('class' => 'select-field black_t')); ?>
<div class="clr"></div>

<div class="clr" style="margin-bottom:30px;"></div>
<div class="title_l"><?php print Yii::t('application', 'Сменить пароль'); ?></div>
<div class="line_reg"></div>
<div class="old_pass">
    <p><?php print $form->label($model, 'oldPassword', array('for' => 'User_oldPassword')); ?></p>
    <div class="inp_tx customtx black_t">
        <div class="inp_txr"></div>
        <div class="inp_txl"></div>
        <?php print $form->passwordField($model, 'oldPassword'); ?>
    </div>
</div>
<div class="new_pass">
    <p><?php print $form->label($model, 'newPassword', array('for' => 'User_newPassword')); ?></p>
    <div class="inp_tx customtx black_t">
        <div class="inp_txr"></div>
        <div class="inp_txl"></div>
        <?php print $form->passwordField($model, 'newPassword'); ?>
    </div>
</div>
<div class="clr"></div>

<?php $this->endWidget(); ?>