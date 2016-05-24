<?php
    /* @var $this StoreController */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    /* @var $products Product[] */
   
    $this->setPageTitle(Yii::t('application', 'Бонусный магазин'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $currentUser = $this->getUser();
    
    $cs->registerScript('products', "
        var offset = ".$this->productsLimit.";
        var limit = ".$this->productsLimit.";
        var allLoaded = false;
        var productCategoryId = '".$selectedProductCategoryId."';
 
        function loadProducts()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('store/index')."',
                type: 'GET',
                data: {'productCategoryId': productCategoryId, 'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#productsList').append(data);
                    offset = $('#productsList li').length;
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
                loadProducts();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadProducts();
            }
        });
        
        $('#productsList').on('mouseenter mouseleave', '.buyProductButton', function(event){
            if(event.type == 'mouseenter')
            {
                $(this).prev().show();
            }
            else
            {
                $(this).prev().hide();
            }
        });
        
        $('#productCategoryId').change(function(){
            productCategoryId = $(this).val();
            allLoaded = false;
            offset = 0;
            $('#productsList').empty();
            loadProducts();
        });
    ");
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'Опросы'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href=""><?php print Yii::t('application', 'Бонусный магазин'); ?></a>
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
    <div class="black_t black_select">
        <?php print CHtml::dropDownList('productCategoryId', $selectedProductCategoryId, ProductHelper::getCategoriesToList(), array('class' => 'select-field', 'style' => 'width: 300px;')); ?>
    </div>
    <ul id="productsList" class="list_mir">
        <?php print $this->renderPartial('_products_items', array('products' => $products)); ?>   
    </ul>
    <div class="clr"></div>
</div>