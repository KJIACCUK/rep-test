<?php
    /* @var $this EventSubpageTitle */
?>
<div class="title_l top_pad event-subpage">
    <div class="event-subpage-title"><?php print $this->title; ?></div>
    
    <a href="<?php print $this->getController()->createUrl('event/detail', array('eventId' => $this->eventId)); ?>" class="read_all">
        <?php print Yii::t('application', 'Назад к мероприятию'); ?>
    </a>
    
    <div class="clr"></div>
</div>
<div class="line_reg" style="margin-bottom:10px;">&nbsp;</div>