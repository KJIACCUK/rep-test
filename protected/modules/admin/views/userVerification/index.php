<?php
    /* @var $this UserVerificationController */
    /* @var $model UserVerificationRequest */

?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Заявки'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Верификация'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'user-verification-request-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'userVerificationRequestId',
                'htmlOptions' => array('style' => 'width: 80px'),
            ),
            array(
                'name' => 'user.name',
                'type' => 'raw',
                'value' => '$data->user->name',
                'filter' => CHtml::activeTextField($model->searchUser, 'name'),
                'htmlOptions' => array('style' => 'width: 400px;'),
            ),
            array(
                'header' => Yii::t('application', 'Мессенджер'),
                'name' => 'messengerLogin',
                'value' => '$data->isPhotoVerification?"":Yii::app()->params["messengers"][$data->messenger].": ".$data->messengerLogin',
                'htmlOptions' => array('style' => 'width: 300px'),
            ),
            array(
                'header' => Yii::t('application', 'Дата и Время звонка'),
                'name' => 'callDate',
                'value' => '$data->isMissed?Yii::t("application", "Пропущенный вызов"):($data->isPhotoVerification?Yii::t("application", "Верификация по фото"):date(Yii::app()->params["dateTimeFormat"], $data->callDate))',
                'htmlOptions' => array('style' => 'width: 200px'),
            ),
            array(
                'name' => 'dateCreated',
                'value' => 'date(Yii::app()->params["dateTimeFormat"], $data->dateCreated)',
                'htmlOptions' => array('style' => 'width: 120px'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{view}'
            ),
        ),
    ));
?>