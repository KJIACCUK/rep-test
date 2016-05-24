<?php
    /* @var $this EventController */
    /* @var $users array */
    
    $currentUser = $this->getUser();
?>
<?php foreach($users as $user): ?>
    <?php if($user['userId'] == $currentUser->userId): ?>
        <li>
            <div class="ico_online<?php print $user['isOnline']?' act':''; ?>"></div>

            <span class="ico_frend" style="background-image:url(<?php print $user['image']; ?>);">
                <a href="<?php print $this->createUrl('user/index'); ?>"></a>
            </span>

            <div class="inf_frend">
                <p><?php print CHtml::encode($user['name']); ?></p>
                <p class="frend_age"><?php print UserHelper::getAge($user['birthday']); ?></p>
            </div>
        </li>
    <?php else: ?>
        <li>
            <div class="ico_online<?php print $user['isOnline']?' act':''; ?>"></div>

            <span class="ico_frend" style="background-image:url(<?php print $user['image']; ?>);">
                <a href="<?php print $this->createUrl('user/detail', array('userId' => $user['userId'])); ?>"></a>
            </span>


            <?php if(!$user['isFriend']): ?>
                <?php if(!$user['friendshipRequest']): ?>
                <button id="invite_user_<?php print $user['userId']; ?>" class="but_light add_frend" type="submit"><?php print Yii::t('application', '+1 Добавить'); ?></button>
                <?php else: ?>
                <div class="add_frend_invited"><?php print Yii::t('application', 'Отправлено'); ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="inf_frend">
                <p><?php print CHtml::encode($user['name']); ?></p>
                <p class="frend_age"><?php print UserHelper::getAge($user['birthday']); ?></p>
            </div>
        </li>
    <?php endif; ?>
<?php endforeach; ?>