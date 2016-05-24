<?php
/* @var $this UserController */
/* @var $model User */
?>
<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Пользователь #').$model->userId, array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'История пользователя'); ?></h1>

<?php
$this->widget('bootstrap.widgets.TbGridView', array(
  'id' => 'points-grid',
  'dataProvider' => $model->history(),
  'columns' => array(
    array(
      'header' => Yii::t('application', 'Кол-во очков'),
      'name' => 'pointsCount',
      'htmlOptions' => array('style' => 'width: 80px'),
    ),
    array(
      'header' => Yii::t('application', 'Действие'),
      'name' => 'point.pointKey',
      'type' => 'raw',
      'value' => 'PointHelper::categoryNameById($data->point->pointKey)',
      'htmlOptions' => array('style' => 'width: 100px;'),
    ),
    array(
      'header' => Yii::t('application', 'Параметры'),
      'name' => 'params',
      'type' => 'raw',
      'value' => 'CommonHelper::decodeParams($data->params)',
      'htmlOptions' => array('style' => 'width: 120px'),
    ),
    array(
      'header' => Yii::t('application', 'Дата события'),
      'name' => 'dateCreated',
      'value' => 'date(Yii::app()->params["dateFormat"], $data->dateCreated)',
      'htmlOptions' => array('style' => 'width: 130px;'),
    ),
  ),
));
?>

<h3><?php print Yii::t('application', 'Добавить баллы'); ?> (сейчас <?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($userPoints)); ?>)</h3>
<div class="well well-small form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
      'id' => 'add-points-form',
      'layout' => TbHtml::FORM_LAYOUT_INLINE,
      'enableAjaxValidation' => false,
      'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>

    <?php print $form->errorSummary($pointsetter); ?>
    <?php /* @var $form TbActiveForm */ ?>

    <label>Число баллов</label>
    <?php print $form->textField($pointsetter, 'points', array('size' => TbHtml::INPUT_SIZE_MINI)); ?>
    <label>Причина добавления</label>
    <?php print $form->textField($pointsetter, 'comment', array('size' => TbHtml::INPUT_SIZE_XXLARGE)); ?>

    <?php
    echo TbHtml::submitButton(Yii::t('application', 'Добавить'), array(
      'color' => TbHtml::BUTTON_COLOR_PRIMARY,
      'size' => TbHtml::BUTTON_SIZE_DEFAULT,
    ));
    ?>

    <?php $this->endWidget(); ?>

</div>
