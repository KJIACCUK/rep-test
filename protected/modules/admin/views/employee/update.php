<?php
    /* @var $this EmployeeController */
    /* @var $model Employee */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Редактировать'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Сотрудники'); ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>