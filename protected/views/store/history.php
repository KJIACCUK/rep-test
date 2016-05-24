<?php
    /* @var $this StoreController */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    /* @var $purchases ProductPurchase[] */
   
    $this->setPageTitle(Yii::t('application', 'Заказанные сувениры'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $currentUser = $this->getUser();
    
    $cs->registerScript('purchases', "
        var offset = ".$this->purchasesLimit.";
        var limit = ".$this->purchasesLimit.";
        var allLoaded = false;
 
        function loadPurchases()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('store/history')."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#purchasesList').append(data);
                    offset = $('#purchasesList li').length;
                    if(data.length == 0)
                    {
                        allLoaded = true;
                    }
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                }
            });
        }

        $(window).scroll(function()
        {
            if (document.body.scrollHeight - $(this).scrollTop()  <= $(this).height())
            {
                loadPurchases();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadPurchases();
            }
        });
    ");
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'Опросы'); ?></a>
    </li>
    <li>
        <a  href="<?php print $this->createUrl('store/index'); ?>"><?php print Yii::t('application', 'Бонусный магазин'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Заказанные сувениры'); ?></a>
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
    <ul id="purchasesList" class="list_mir suvenir">
        <?php print $this->renderPartial('_history_items', array('purchases' => $purchases)); ?>				
    </ul>
    <div class="clr"></div>
</div>