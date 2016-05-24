<?php
/* @var $this SettingController */
/* @var $model SettingForm */
?>
<h1><?php print Yii::t('application', 'Настройки'); ?></h1>
<div class="form">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'employee-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <?php /* @var $form TbActiveForm */ ?>
    <?php echo $form->errorSummary($model); ?>

    <h3><?php print Yii::t('application', 'Настройки баллов'); ?></h3>

    <?php echo $form->textFieldControlGroup($model, 'pointSocialInvite', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'pointVerification', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'pointResearchVisit', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'pointResearchAnswer', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'pointEventCreate', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'pointTenEventsSubscribed', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'pointSocialShare', array('span' => 5)); ?>
    <?php echo $form->textFieldControlGroup($model, 'promoPointsPerCode', array('span' => 5)); ?>

    <h3><?php print Yii::t('application', 'Настройки времени работы оператора'); ?></h3>

    <div>
        <?php print TbHtml::label(Yii::t('application', 'Понедельник'), 'SettingForm_operatorMondayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorMondayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorMondayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>
    
    <div>
        <?php print TbHtml::label(Yii::t('application', 'Вторник'), 'SettingForm_operatorTuesdayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorTuesdayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorTuesdayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>
    
    <div>
        <?php print TbHtml::label(Yii::t('application', 'Среда'), 'SettingForm_operatorWednesdayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorWednesdayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorWednesdayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>
    
    <div>
        <?php print TbHtml::label(Yii::t('application', 'Четверг'), 'SettingForm_operatorThursdayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorThursdayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorThursdayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>
    
    <div>
        <?php print TbHtml::label(Yii::t('application', 'Пятница'), 'SettingForm_operatorFridayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorFridayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorFridayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>
    
    <div>
        <?php print TbHtml::label(Yii::t('application', 'Суббота'), 'SettingForm_operatorSaturdayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorSaturdayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorSaturdayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>
    
    <div>
        <?php print TbHtml::label(Yii::t('application', 'Воскресение'), 'SettingForm_operatorSundayStartTime'); ?>
        <?php echo $form->textField($model, 'operatorSundayStartTime', array('span' => 1, 'label' => false)); ?>
        <?php echo $form->textField($model, 'operatorSundayEndTime', array('span' => 1, 'label' => false)); ?>
    </div>


    <div class="form-actions">
        <?php
        echo TbHtml::submitButton(Yii::t('application', 'Сохранить'), array(
            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
            'size' => TbHtml::BUTTON_SIZE_LARGE,
        ));
        ?>
    </div>

    <?php $this->endWidget(); ?>
</div>