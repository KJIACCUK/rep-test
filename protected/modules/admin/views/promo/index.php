<?php
    /* @var $this MarketingResearchController */
    /* @var $model MarketingResearch */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Промо-коды'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<div>
    <?php echo CHtml::link(Yii::t('application', 'Импорт'), $this->createUrl('promo/import'), array('class' => 'btn btn-success pull-left')); ?>
    <?php echo CHtml::link(Yii::t('application', 'Экспорт'), $this->createUrl('promo/export'), array('class' => 'btn btn-primary pull-right')); ?>
    <div class="clearfix"></div>
</div>


<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'promo-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'code',
                'htmlOptions' => array('style' => 'width: 100px'),
            ),
            array(
                'name' => 'status',
                'type' => 'raw',
                'value' => 'PromoHelper::statusToGridValue($data->status)', 
                'filter' => CHtml::listData(PromoHelper::statusGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 90px; text-align:left;'),
            ),
            array(
                'name' => 'userId',
                'type' => 'raw',
                'value' => 'PromoHelper::userToGridValue($data->userId)', 
                'filter' => false,
                'htmlOptions' => array('style' => 'width: 400px; text-align:left;'),
            ),
            array(
                'name' => 'pointsActivated',
                'value' => '$data->pointsActivated?$data->pointsActivated:""',
                'header' => Yii::t('application', 'Баллов'),
                'htmlOptions' => array('style' => 'width: 50px; text-align:center;'),
            ),
            array(
                'name' => 'dateActivated',
                'value' => '$data->dateActivated?date(Yii::app()->params["dateTimeFormat"], $data->dateActivated):""',
                'htmlOptions' => array('style' => 'width: 120px; text-align:center;'),
            ),
            array(
                'name' => 'dateCreated',
                'value' => 'date(Yii::app()->params["dateTimeFormat"], $data->dateCreated)',
                'htmlOptions' => array('style' => 'width: 120px; text-align:center;'),
            ),
        ),
    ));
?>