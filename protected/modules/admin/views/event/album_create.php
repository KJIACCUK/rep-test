<?php
    /* @var $this EventController */
    /* @var $albums EventGalleryAlbum[] */
    /* @var $albumModel EventGalleryAlbum */

    $albumsList = array();

    foreach($albums as $item)
    {
        $albumsList[] = array(
            'label' => $item->name,
            'url' => $item->isDefault?$this->createUrl('gallery', array('id' => $eventId)):$this->createUrl('gallery', array('id' => $eventId, 'album' => $item->eventGalleryAlbumId)),
            'active' => false,
            'icon' => TbHtml::ICON_FOLDER_CLOSE
        );
    }

    $albumsList[] = array(
        'label' => Yii::t('application', 'Добавить'),
        'url' => $this->createUrl('albumCreate', array('id' => $eventId)),
        'active' => true,
        'icon' => TbHtml::ICON_PLUS
    );
?>

<h1><?php print TbHtml::labelTb(Yii::t('application', 'Добавить альбом'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Мероприятия'); ?></h1>

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
    
    <div class="form-actions">
        <?php
            echo TbHtml::submitButton(Yii::t('application', 'Добавить'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
        ?>
    </div>
    
    <?php $this->endWidget(); ?>
</div>