<?php
    /* @var $this MarketingResearchController */
    /* @var $cs CClientScript */
    /* @var $researches MarketingResearch[] */
   
    $this->setPageTitle(Yii::t('application', 'Опросы'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('researches', "
        var offset = ".$this->researchesLimit.";
        var limit = ".$this->researchesLimit.";
        var allLoaded = false;
 
        function loadResearches()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('marketingResearch/list')."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#researchesList').append(data);
                    offset = $('#researchesList li').length;
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
                loadResearches();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadResearches();
            }
        });
    ");
    
?>
<ul class="navi_menu">
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Опросы'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('store/index'); ?>"><?php print Yii::t('application', 'Бонусный магазин'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('store/history'); ?>"><?php print Yii::t('application', 'Заказанные сувениры'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('promo/index'); ?>"><?php print Yii::t('application', 'Ввести промо-код'); ?></a>
    </li>
</ul>
<div class="opros">
    <div class="title_l top_pad">
        <?php print Yii::t('application', 'Список вопросов'); ?>
    </div>
    <ul id="researchesList" class="list_opros">
        <?php print $this->renderPartial('_researches_items', array('researches' => $researches)); ?>
    </ul>
    <div class="clr"></div>
</div>