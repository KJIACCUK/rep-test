<?php
    /* @var $this EventController */
    /* @var $albums array */
    /* @var $eventId integer */
    /* @var $cs CClientScript */
    
    $cs = Yii::app()->clientScript;
    $showAlbums = count($albums) > 1;
?>
<?php if($showAlbums): ?>
<ul class="photo_mir">
<?php foreach($albums as $album): ?>
<li>
    <?php if(isset($album['images'][0])): ?>
    <a href="<?php print $this->createUrl('event/getAlbum', array('eventId' => $eventId, 'albumId' => $album['albumId'])); ?>">
        <img src="<?php print CommonHelper::getImageLink($album['images'][0]['originalImage'], '250x166'); ?>" />
        <?php print CHtml::encode($album['name']); ?>
    </a>
    <?php else: ?>
    <a href="<?php print $this->createUrl('event/getAlbum', array('eventId' => $eventId, 'albumId' => $album['albumId'])); ?>">
        <img src="<?php print EventGalleryHelper::getDefaultAlbumImage(); ?>" />
        <?php print CHtml::encode($album['name']); ?>
    </a>
    <?php endif; ?>

</li>
<?php endforeach; ?>
</ul>
<div class="clr"></div>
<?php endif; ?>