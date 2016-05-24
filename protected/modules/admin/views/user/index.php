<?php
    /* @var $this UserController */
    /* @var $model User */
?>

<h1><?php print Yii::t('application', 'Пользователи'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>or <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'user-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'userId',
                'htmlOptions' => array('style' => 'width: 80px'),
            ),
            array(
                'name' => 'name',
                'htmlOptions' => array('style' => 'width: 400px'),
            ),
            array(
                'name' => 'email',
                'htmlOptions' => array('style' => 'width: 200px'),
            ),
            array(
                'name' => 'login',
                'htmlOptions' => array('style' => 'width: 200px'),
            ),
            array(
                'header' => Yii::t('application', 'Заполнен'),
                'name' => 'isFilled',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->isFilled)', 
                'filter' => CHtml::listData(CommonHelper::yesnoToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 100px; text-align:center;'),
            ),
            array(
                'header' => Yii::t('application', 'Верифицирован'),
                'name' => 'isVerified',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->isVerified)', 
                'filter' => CHtml::listData(CommonHelper::yesnoToGridList(), 'id', 'title'),
                'htmlOptions' => array('style' => 'width: 120px; text-align:center;'),
            ),
            array(
                'name' => 'account.isActive',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->account->isActive)', 
                'filter' => CHtml::activeDropDownList($model->searchAccount, 'isActive', CommonHelper::yesnoToList()),
                'htmlOptions' => array('style' => 'width: 100px; text-align:center;'),
            ),
            array(
                'header' => Yii::t('application', 'Зарегистрирован'),
                'name' => 'account.dateCreated',
                'filter' => CHtml::activeTextField($model->searchAccount, 'dateCreated'),
                'value' => 'date(Yii::app()->params["dateFormat"], $data->account->dateCreated)',
                'htmlOptions' => array('style' => 'width: 130px; text-align:center;'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{view} {delete} {history}',
                'buttons' => array(
                  'history' => array(
                    'label' => Yii::t('application', 'История пользователя'),
                    'icon' => 'time',
                    'url' => 'Yii::app()->createUrl("admin/users/history/{$data->userId}")',
                  )
                )
            ),
        ),
    ));
?>