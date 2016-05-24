<?php
    /* @var $this MarketingResearchController */
    /* @var $dataProvider CActiveDataProvider */
    /* @var $cs CClientScript */

    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('marketing_research_statistics', "
        $(document).on('click', '.btnStatsDetails', function(){
            if($(this).hasClass('opened'))
            {
                $(this).removeClass('opened');
                $(this).find('.".TbHtml::ICON_CHEVRON_UP."').removeClass('".TbHtml::ICON_CHEVRON_UP."').addClass('".TbHtml::ICON_CHEVRON_DOWN."');
                $(this).parent().parent().find('.stats-detail').hide();
            }
            else
            {
                $(this).addClass('opened');
                $(this).find('.".TbHtml::ICON_CHEVRON_DOWN."').removeClass('".TbHtml::ICON_CHEVRON_DOWN."').addClass('".TbHtml::ICON_CHEVRON_UP."');
                $(this).parent().parent().find('.stats-detail').show();
            }
            
            return false;
        });
    ");
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Статистика'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Маркетинговые исследования'); ?></h1>

<div class="view" style="padding: 10px; margin-bottom: 10px;">
    <div class="pull-left" style="width: 60px;">
        <?php print Yii::t('application', 'ID'); ?>
    </div>
    <div class="pull-left" style="width: 100px;">
        <?php print Yii::t('application', 'Тип'); ?>
    </div>

    <div class="pull-left" style="width: 850px;">
        <?php print Yii::t('application', 'Название'); ?>
    </div>

    <div class="clearfix"></div>
</div>

<?php
    $this->widget('bootstrap.widgets.TbListView', array(
        'dataProvider' => $dataProvider,
        'itemView' => '_statistics_view',
    ));
?>