<?php
    /* @var $this StoreController */
    /* @var $model Product */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Добавить товар'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>


<?php $this->renderPartial('_form', array('model'=>$model)); ?>