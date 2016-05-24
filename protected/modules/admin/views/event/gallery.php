<?php
    /* @var $this EventController */
    /* @var $albums EventGalleryAlbum[] */
    /* @var $albumModel EventGalleryAlbum */
    /* @var $images EventGalleryImage[] */
    /* @var $cs CClientScript */
    
    $cs = Yii::app()->clientScript;

    $albumsList = array();

    foreach($albums as $item)
    {
        $albumsList[] = array(
            'label' => $item->name,
            'url' => $item->isDefault?$this->createUrl('gallery', array('id' => $eventId)):$this->createUrl('gallery', array('id' => $eventId, 'album' => $item->eventGalleryAlbumId)),
            'active' => ($albumModel->eventGalleryAlbumId == $item->eventGalleryAlbumId),
            'icon' => ($albumModel->eventGalleryAlbumId == $item->eventGalleryAlbumId)?TbHtml::ICON_FOLDER_OPEN:TbHtml::ICON_FOLDER_CLOSE
        );
    }

    $albumsList[] = array(
        'label' => Yii::t('application', 'Добавить'),
        'url' => $this->createUrl('albumCreate', array('id' => $eventId)),
        'active' => false,
        'icon' => TbHtml::ICON_PLUS
    );
    
    $imagesList = array();

    foreach($images as $item)
    {
        $imagesList[] = array(
            'image' => CommonHelper::getImageLink($item->image, '300x200'),
            'url' => $item->image,
            'caption' => TbHtml::button(Yii::t('application', 'Удалить'), array('rel' => $item->eventGalleryImageId, 'color' => TbHtml::BUTTON_COLOR_DANGER, 'class' => 'btnDeleteImage'))
        );
    }
    
    $cs->registerScriptFile('/js/jquery.fancybox.js');
    $cs->registerCssFile('/css/jquery.fancybox.css');
    
    $cs->registerScript('event_gallery', "
        
        $('.thumbnail').fancybox();

        $('#btnDeleteAlbum').click(function(){
            return confirm('".Yii::t('application', 'Удалить альбом?')."');
        });

        $('.btnDeleteImage').click(function(){
            if(confirm('".Yii::t('application', 'Удалить фотографию?')."'))
            {
                window.location.href = '".$this->createUrl('imageDelete')."?id='+$(this).attr('rel');
            }
            return false;
        });
    ");
?>

<h1><?php print TbHtml::labelTb(Yii::t('application', 'Галлерея'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Мероприятия'); ?></h1>

<h3><?php print Yii::t('application', 'Альбомы'); ?></h3>

<?php print TbHtml::pills($albumsList);?>

<div class="well well-small form">
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'album-form',
            'enableAjaxValidation' => false
        ));
    ?>

    <?php print $form->errorSummary($albumModel); ?>
    <?php /* @var $form TbActiveForm */ ?>
    
    <?php echo $form->textFieldControlGroup($albumModel, 'name', array('span' => 8, 'maxlength' => 255)); ?>
    
    <div>
        <?php
            echo TbHtml::submitButton(Yii::t('application', 'Переименовать'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY
            ));
        ?>
        <?php if(!$albumModel->isDefault): ?>
            <?php
                echo TbHtml::linkButton(Yii::t('application', 'Удалить'), array(
                    'id' => 'btnDeleteAlbum',
                    'color' => TbHtml::BUTTON_COLOR_DANGER,
                    'url' => $this->createUrl('albumDelete', array('id' => $albumModel->eventGalleryAlbumId))
                ));
            ?>
        <?php endif; ?>
    </div>
    
    <?php $this->endWidget(); ?>
</div>

<h3><?php print Yii::t('application', 'Фотографии'); ?></h3>

<?php print TbHtml::thumbnails($imagesList); ?>

<div class="well well-small form">
    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'album-form',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
    ?>

    <?php print $form->errorSummary($modelsWithErrors); ?>
    <?php /* @var $form TbActiveForm */ ?>
    
    <?php echo TbHtml::fileFieldControlGroup('imageFiles[]', null, array('multiple' => true)); ?>
    <?php print TbHtml::hiddenField('uploadImages', 1); ?>
    
    <div style="margin-top: 20px;">
        <?php
            echo TbHtml::submitButton(Yii::t('application', 'Загрузить'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY
            ));
        ?>
    </div>
    
    <?php $this->endWidget(); ?>
</div>