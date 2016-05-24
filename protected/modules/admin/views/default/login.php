<?php
    /* @var $this DefaultController */
    /* @var $model AdminLoginForm */
    $this->pageTitle = Yii::t('application', 'Логин');

    $this->layout = 'unauthorized';
?>
<div class="row" style="height: 150px;"></div>
<div class="row">
    <div class="span4 offset4">
        <div class="form well admin-login-form-panel">
            
            <h1><?php print Yii::t('application', 'Авторизация'); ?></h1>

            <?php
                $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                    'id' => 'admin-login-form',
                    'enableAjaxValidation' => false
                ));
            ?>
            <?php /* @var $form TbActiveForm */; ?>
            <?php echo $form->errorSummary($model); ?>

            <?php echo $form->textFieldControlGroup($model, 'login', array('span' => 3, 'placeholder' => Yii::t('application', 'Логин'))); ?>
            <?php echo $form->passwordFieldControlGroup($model, 'password', array('span' => 3, 'placeholder' => Yii::t('application', 'Пароль'))); ?>
            
            <?php print $form->checkBoxControlGroup($model, 'rememberMe', array('span' => 3)); ?>

            <?php
                echo TbHtml::submitButton(Yii::t('application', 'Войти'), array(
                    'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                    'size' => TbHtml::BUTTON_SIZE_DEFAULT,
                ));
            ?>

            <?php $this->endWidget(); ?>

        </div><!-- form -->
    </div>
</div>