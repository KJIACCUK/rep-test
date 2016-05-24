<?php
    /* @var $this MarketingResearchController */
    /* @var $data MarketingResearch */

    $stats = MarketingResearchHelper::exportVariantsStats($data);
    $types = MarketingResearchHelper::typesToDropDown();
?>

<div class="view" style="border: 1px solid #0066A4; border-radius: 3px; padding: 10px; margin-bottom: 10px;">
    <div class="stats-small">
        <div class="pull-left" style="width: 60px;">
            # <?php print $data->marketingResearchId; ?>
        </div>

        <div class="pull-left" style="width: 100px;">
            <?php print $types[$data->type]; ?>
        </div>

        <div class="pull-left" style="width: 850px;">
            <?php print CHtml::encode($data->name); ?>
        </div>
        
        <?php if($data->type == MarketingResearch::TYPE_CUSTOM_TEXT): ?>
        <?php print TbHtml::linkButton(Yii::t('application', 'Ответы'), array('url' => $this->createUrl('textAnswers', array('id' => $data->marketingResearchId)), 'class' => 'pull-right', 'color' => TbHtml::BUTTON_COLOR_INFO, 'icon' => TbHtml::ICON_COMMENT)); ?>
        <?php else: ?>
        <?php print TbHtml::linkButton(Yii::t('application', 'Детали'), array('url' => '#', 'class' => 'btnStatsDetails pull-right', 'color' => TbHtml::BUTTON_COLOR_INFO, 'icon' => TbHtml::ICON_CHEVRON_DOWN)); ?>
        
        <?php endif; ?>
        
        <div class="clearfix"></div>
    </div>
    <div class="stats-detail" style="display: none;">
        <?php if(in_array($data->type, array(MarketingResearch::TYPE_CHECKBOX, MarketingResearch::TYPE_RADIO))): ?>
            <?php foreach($stats['stats'] as $item): ?>
                <div style="margin-bottom: 5px;"><?php print CHtml::encode($item['variant']); ?></div>
                <?php echo TbHtml::progressBar($item['percents'], array('content' => $item['percents'].'%')); ?>
            <?php endforeach; ?>
                <div>
                    <h4><?php print Yii::t('application', 'Всего ответили:'); ?> <?php print TbHtml::labelTb($stats['totalUsers'], array('color' => TbHtml::LABEL_COLOR_INFO)); ?></h4>
                </div>
        <?php endif; ?>
    </div>


</div>