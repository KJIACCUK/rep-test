<?php
    /* @var $this EmployeeController */
    /* @var $model Employee */
    /* @var $type string */

    switch($type)
    {
        case 'administrators':
            $title = Yii::t('application', 'Администраторы');
            break;
        
        case 'moderators':
            $title = Yii::t('application', 'Модераторы');
            break;
        
        case 'operators':
            $title = Yii::t('application', 'Операторы');
            break;
    }
?>

<h1><?php echo TbHtml::labelTb($title, array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Сотрудники'); ?></h1>

<p>
    <?php print Yii::t('application', 'Вы можете использовать операторы сравнения 
    (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> или <b>=</b>)
    в поле фильтра в начале поискогово запроса'); ?>
</p>

<?php echo CHtml::link(Yii::t('application', 'Добавить'), $this->createUrl('employee/create', array('type' => $type)), array('class' => 'btn btn-success')); ?>

<?php
    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'employee-grid',
        'dataProvider' => $model->search($type),
        'filter' => $model,
        'columns' => array(
            array(
                'name' => 'employeeId',
                'htmlOptions' => array('style' => 'width: 60px'),
            ),
            array(
                'name' => 'name',
                'htmlOptions' => array('style' => 'width: 500px'),
            ),
            array(
                'name' => 'email',
                'type' => 'raw',
                'value' => 'TbHtml::link($data->email, "mailto:".$data->email)',
                'htmlOptions' => array('style' => 'width: 200px;'),
            ),
            array(
                'name' => 'login',
                'htmlOptions' => array('style' => 'width: 200px'),
            ),
            array(
                'name' => 'account.isActive',
                'type' => 'raw',
                'value' => 'CommonHelper::yesnoToGridValue($data->account->isActive)', 
                'filter' => CHtml::activeDropDownList($model->searchAccount, 'isActive', CommonHelper::yesnoToList()),
                'htmlOptions' => array('style' => 'width: 100px; text-align:center;'),
            ),
            array(
                'name' => 'account.dateCreated',
                'filter' => CHtml::activeTextField($model->searchAccount, 'dateCreated'),
                'value' => 'date(Yii::app()->params["dateFormat"], $data->account->dateCreated)',
                'htmlOptions' => array('style' => 'width: 130px; text-align:center;'),
            ),
            array(
                'class' => 'bootstrap.widgets.TbButtonColumn',
                'template' => '{update} {delete}',
                'buttons' => array(
                    'update' => array(
                        'visible' => '$data->accountId != Yii::app()->user->getId()'
                    ),
                    'delete' => array(
                        'visible' => '$data->accountId != Yii::app()->user->getId()'
                    )
                )
            ),
        ),
    ));
?>