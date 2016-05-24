<?php
    /* @var $this MarketingResearchController */
    /* @var $model MarketingResearch */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Исследования'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Маркетинговые исследования'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php echo CHtml::link(Yii::t('application', 'Добавить'), $this->createUrl('marketingResearch/create'), array('class' => 'btn btn-success')); ?>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'marketing-research-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'marketingResearchId',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'name' => 'type',
                'value' => 'MarketingResearchHelper::typeGridValue($data->type)', 
                'filter' => CHtml::listData(MarketingResearchHelper::typesToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 120px'),
            ),
            array(
                'name' => 'name',
                'htmlOptions' => array('style' => 'width: 500px'),
            ),
            array(
                'name' => 'isEnabled',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->isEnabled)', 
                'filter' => CHtml::listData(CommonHelper::yesnoToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 100px; text-align:center;'),
            ),
            array(
                'name' => 'isPushed',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->isPushed)', 
                'filter' => CHtml::listData(CommonHelper::yesnoToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 120px; text-align:center;'),
            ),
            array(
                'name' => 'dateCreated',
                'value' => 'date(Yii::app()->params["dateFormat"], $data->dateCreated)',
                'htmlOptions' => array('style' => 'width: 120px; text-align:center;'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{update} {delete}'
            ),
        ),
    ));
?>