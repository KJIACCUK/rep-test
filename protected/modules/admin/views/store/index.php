<?php
    /* @var $this StoreController */
    /* @var $model Product */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Товары'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php echo CHtml::link(Yii::t('application', 'Добавить'), $this->createUrl('store/create'), array('class' => 'btn btn-success')); ?>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'product-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'productId',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'name' => 'articleCode',
                'htmlOptions' => array('style' => 'width: 100px'),
            ),
            array(
                'name' => 'name',
                'htmlOptions' => array('style' => 'width: 400px'),
            ),
            array(
                'name' => 'productCategoryId',
                'value' => '$data->category->name', 
                'filter' => CHtml::listData(ProductHelper::categoriesToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 300px'),
            ),
            array(
                'name' => 'cost',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'name' => 'itemsCount',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'name' => 'type',
                'value' => 'ProductHelper::typeGridValue($data->type)', 
                'filter' => CHtml::listData(ProductHelper::typesToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 200px'),
            ),
            array(
                'name' => 'isActive',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->isActive)', 
                'filter' => CHtml::listData(CommonHelper::yesnoToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 80px; text-align:center;'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{gallery} {update} {delete}',
                'buttons' => array(
                    'gallery' => array(
                        'label' => Yii::t('application', 'Галлерея'),
                        'icon' => 'picture',
                        'url' => 'Yii::app()->createUrl("admin/store/gallery", array("id"=>$data->productId))',
                    )
                )
            ),
        ),
    ));
?>