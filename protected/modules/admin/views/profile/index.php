<?php
    /* @var $this ProfileController */
    /* @var $model Employee */
    /* @var $cs CClientScript */

    $cs = Yii::app()->clientScript;

    $types = UserHelper::getAdminTypes();
    $model->type = $types[$model->account->type];

    $cs->registerScript('profile', "

        function togglePasswordPanel(){
            if($('#checkboxIsChangePassword').prop('checked'))
            {
                $('#changePasswordPanel').show();
            }
            else
            {
                $('#changePasswordPanel').hide();
            }
        }

        $('#checkboxIsChangePassword').click(function(){
            togglePasswordPanel();
        });

        togglePasswordPanel();

    ");
?>
<h1><?php print Yii::t('application', 'Профиль'); ?></h1>
<div class="form">
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'employee-form',
            'enableAjaxValidation' => false,
        ));
    ?>

    <?php /* @var $form TbActiveForm */ ?>
    <?php echo $form->errorSummary($model); ?>

    <?php print $form->uneditableFieldControlGroup($model, 'type', array('span' => 8)); ?>

    <?php echo $form->textFieldControlGroup($model, 'name', array('span' => 8, 'maxlength' => 255)); ?>
    <?php echo $form->textFieldControlGroup($model, 'email', array('span' => 8, 'maxlength' => 255)); ?>
    <?php echo $form->textFieldControlGroup($model, 'login', array('span' => 8, 'maxlength' => 255)); ?>

    <?php echo $form->checkBoxControlGroup($model, 'isChangePassword', array('id' => 'checkboxIsChangePassword', 'span' => 5)); ?>
    <div id="changePasswordPanel">
        <?php echo $form->passwordFieldControlGroup($model, 'password', array('span' => 8, 'maxlength' => 255)); ?>
        <?php echo $form->passwordFieldControlGroup($model, 'passwordConfirm', array('span' => 8, 'maxlength' => 255)); ?>
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