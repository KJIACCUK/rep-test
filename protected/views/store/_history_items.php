<?php
    /* @var $this StoreController */
    /* @var $purchases ProductPurchase[] */
?>
<?php foreach($purchases as $item): ?>
<li>
    <a class="mir_img" href="<?php print $this->createUrl('store/detail', array('productId' => $item->product->productId)); ?>">
        <img src="<?php print CommonHelper::getImageLink($item->product->image, '200x150'); ?>" alt=""/>
    </a>
    <div class="mir_tx">
        <p><?php print CHtml::encode($item->product->name); ?></p>
        <p class="sm_p"><?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($item->pointsCount)); ?></p>
    </div>
</li>
<?php endforeach; ?>