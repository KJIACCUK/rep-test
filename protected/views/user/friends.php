<?php
    /* @var $this UserController */
    /* @var $user array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Друзья'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
?>
<ul class="navi_menu">
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Друзья'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('user/search'); ?>"><?php print Yii::t('application', 'Найти друзей'); ?></a>
    </li>
    <li>
        <?php $this->widget('application.widgets.UserInviteSocialButton'); ?>
    </li>
</ul>
<?php if(count($friends)): ?>
    <ul class="list_frend">
        <?php foreach($friends as $user): ?>
            <li>
                <div class="ico_online<?php print $user['isOnline']?' act':''; ?>"></div>
                <span class="ico_frend" style="background-image:url(<?php print $user['image']; ?>);">
                    <a href="<?php print $this->createUrl('user/detail', array('userId' => $user['userId'])); ?>"></a>
                </span>
                <a href="<?php print $this->createUrl('chat/dialog', array('userId' => $user['userId'])); ?>" class="mess_frend"></a>
                <div class="inf_frend">
                    <p><?php print CHtml::encode($user['name']); ?></p>
                    <p class="frend_age"><?php print UserHelper::getAge($user['birthday']); ?></p>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?php print Yii::t('application', 'Вы еще никого не добавили в друзья'); ?></p>
<?php endif; ?>
