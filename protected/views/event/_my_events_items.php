<?php
    /* @var $this EventController */
    /* @var $events array */
?>
<?php foreach($events as $item): ?>
<li>
    <a class="mir_img" href="<?php print $this->createUrl('event/detail', array('eventId' => $item['eventId'])); ?>">
        <img src="<?php print $item['image']; ?>" alt=""/>
    </a>
    
    <?php if($item['status'] == Event::STATUS_WAITING): ?>
    <a href="#" class="redact_m status_waiting"></a>
    <a href="<?php print $this->createUrl('event/delete', array('eventId' => $item['eventId'])); ?>" class="del_m"></a>
    <button class="but_blue but_mir" type="submit"><?php print Yii::t('application', 'В ожидании'); ?></button>
    <?php elseif($item['status'] == Event::STATUS_APPROVED): ?>
    <a href="<?php print $this->createUrl('event/edit', array('eventId' => $item['eventId'])); ?>" class="redact_m"></a>
    <a href="<?php print $this->createUrl('event/delete', array('eventId' => $item['eventId'])); ?>" class="del_m"></a>
    <button class="but_light but_mir" type="submit"><?php print Yii::t('application', 'Подтверждено'); ?></button>
    <?php elseif($item['status'] == Event::STATUS_DECLINED): ?>
    <a href="<?php print $this->createUrl('event/edit', array('eventId' => $item['eventId'])); ?>" class="redact_m"></a>
    <a href="<?php print $this->createUrl('event/delete', array('eventId' => $item['eventId'])); ?>" class="del_m"></a>
    <button class="but_red but_mir" type="submit"><?php print Yii::t('application', 'Отклонено'); ?></button>
    <?php endif; ?>
    <div class="mir_tx">
        <p class="sm_tx"><?php print CHtml::encode($item['publisherName']); ?></p>
        <p><?php print CHtml::encode($item['name']); ?></p>
        <div class="date"><?php print $item['dateStart']; ?>, <?php print $item['timeStart']; ?></div>
        <a href="<?php print $this->createUrl('event/subscribers', array('eventId' => $item['eventId'])); ?>" class="peopl_p"><?php print Yii::t('application', 'Подписано: {count} чел.', array('{count}' => $item['subscribersCount'])); ?></a>
    </div>
</li>
<?php endforeach; ?>