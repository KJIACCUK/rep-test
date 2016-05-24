<?php
    /* @var $this StoreController */
    /* @var $model ProductCategory */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Редактировать категорию'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>


<?php $this->renderPartial('_category_form', array('model' => $model)); ?>