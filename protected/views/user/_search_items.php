<?php
    /* @var $this UserController */
    /* @var $users array */
?>
<?php foreach($users as $user): ?>
<li>
    <div class="ico_online<?php print $user['isOnline']?' act':''; ?>"></div>
    
    <span class="ico_frend" style="background-image:url(<?php print $user['image']; ?>);">
        <a href="<?php print $this->createUrl('user/detail', array('userId' => $user['userId'])); ?>"></a>
    </span>
    
    <?php if(!$user['friendshipRequest']): ?>
    <button id="invite_user_<?php print $user['userId']; ?>" class="but_light add_frend" type="submit"><?php print Yii::t('application', '+1 Добавить'); ?></button>
    <?php else: ?>
    <div class="add_frend_invited"><?php print Yii::t('application', 'Отправлено'); ?></div>
    <?php endif; ?>
    
    <div class="inf_frend">
        <p><?php print CHtml::encode($user['name']); ?></p>
        <p class="frend_age"><?php print UserHelper::getAge($user['birthday']); ?></p>
    </div>
</li>
<?php endforeach; ?>