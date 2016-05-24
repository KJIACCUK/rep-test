<?php
    /* @var $this MarketingResearchController */
    /* @var $model MarketingResearch */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Добавить'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Маркетинговые исследования'); ?></h1>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>