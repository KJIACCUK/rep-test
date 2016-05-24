<?php
    /* @var $this StoreController */
    /* @var $model Product */
    /* @var $cs CClientScript */

    $cs = Yii::app()->clientScript;

    $cs->registerScript('store', "
        function toggleProductType()
        {
            if($('#Product_type').val() == '".Product::TYPE_WITH_RECEIPT_ADDRESS."')
            {
                $('#receiptAddressPanel').show();
                //$('#attachmentFilePanel').hide();
            }
            else if($('#Product_type').val() == '".Product::TYPE_WITH_SERTIFICATE."')
            {
                //$('#attachmentFilePanel').show();
                $('#receiptAddressPanel').hide();
            }
            else
            {
                $('#receiptAddressPanel').hide();
                //$('#attachmentFilePanel').hide();
            }
        }
        
        $('#Product_type').change(function(){
            toggleProductType();
        });
        
        toggleProductType();
    ");
?>

<div class="form">

    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'product-form',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
    ?>

    <?php print $form->errorSummary($model); ?>
    <?php /* @var $form TbActiveForm */ ?>

    <?php print$form->dropDownListControlGroup($model, 'productCategoryId', ProductHelper::getCategoriesToEdit(), array('span' => 8)); ?>
    <?php print $form->textFieldControlGroup($model, 'publisherName', array('span' => 8, 'maxlength' => 255)); ?>
    <?php print $form->textFieldControlGroup($model, 'name', array('span' => 8, 'maxlength' => 255)); ?>
    <?php print $form->textAreaControlGroup($model, 'description', array('rows' => 6, 'span' => 8)); ?>
    <?php print $form->fileFieldControlGroup($model, 'imageFile', array('span' => 8)); ?>
    <?php if($model->image): ?>
        <div>
            <?php print TbHtml::imagePolaroid($model->image, '', array('class' => 'span8', 'style' => 'margin-left: 0px;')); ?>
            <div class="clearfix"></div>
        </div> 
    <?php endif; ?>
    <?php print $form->textFieldControlGroup($model, 'cost', array('span' => 8, 'maxlength' => 11)); ?>
    <?php print$form->dropDownListControlGroup($model, 'type', ProductHelper::typesToEdit(), array('span' => 8)); ?>
    
    <?php '
        
        <div id="attachmentFilePanel">
        <?php print $form->fileFieldControlGroup($model, "attachmentFile", array("span" => 8)); ?>
        <?php if($model->attachment): ?>
            <div style="margin-bottom: 20px;">
                <?php print TbHtml::link(Yii::t("application", "Скачать"), $model->attachment); ?>
            </div>
        <?php endif; ?>
    </div>
        '?>
    
    

    <div id="receiptAddressPanel">
        <?php print $form->textAreaControlGroup($model, 'receiptAddress', array('rows' => 6, 'span' => 8)); ?>
    </div>
    <?php print $form->textFieldControlGroup($model, 'articleCode', array('span' => 8, 'maxlength' => 255)); ?>
    <?php print $form->textFieldControlGroup($model, 'itemsCount', array('span' => 8, 'maxlength' => 255)); ?>
    <?php print $form->textFieldControlGroup($model, 'dateStart', array('span' => 8, 'maxlength' => 10)); ?>
    <?php print $form->checkBoxControlGroup($model, 'isActive', array('span' => 5)); ?>

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