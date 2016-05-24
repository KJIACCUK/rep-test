<?php
    /* @var $this NotificationController */
    /* @var $notifications UserNotification[] */
?>
<?php foreach($notifications as $item): ?>
<li><a href="<?php print UserNotificationsHelper::getNotificationLink($item->settingKey, CJSON::decode($item->params)); ?>"><?php print $item->notificationText; ?></a></li>
<?php endforeach; ?>