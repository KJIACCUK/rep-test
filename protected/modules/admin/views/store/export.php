<?php
    /* @var $this StoreController */
    /* @var $model ExportOrderForm */
?>
<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Экспорт заказов'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<div class="form">
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'export_orders-form',
            'enableAjaxValidation' => false,
        ));
    ?>
    
    <?php /* @var $form TbActiveForm */ ?>
    <?php echo $form->errorSummary($model); ?>
    
    <h3><?php print Yii::t('application', 'Фильтр'); ?></h3>
    
    <?php print TbHtml::label(Yii::t('application', 'Дата создания'), ''); ?>
    <div>
        <div class="pull-left span3" style="margin-left: 0px;">
            <?php print $form->textFieldControlGroup($model, 'createDateStart', array('placeholder' => date(Yii::app()->params['dateFormat']))); ?>
        </div>
        <div class="pull-left span3" style="margin-left: 0px;">
            <?php print $form->textFieldControlGroup($model, 'createDateEnd', array('placeholder' => date(Yii::app()->params['dateFormat']))); ?>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <?php print $form->radioButtonListControlGroup($model, 'type', $model->getTypesList(), array('span' => 8)); ?>
    <br />
    <?php print $form->dropDownListControlGroup($model, 'productId', $model->getProductsList(), array('span' => 8)); ?>
    
    <h3><?php print Yii::t('application', 'Поля'); ?></h3>
    
    <?php print $form->checkBoxListControlGroup($model, 'fields', $model->getFieldsList(), array('span' => 8)); ?>
    
    <h3><?php print Yii::t('application', 'Выгрузка'); ?></h3>
    
    <?php print TbHtml::label(Yii::t('application', 'Количество записей'), ''); ?>
    <div>
        <div class="pull-left span1" style="margin-left: 0px;">
            <?php print $form->textFieldControlGroup($model, 'offset', array('span' => 1)); ?>
        </div>
        <div class="pull-left span1" style="margin-left: 10px;">
            <?php print $form->textFieldControlGroup($model, 'limit', array('span' => 1)); ?>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <?php print $form->textFieldControlGroup($model, 'filename', array('span' => 8)); ?>
    
    <?php print $form->checkBoxControlGroup($model, 'writeHeaders', array('span' => 5)); ?>
    
    <div class="form-actions">
        <?php
            echo TbHtml::submitButton(Yii::t('application', 'Экспорт'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
        ?>
    </div>
    
    <?php $this->endWidget(); ?>
</div>
