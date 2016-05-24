<?php
/* @var $this StoreController */
/* @var $data ProductPurchase */
    
    $types = ProductHelper::typesToEdit();
?>

<div class="view" style="border: 1px solid #0066A4; border-radius: 3px; padding: 10px; margin-bottom: 10px;">
    <div class="pull-left" style="width: 60px;">
        # <?php print $data->productPurchaseId; ?>
    </div>
    <div class="pull-left" style="width: 130px;">
        <?php print date(Yii::app()->params['dateTimeFormat'], $data->dateCreated); ?>
    </div>
    
    <div class="pull-left" style="width: 100px;">
        <?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($data->pointsCount)); ?>
    </div>
    
    <div class="pull-left" style="width: 300px;">
        <strong><?php print CHtml::encode($data->product->name); ?></strong>
    </div>
    
    <div class="pull-left" style="width: 300px;">
        <?php print CHtml::encode($data->user->name); ?>
    </div>
    
    <div class="pull-left" style="width: 100px;">
        <?php print $data->purchaseCode; ?>
    </div>
    
    <?php print TbHtml::linkButton(Yii::t('application', 'Детали'), array('url' => $this->createUrl('orderView', array('id' => $data->productPurchaseId)), 'class' => 'btnOrdersDetails pull-right', 'color' => TbHtml::BUTTON_COLOR_INFO)); ?>
    
    <div class="clearfix"></div>
</div>