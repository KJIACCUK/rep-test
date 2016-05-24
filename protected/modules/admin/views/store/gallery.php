<?php
    /* @var $this StoreController */
    /* @var $productImages ProductImage[] */
    /* @var $model ProductImage */
    /* @var $cs CClientScript */

    $cs = Yii::app()->clientScript;
    
    $images = array();

    foreach($productImages as $item)
    {
        $images[] = array(
            'image' => CommonHelper::getImageLink($item->image, '300x200'),
            'url' => $item->image,
            'caption' => TbHtml::button(Yii::t('application', 'Удалить'), array('rel' => $item->productImageId, 'color' => TbHtml::BUTTON_COLOR_DANGER, 'class' => 'btnDeleteImage'))
        );
    }

    $cs->registerScriptFile('/js/jquery.fancybox.js');
    $cs->registerCssFile('/css/jquery.fancybox.css');
    $cs->registerScript('fancybox', '
        $(".thumbnail").fancybox();
    ');
    
    $cs->registerScript('product_gallery', '
        $(".btnDeleteImage").click(function(){
            if(confirm("'.Yii::t('application', 'Удалить фотографию?').'"))
            {
                window.location.href = "'.$this->createUrl('deleteImage').'?id="+$(this).attr("rel");
            }
            return false;
        });
    ');   
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Фотографии товара'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<div class="well well-small">
    <h3><?php print Yii::t('application', 'Добавить фотографию'); ?></h3>
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'product-image-form',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
            'layout' => TbHtml::FORM_LAYOUT_INLINE
        ));
    ?>
    <?php print $form->errorSummary($model); ?>
    <?php /* @var $form TbActiveForm */ ?>

    <?php print $form->fileField($model, 'imageFile', array('span' => 8)); ?>
    <?php print TbHtml::submitButton(Yii::t('application', 'Добавить'), array('color' => TbHtml::BUTTON_COLOR_SUCCESS)); ?>
    <?php $this->endWidget(); ?>
</div>

<?php print TbHtml::thumbnails($images); ?>