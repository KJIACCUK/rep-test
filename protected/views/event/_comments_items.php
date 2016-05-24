<?php
    /* @var $this EventController */
    /* @var $comments array */
    
    $currentUser = $this->getUser();
?>
<?php foreach($comments as $item): ?>
<li>
    <span class="ico_frend" style="background-image:url(<?php print $item['image']; ?>);">
        <?php if($item['userId'] == $currentUser->userId): ?>
        <a href="<?php print $this->createUrl('user/index'); ?>"></a>
        <?php else: ?>
        <a href="<?php print $this->createUrl('user/detail', array('userId' => $item['userId'])); ?>"></a>
        <?php endif; ?>
    </span>
    <div class="text_com">
        <div class="name_com"><?php print CHtml::encode($item['publisherName']); ?></div>
        <p><?php print CHtml::encode($item['content']); ?></p>
        <div class="date"><?php print$item['dateCreated']; ?></div>
    </div>
</li>
<?php endforeach; ?>
