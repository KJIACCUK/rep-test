<?php
    /* @var $this StoreController */
    /* @var $products Product[] */
    /* @var $currentUser User */
    
    $currentUser = $this->getUser();
    $canPurchaseByTimeout = ProductHelper::canPurchaseByTimeout($currentUser->userId);
?>
<?php foreach($products as $item): ?>
<li>
    <a class="mir_img" href="<?php print $this->createUrl('store/detail', array('productId' => $item->productId)); ?>">
        <img src="<?php print CommonHelper::getImageLink($item->image, '200x150'); ?>" alt=""/>
    </a>
    <?php if($currentUser->points >= $item->cost): ?>
    
        <?php if($canPurchaseByTimeout): ?>
            <?php if($item->type == Product::TYPE_WITH_SERTIFICATE): ?>
                <div style="display: none;" class="wind_cl">
                    <div class="ico_uks"></div>
                    <p>
                        <?php print Yii::t('application', 'После покупки товара на ваш <br/> E-mail будет отправлен сертификат'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if($item->type == Product::TYPE_WITH_RECEIPT_ADDRESS): ?>
                <div style="display: none;" class="wind_cl">
                    <div class="ico_uks"></div>
                    <p>
                        <?php print Yii::t('application', 'Товар можно забрать по адресу'); ?> <br />
                        <?php print CHtml::encode($item->receiptAddress); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if($item->type == Product::TYPE_WITH_DELIVERY): ?>
                <div style="display: none;" class="wind_cl">
                    <div class="ico_uks"></div>
                    <p>
                        <?php print Yii::t('application', 'После покупки товар будет отправлен <br/>на указанный вами адрес'); ?>
                    </p>
                </div>
            <?php endif; ?>
            <button onclick="window.location.href = '<?php print $this->createUrl('store/ordering', array('productId' => $item->productId)); ?>'" class="but_light but_mir buyProductButton" type="button"><?php print Yii::t('application', 'Купить'); ?></button>            
            <?php else: ?>
                <div style="display: none;" class="wind_cl">
                    <div class="ico_uks"></div>
                    <p><?php print Yii::t('application', 'Совершать покупки можно раз в 30 дней.'); ?>
                    </p>
                </div>
            <button class="but_blue but_mir buyProductButton" type="button"><?php print Yii::t('application', 'Купить'); ?></button>
            <?php endif; ?>
    <?php else: ?>
        <div style="display: none;" class="wind_cl">
            <div class="ico_uks"></div>
            <p><?php print Yii::t('application', 'У вас недостаточно баллов для покупки.'); ?>
            </p>
        </div>
        <button class="but_blue but_mir buyProductButton" type="button"><?php print Yii::t('application', 'Купить'); ?></button>
    <?php endif; ?>
    <div class="mir_tx">
        <?php if($item->publisherName): ?>
        <p class="sm_tx"><?php print CHtml::encode($item->publisherName); ?></p>         
        <?php endif; ?>
        <p>
            <a href="<?php print $this->createUrl('store/detail', array('productId' => $item->productId)); ?>" style="color: #FFFFFF; text-decoration: none;">
                <?php print CHtml::encode($item->name); ?>
            </a>
        </p>
        <div class="date">
            <?php if($item->dateStart): ?>
            <?php print date(Yii::app()->params['dateFormat'], $item->dateStart); ?>
            <?php endif; ?>
        </div>
        <p class="sm_p">
            <a href="<?php print $this->createUrl('store/detail', array('productId' => $item->productId)); ?>" style="color: #FFFFFF; text-decoration: none;">
                <?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($item->cost)); ?>
            </a>
        </p>
    </div>
</li>
<?php endforeach; ?>