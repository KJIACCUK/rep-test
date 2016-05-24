<?php
    /* @var $this UserController */
    /* @var $user array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Моя личная страница'));
    
    $cs = Yii::app()->clientScript;
?>
<div id="profile-edit" class="title_l">
    <div class="profile-edit-title"><?php print CHtml::encode(Yii::t('application', 'Моя личная страница')); ?></div>
    <div class="profile-edit-button"><a class="mode-view" href="<?php print $this->createUrl('user/edit'); ?>"><?php print CHtml::encode(Yii::t('application', 'редактировать')); ?></a></div>
    <div class="clr"></div>
</div>
<div class="line_reg"></div>

<?php $this->widget('application.widgets.UserAvatar', array('image' => $user['image'], 'saveUrl' => 'user/saveAvatar')); ?>

<div class="inf_lich">
    <div class="name_l"><?php print CHtml::encode($user['name']); ?></div>
    <div class="user-info-field" style="text-decoration: underline;"><?php print CHtml::encode($user['email']); ?></div>
    <div class="user-info-field"><?php print $user['birthday']; ?></div>
    <div class="user-info-field">+375 (<?php print $user['phoneCode']; ?>) <?php print $user['phone']; ?></div>
    
    <?php if($user['messenger'] && $user['messengerLogin']): ?>
    <div class="user-info-field"><?php print Yii::app()->params['messengers'][$user['messenger']]; ?>: <?php print CHtml::encode($user['messengerLogin']); ?></div>
    <?php endif; ?>
</div>

<div class="clr"></div>
<div class="nav_prof">
    <button class="but_light" onclick="window.location.href='<?php print $this->createUrl('user/friends'); ?>'" type="submit"><?php print Yii::t('application', 'Друзья'); ?> (<?php print $user['counters']['friends']; ?>)</button>
    <button class="but_light" onclick="window.location.href='<?php print $this->createUrl('event/comingEvents'); ?>'" type="submit"><?php print Yii::t('application', 'Мероприятия'); ?> (<?php print $user['counters']['events']; ?>)</button>
    <button class="but_blue" onclick="window.location.href='<?php print $this->createUrl('event/pastEvents'); ?>'" type="submit"><?php print Yii::t('application', 'Прошедшие'); ?> (<?php print $user['counters']['pastEvents']; ?>)</button>
</div>

<div class="clr"></div>
<div class="title_l"><?php print Yii::t('application', 'Интересы'); ?></div>			
<div class="line_reg"></div>
<div class="love_l"><?php print Yii::t('application', 'Любимая музыка'); ?>:</div>
<?php if($user['favoriteMusicGenre']): ?>
<div class="user-info-field2" style="width: 555px;"><?php print CHtml::encode($user['favoriteMusicGenre']); ?></div>
<?php else: ?>
<div class="user-info-field2" style="width: 555px;"><em><?php print Yii::t('application', 'не указано'); ?></em></div>
<?php endif; ?>
<div class="clr"></div>
<div class="love_l"><?php print Yii::t('application', 'Любимый табачный бренд'); ?>:</div>
<?php if($user['favoriteCigaretteBrand']): ?>
<div class="user-info-field2" style="width: 475px;"><?php print CHtml::encode($user['favoriteCigaretteBrand']); ?></div>
<?php else: ?>
<div class="user-info-field2" style="width: 475px;"><em><?php print Yii::t('application', 'не указано'); ?></em></div>
<?php endif; ?>
<div class="clr"></div>