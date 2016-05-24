<?php
/* @var $this EventController */
/* @var $model Event */
/* @var $cs CClientScript */

$cs = Yii::app()->clientScript;

$cs->registerScriptFile('/js/jquery.fancybox.js');
$cs->registerCssFile('/css/jquery.fancybox.css');

$cs->registerScript('event_view', "
        $('.thumbnail').fancybox();
    ");

if ($model->productId) {
    $product = Product::model()->findByPk($model->productId);
}

$author = '';
if ($model->userId) {
    $author = TbHtml::link($model->publisherName, $this->createUrl('user/view', array('id' => $model->userId)));
} else {
    $author = CHtml::encode($model->publisherName);
}

if ($model->relaxId) {
    $relax = TbHtml::labelTb(Yii::t('application', ' Да'), array('color' => TbHtml::LABEL_COLOR_SUCCESS)).TbHtml::link(Yii::t('application', ' (открыть оригинал в новом окне)'), $model->relaxUrl, array('target' => '_blank'));
} else {
    $relax = TbHtml::labelTb(Yii::t('application', ' Нет'), array('color' => TbHtml::LABEL_COLOR_IMPORTANT));
}

$status = '';
$statuses = EventHelper::getStatusesList();
switch ($model->status) {
    case Event::STATUS_APPROVED:
        $status = TbHtml::labelTb($statuses[$model->status], array('color' => TbHtml::LABEL_COLOR_SUCCESS));
        break;

    case Event::STATUS_WAITING:
        $status = TbHtml::labelTb($statuses[$model->status], array('color' => TbHtml::LABEL_COLOR_WARNING));
        if ($model->relaxId) {
            $status .= ' '.TbHtml::linkButton(Yii::t('application', 'Опубликовать'), array('url' => $this->createUrl('publish', array('id' => $model->eventId)), 'color' => TbHtml::BUTTON_COLOR_SUCCESS));
        } else {
            $status .= ' '.TbHtml::linkButton(Yii::t('application', 'Разрешить'), array('url' => $this->createUrl('approve', array('id' => $model->eventId)), 'color' => TbHtml::BUTTON_COLOR_SUCCESS)).' '
            .TbHtml::linkButton(Yii::t('application', 'Запретить'), array('url' => $this->createUrl('decline', array('id' => $model->eventId)), 'color' => TbHtml::BUTTON_COLOR_DANGER));
        }
        break;

    case Event::STATUS_DECLINED:
        $status = TbHtml::labelTb($statuses[$model->status], array('color' => TbHtml::LABEL_COLOR_IMPORTANT));
        break;
}

$address = $model->cityObject->name;
if ($model->street) {
    $address .= ', '.$model->street;
}

if ($model->houseNumber) {
    $address .= ' '.$model->houseNumber;
}

if ($model->latitude && $model->longitude) {
    $address .= '   ('.TbHtml::link(Yii::t('application', 'Посмотреть на карте'), 'http://maps.yandex.ru/?text='.$model->latitude.','.$model->longitude, array('target' => '_blank')).')';
}

$relaxErrorString = Yii::t('application', 'Нет');
if ($model->relaxParsingErrors) {
    $relaxErrorsData = EventHelper::getRelaxErrors($model->relaxParsingErrors);
    foreach ($relaxErrorsData as $label => $error) {
        $relaxErrorString .= '<p>'.TbHtml::labelTb($label, array('color' => TbHtml::LABEL_COLOR_DEFAULT)).' - '.$error.'</p>';
    }
    $relaxErrorString = TbHtml::alert(TbHtml::ALERT_COLOR_ERROR, $relaxErrorString, array('closeText' => false));
}
?>

<h1><?php print TbHtml::labelTb(Yii::t('application', 'Просмотр'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Мероприятия'); ?></h1>
<h3><?php print Yii::t('application', 'Мероприятие'); ?></h3>
<?php
$this->widget('zii.widgets.CDetailView', array(
    'htmlOptions' => array(
        'class' => 'table table-striped table-condensed table-hover',
    ),
    'data' => $model,
    'attributes' => array(
        'eventId',
        array(
            'label' => Yii::t('application', 'Взято с Relax.by'),
            'type' => 'raw',
            'value' => $relax
        ),
        'category',
        array(
            'label' => Yii::t('application', 'Автор'),
            'type' => 'raw',
            'value' => $author
        ),
        'name',
        array(
            'name' => 'image',
            'type' => 'raw',
            'value' => TbHtml::imagePolaroid(CommonHelper::getImageLink($model->image, '82x80'))
        ),
        'description',
        array(
            'label' => Yii::t('application', 'Адрес'),
            'type' => 'raw',
            'value' => $address
        ),
        array(
            'name' => 'isPublic',
            'type' => 'raw',
            'value' => CommonHelper::yesnoToGridValue($model->isPublic)
        ),
        array(
            'name' => 'isGlobal',
            'type' => 'raw',
            'value' => CommonHelper::yesnoToGridValue($model->isGlobal)
        ),
        array(
            'name' => 'productId',
            'value' => $model->productId?$product->name:''
        ),
        array(
            'name' => 'status',
            'type' => 'raw',
            'value' => $status
        ),
        array(
            'label' => Yii::t('application', 'Дата и время проведения'),
            'type' => 'raw',
            'value' => date(Yii::app()->params['dateFormat'], $model->dateStart).' '.$model->timeStart.($model->timeEnd?' - '.$model->timeEnd:'')
        ),
        array(
            'name' => 'dateCreated',
            'value' => date(Yii::app()->params['dateTimeFormat'], $model->dateCreated)
        ),
        array(
            'label' => Yii::t('application', 'Ошибки парсинга'),
            'type' => 'raw',
            'value' => $relaxErrorString
        )
    ),
));
?>
<h3><?php print Yii::t('application', 'Галлерея'); ?></h3>
<?php foreach ($model->galleryAlbums as $album): ?>
    <h5><?php print CHtml::encode($album->name); ?></h5>
    <?php
    $images = array();
    foreach ($album->images as $item) {
        $images[] = array(
            'image' => CommonHelper::getImageLink($item->image, '300x200'),
            'url' => $item->image
        );
    }
    ?>
    <?php print TbHtml::thumbnails($images); ?>
<?php endforeach; ?>