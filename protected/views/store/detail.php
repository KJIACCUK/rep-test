<?php
    /* @var $this StoreController */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    /* @var $product Product */
   
    $this->setPageTitle(Yii::t('application', 'Бонусный магазин'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $currentUser = $this->getUser();
    
    $cs->registerScriptFile('/js/jquery.fancybox.js');
    $cs->registerCssFile('/css/jquery.fancybox.css');
    
    
    $cs->registerScript('fancybox', '
        $(".fancybox").fancybox();
    ');
    
    $cs->registerScript('detail', "
        $('.bonus_mag').on('mouseenter mouseleave', '.buyProductButton', function(event){
            if(event.type == 'mouseenter')
            {
                $(this).next().show();
            }
            else
            {
                $(this).next().hide();
            }
        });
    ");
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'Опросы'); ?></a>
    </li>
    <li class="act">
        <a href="<?php print $this->createUrl('store/index'); ?>"><?php print Yii::t('application', 'Бонусный магазин'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('store/history'); ?>"><?php print Yii::t('application', 'Заказанные сувениры'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('promo/index'); ?>"><?php print Yii::t('application', 'Ввести промо-код'); ?></a>
    </li>
</ul>
<div class="bonus_mag">
    <div class="bonus_lich">
        <span class="ico_frend" style="background-image:url(<?php print CommonHelper::getImageLink($currentUser->image, '82x80'); ?>);">
            <a href="<?php print $this->createUrl('user/index'); ?>"></a>
        </span>
        <?php $this->widget('application.widgets.StorePointsHelpButton'); ?>
        <div class="inf_frend">
            <p><?php print CHtml::encode($currentUser->name); ?> <span>(<?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($currentUser->points)); ?>)</span></p>
            <p class="frend_age"><?php print UserHelper::getAge(date(Yii::app()->params['dateFormat'], $currentUser->birthday)); ?></p>
        </div>
    </div>
    <div class="line_reg"></div>
    <div class="bonus_timg">
        <img src="<?php print CommonHelper::getImageLink($product->image, '340x250'); ?>" alt=""/>
        <?php if(count($product->images)): ?>
        <ul>
            <?php foreach($product->images as $image): ?>
            <li><a class="fancybox" rel="product_<?php print $product->productId; ?>_images" href="<?php print $image->image; ?>"><img src="<?php print CommonHelper::getImageLink($image->image, '110x80'); ?>" alt=""/></a></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
    <div class="bonus_t_text">
        <div class="kratk_inf">
            <?php if($product->publisherName): ?>
            <div class="mest_mir">
                <p class="lf_f">Аврора</p>
            </div>
            <?php endif; ?>
            
            <div class="name_mir">
                <span><?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($product->cost)); ?></span>
                <p><?php print CHtml::encode($product->name); ?></p>
            </div>
            
            <div class="date_m">
                <div class="lf_f">
                    <?php if($product->dateStart): ?>
                    <?php print date(Yii::app()->params['dateTimeFormat'], $product->dateStart); ?>
                    <?php endif; ?>
                </div>
                <div class="rg_f"><?php print Yii::t('application', 'Категория'); ?>: <span><?php print CHtml::encode($product->category->name); ?></span></div>
            </div>
        </div>
        <p><?php print CHtml::encode($product->description); ?></p>
        
        <?php if($currentUser->points >= $product->cost): ?>
            <?php if(ProductHelper::canPurchaseByTimeout($currentUser->userId)): ?>
            <button onclick="window.location.href = '<?php print $this->createUrl('store/ordering', array('productId' => $product->productId)); ?>'" class="but_light but_mir" type="button"><?php print Yii::t('application', 'Купить'); ?></button>
            <?php else: ?>
            <button class="but_blue but_mir buyProductButton" type="button"><?php print Yii::t('application', 'Купить'); ?></button>
            <div style="display: none;" class="wind_cl wind_cl_detail">
                <div class="ico_uks"></div>
                <p><?php print Yii::t('application', 'Совершать покупки можно раз в 30 дней.'); ?>
                </p>
            </div>
            <?php endif; ?>
        <?php else: ?>
        
        <button class="but_blue but_mir buyProductButton" type="button"><?php print Yii::t('application', 'Купить'); ?></button>
        <div style="display: none;" class="wind_cl wind_cl_detail">
            <div class="ico_uks"></div>
            <p><?php print Yii::t('application', 'У вас недостаточно баллов для покупки.'); ?>
            </p>
        </div>
        <?php endif; ?>
        <a href="<?php print $this->createUrl('store/index', array('productCategoryId' => $product->productCategoryId)); ?>" class="back_l"><?php print Yii::t('application', 'Назад к списку бонусного магазина'); ?></a>
    </div>
    <div class="line_reg"></div>
</div>
