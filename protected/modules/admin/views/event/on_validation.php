<?php
    /* @var $this EventController */
    /* @var $model Event */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'На проверке'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Мероприятия'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> или <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'event-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'eventId',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'header' => Yii::t('application', 'Пользователь'),
                'name' => 'publisherName',
                'htmlOptions' => array('style' => 'width: 200px'),
            ),
            array(
                'header' => Yii::t('application', 'Название'),
                'name' => 'name',
                'htmlOptions' => array('style' => 'width: 300px'),
            ),
            array(
                'header' => Yii::t('application', 'Категория'),
                'name' => 'category',
                'filter' => array_combine(Yii::app()->params['eventCategories'], Yii::app()->params['eventCategories']),
                'htmlOptions' => array('style' => 'width: 100px; text-align:center;'),
            ),
            array(
                'header' => Yii::t('application', 'Город'),
                'name' => 'cityObject.name',
                'filter' => CHtml::activeTextField($model->searchCityObject, 'name'),
                'htmlOptions' => array('style' => 'width: 130px; text-align:center;'),
            ),
            array(
                'name' => 'dateStart',
                'value' => 'date(Yii::app()->params["dateFormat"], $data->dateStart)',
                'htmlOptions' => array('style' => 'width: 130px; text-align:center;'),
            ),
            array(
                'header' => Yii::t('application', 'Начало'),
                'name' => 'timeStart',
                'htmlOptions' => array('style' => 'width: 80px; text-align:center;'),
            ),
            array(
                'header' => Yii::t('application', 'Создано'),
                'name' => 'dateCreated',
                'value' => 'date(Yii::app()->params["dateFormat"], $data->dateCreated)',
                'htmlOptions' => array('style' => 'width: 130px; text-align:center;'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{view} {delete}',
            )
        ),
    ));
?>