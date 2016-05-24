<?php
    /* @var $this EmployeeController */
    /* @var $model Employee */
    /* @var $type string */
    /* @var $cs CClientScript */
    
    $cs = Yii::app()->clientScript;

    if($model->isNewRecord)
    {
        $type = Web::getParam('type');

        switch($type)
        {
            case 'administrators':
                $defaultRole = Account::TYPE_ADMIN;
                break;

            case 'moderators':
                $defaultRole = Account::TYPE_MODERATOR;
                break;

            case 'operators':
                $defaultRole = Account::TYPE_OPERATOR;
                break;

            default :
                $defaultRole = '';
        }

        $model->type = $defaultRole;
    }
    else
    {
        $types = UserHelper::getAdminTypes();
        $model->type = $types[$model->account->type];
        $model->isActive = $model->account->isActive;
        
        
        $cs->registerScript('update_employee', "

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
    }
?>

<div class="form">

    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'employee-form',
            'enableAjaxValidation' => false,
        ));
    ?>

    <?php /* @var $form TbActiveForm */ ?>

    <?php echo $form->errorSummary($model); ?>

    <?php if($model->isNewRecord): ?>
            <?php print $form->dropDownListControlGroup($model, 'type', UserHelper::getAdminTypes(), array('span' => 8)); ?>
        <?php else: ?>
            <?php print $form->uneditableFieldControlGroup($model, 'type', array('span' => 8)); ?>
    <?php endif; ?>

    <?php echo $form->textFieldControlGroup($model, 'name', array('span' => 8, 'maxlength' => 255)); ?>
    <?php echo $form->textFieldControlGroup($model, 'email', array('span' => 8, 'maxlength' => 255)); ?>
    <?php echo $form->textFieldControlGroup($model, 'login', array('span' => 8, 'maxlength' => 255)); ?>

    <?php if($model->isNewRecord): ?>
            <?php echo $form->passwordFieldControlGroup($model, 'password', array('span' => 8, 'maxlength' => 255)); ?>
            <?php echo $form->passwordFieldControlGroup($model, 'passwordConfirm', array('span' => 8, 'maxlength' => 255)); ?>
        <?php else: ?>
            <?php echo $form->checkBoxControlGroup($model, 'isChangePassword', array('id' => 'checkboxIsChangePassword', 'span' => 5)); ?>
            <div id="changePasswordPanel">
                <?php echo $form->passwordFieldControlGroup($model, 'password', array('span' => 8, 'maxlength' => 255)); ?>
                <?php echo $form->passwordFieldControlGroup($model, 'passwordConfirm', array('span' => 8, 'maxlength' => 255)); ?>
            </div>
    <?php endif; ?>

    <?php if(!$model->isNewRecord): ?>
    <?php echo $form->checkBoxControlGroup($model, 'isActive', array('id' => 'checkboxIsChangePassword', 'span' => 5)); ?>
    <?php endif; ?>

    <div class="form-actions">
        <?php
            echo TbHtml::submitButton($model->isNewRecord?Yii::t('application', 'Добавить'):Yii::t('application', 'Сохранить'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
        ?>
        <?php
            echo TbHtml::link(Yii::t('application', 'Отмена'), $this->createUrl('index'));
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->