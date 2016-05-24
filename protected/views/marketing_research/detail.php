<?php
    /* @var $this MarketingResearchController */
    /* @var $cs CClientScript */
    /* @var $research MarketingResearch */
    /* @var $nextResearch MarketingResearch */
    /* @var $model MarketingResearchForm */
    /* @var $stats arrays */
   
    $this->setPageTitle(Yii::t('application', 'Опрос'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('fb_async_init2', "
        FB.Canvas.setSize({height: $('body').height()});
    ", CClientScript::POS_LOAD);
    
?>
<ul class="navi_menu">
    <li class="act">
        <a href="<?php print $this->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'Опросы'); ?></a>
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
<div class="vopros">
    <div class="main-content" style="padding: 20px 20px 0;">
        <?php print $research->content; ?>
    </div>
    <div class="vopros_t"><p><?php print CHtml::encode($research->name); ?></p></div>
    <?php if($research->isAnswered): ?>
    
        <?php if($research->type == MarketingResearch::TYPE_CUSTOM_TEXT): ?>
    
        <p><?php print CHtml::encode($stats['answerText']); ?></p>
        
        <?php else: ?>
            <ul class="list_st">
                <?php foreach($stats['stats'] as $item): ?>
                <li>
                    <p><?php print CHtml::encode($item['variant']); ?></p>
                    <div class="numer_st"><?php print $item['percents']; ?>%</div>
                    <div class="num_stat">
                        <div style="width:<?php print $item['percents']; ?>%;"></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <div class="clr"></div>
        
    <?php else: ?>
    
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'marketing-research',
            'enableAjaxValidation' => false
        )); ?>
    
        <?php /* @var $form CActiveForm */ ?>
    
        <?php print $form->errorSummary($model); ?>
    
        <?php if($research->type == MarketingResearch::TYPE_CUSTOM_TEXT): ?>
            <div class="dost_coment">
                <?php print $form->textArea($model, 'answerText', array('rows' => 5, 'cols' => 50)); ?>
                <button class="but_light" type="submit"><?php print Yii::t('application', 'Ответить'); ?></button>
            </div>
        <?php elseif($research->type == MarketingResearch::TYPE_CHECKBOX): ?>
        
            <?php print $form->checkBoxList($model,
                    'answerVariants', 
                    MarketingResearchHelper::variantsToList($research->variants), 
                    array('class' => 'checkbox-field',
                        'template' => '<div class="research-element-wrap">{input} {label}<div class="clr"></div></div>',
                        'separator' => ''
                    )
                ); 
            ?>

            <div class="dost_coment">
                <button class="but_light" type="submit"><?php print Yii::t('application', 'Ответить'); ?></button>
            </div>
                
        <?php elseif($research->type == MarketingResearch::TYPE_RADIO): ?>
                
            <?php print $form->radioButtonList($model,
                    'answerVariants',
                    MarketingResearchHelper::variantsToList($research->variants),
                    array('class' => 'checkbox-field',
                        'template' => '<div class="research-element-wrap">{input} {label}<div class="clr"></div></div>',
                        'separator' => ''
                    )
                ); 
            ?>
    
            <div class="dost_coment">
                <button class="but_light" type="submit"><?php print Yii::t('application', 'Ответить'); ?></button>
            </div>
                
        <?php endif; ?>
        
        <div class="clr"></div>
        <?php $this->endWidget(); ?>
                
    <?php endif; ?>
        
    <?php if($nextResearch): ?>
        <div style="margin-top: -40px;overflow: hidden;">
            <button onclick="window.location.href = '<?php print $this->createUrl('marketingResearch/detail', array('marketingResearchId' => $nextResearch->marketingResearchId)); ?>'" class="but_light" style="float: right;" type="button"><?php print Yii::t('application', 'Следующий вопрос'); ?></button>
            <button onclick="window.location.href = '<?php print $this->createUrl('marketingResearch/list'); ?>'" class="but_blue" style="float: right; margin-right: 10px;" type="button"><?php print Yii::t('application', ' Все вопросы'); ?></button>
        </div>
    <?php endif; ?>
</div>