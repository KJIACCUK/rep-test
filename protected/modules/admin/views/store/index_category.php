<?php
    /* @var $this StoreController */
    /* @var $model ProductCategory */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Категории'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php echo CHtml::link(Yii::t('application', 'Добавить'), $this->createUrl('store/createCategory'), array('class' => 'btn btn-success')); ?>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'product-category-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'productCategoryId',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'name' => 'name',
                'htmlOptions' => array('style' => 'width: 500px'),
            ),
            array(
                'header' => Yii::t('application', 'Родительская категория'),
                'name' => 'parentCategory.name',
                'htmlOptions' => array('style' => 'width: 500px'),
            ),
            array(
                'name' => 'level',
                'htmlOptions' => array('style' => 'width: 80px'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{update} {delete}',
                'buttons' => array(
                    'update' => array(
                        'url' => 'Yii::app()->createUrl("admin/store/updateCategory", array("id"=>$data->productCategoryId))',
                    ),
                    'delete' => array(
                        'url' => 'Yii::app()->createUrl("admin/store/deleteCategory", array("id"=>$data->productCategoryId))',
                        'visible' => 'count($data->childsCategories) == 0'
                    )
                )
            ),
        ),
    ));
?>