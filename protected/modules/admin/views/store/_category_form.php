<?php

    /* @var $this StoreController */
    /* @var $model ProductCategory */
    
    if(!$model->isNewRecord)
    {
        $model->parentCategoryName = $model->parentCategory?$model->parentCategory->name:'';
    }
?>

<div class="form">

    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'product-category-form',
            'enableAjaxValidation' => false,
        ));
    ?>

    <?php echo $form->errorSummary($model); ?>
    <?php /* @var $form TbActiveForm */ ?>

    <?php echo $form->textFieldControlGroup($model, 'name', array('span' => 8, 'maxlength' => 255)); ?>
    <?php if($model->isNewRecord): ?>
    <?php echo $form->dropDownListControlGroup($model, 'parent', ProductHelper::getCategoriesToEdit($model->productCategoryId), array('span' => 8)); ?>
    <?php else: ?>
    <?php print $form->uneditableFieldControlGroup($model, 'parentCategoryName', array('span' => 8)); ?>
    <?php endif; ?>

    <div class="form-actions">
        <?php
            echo TbHtml::submitButton($model->isNewRecord?Yii::t('application', 'Добавить'):Yii::t('application', 'Сохранить'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
        ?>
        <?php
            echo TbHtml::link(Yii::t('application', 'Отмена'), $this->createUrl('indexCategory'));
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->