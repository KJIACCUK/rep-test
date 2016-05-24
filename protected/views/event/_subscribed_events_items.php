<?php
    /* @var $this EventController */
    /* @var $events array */
    
    $actionId = $this->getAction()->getId();
?>
<?php foreach($events as $item): ?>
<li>
    <a class="mir_img" href="<?php print $this->createUrl('event/detail', array('eventId' => $item['eventId'])); ?>">
        <img src="<?php print $item['image']; ?>" alt=""/>
    </a>
    
    <?php if($actionId == 'comingEvents'): ?>
        <?php if($item['isSubscribe']): ?>
        <a id='eventSubscribe_<?php print $item['eventId']; ?>' href="#" class="but_budu act"></a>
        <?php else: ?>
        <a id='eventSubscribe_<?php print $item['eventId']; ?>' href="#" class="but_budu"></a>
        <?php endif; ?>
    <?php endif; ?>

    <div class="mir_tx">
        <p class="sm_tx"><?php print CHtml::encode($item['publisherName']); ?></p>
        <p><?php print CHtml::encode($item['name']); ?></p>
        <div class="date"><?php print $item['dateStart']; ?>, <?php print $item['timeStart']; ?></div>
        <a href="<?php print $this->createUrl('event/subscribers', array('eventId' => $item['eventId'])); ?>" class="peopl_p"><?php print Yii::t('application', 'Подписано: {count} чел.', array('{count}' => $item['subscribersCount'])); ?></a>
    </div>
</li>
<?php endforeach; ?>