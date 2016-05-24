<?php
/* @var $this EventController */
/* @var $model Event */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Редактировать'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Мероприятия'); ?></h1>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>