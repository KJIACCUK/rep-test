<?php
    /* @var $this ChatController */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    /* @var $recipient User */
    /* @var $messages UserMessage[] */
    
    $cs = Yii::app()->clientScript;
    $currentUser = $this->getUser();
    
?>
<?php foreach($messages as $item): ?>
    <?php if($item->userId == $currentUser->userId): ?>
        <li class="otvet_m">
            <div class="mini_ico1" style="background-image:url(<?php print CommonHelper::getImageLink($currentUser->image, '65x65'); ?>)">
                <a href="<?php print $this->createUrl('user/index'); ?>"></a>
            </div>
            <div class="text_ico1">
                <p class="messageText"><?php print CHtml::encode($item->message); ?></p>
                <div class="date"><?php print date(Yii::app()->params['dateTimeFormat'], $item->dateCreated); ?></div>
            </div>
        </li>
    <?php else: ?>
        <li>
            <div class="mini_ico1" style="background-image:url(<?php print CommonHelper::getImageLink($recipient->image, '65x65'); ?>)">
                <a href="<?php print $this->createUrl('user/detail', array('userId' => $recipient->userId)); ?>"></a>
            </div>
            <div class="text_ico1">
                <div class="ico_m"></div>
                <p class="messageText"><?php print CHtml::encode($item->message); ?></p>
                <div class="date"><?php print date(Yii::app()->params['dateTimeFormat'], $item->dateCreated); ?></div>
            </div>
        </li>
    <?php endif; ?>
<?php endforeach; ?>