<?php
    /* @var $this MarketingResearchController */
    /* @var $dataProvider CActiveDataProvider */
    /* @var $model MarketingResearch */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Ответы'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Маркетинговые исследования'); ?></h1>

<h3><?php print CHtml::encode($model->name); ?></h3>
<?php
    $this->widget('bootstrap.widgets.TbListView', array(
        'dataProvider' => $dataProvider,
        'itemView' => '_answers_view',
    ));
?>