<?php
    /* @var $this UserController */
    /* @var $user array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', Yii::t('application', 'Страница пользователя')));
    
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('addFrendshipRequest', "
        
        $('#btnAddFriend').click(function() {
            var self = this;
            var userId = ".$user['userId'].";
            $.ajax({
                url: '".$this->createUrl('user/addFriendshipRequest')."',
                type: 'GET',
                data: {'userId': userId},
                dataType: 'json',
                success: function(data, status, xhr)
                {
                    if(typeof(data['success']) != 'undefined')
                    {
                        if (data.success)
                        {
                            alertSuccess('".Yii::t('application', 'Запрос на добавление в друзья отправлен.')."');
                            $(self).replaceWith('<div class=\"add_frend_invited\">".Yii::t('application', 'Отправлено')."</div>');
                        }
                        else
                        {
                            alertError(data.message);
                        }
                    }
                    else
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при добавлении в друзья. Попробуйте перезагрузить страницу.')."');
                    }
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при добавлении в друзья. Попробуйте перезагрузить страницу.')."');
                }
            });

            return true;
        });

    ");
?>
<div id="profile-edit" class="title_l">
    <div class="profile-edit-title"><?php print CHtml::encode(Yii::t('application', 'Страница пользователя')); ?></div>
    <?php if(!$user['isFriend']): ?>
        <?php if(!$user['friendshipRequest']): ?>
        <div class="profile-edit-button"><?php print CHtml::button(Yii::t('application', '+1 Добавить'), array('id' => 'btnAddFriend', 'class' => 'but_light')); ?></div>
        <?php else: ?>
        <div class="add_frend_invited"><?php print Yii::t('application', 'Отправлено'); ?></div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="clr"></div>
</div>
<div class="line_reg"></div>

<div class="img_pofil">
    <div id="avatarView" class="photo_log" style="background-image:url(<?php print $user['image']; ?>)">
        <div></div>
    </div>
</div>

<div class="inf_lich">
    <div class="name_l"><?php print CHtml::encode($user['name']); ?></div>
</div>

<div class="clr"></div>
<div class="nav_prof">
    <button class="but_light" onclick="window.location.href='<?php print $this->createUrl('user/userFriends', array('userId' => $user['userId'])); ?>'" type="submit"><?php print Yii::t('application', 'Друзья'); ?> (<?php print $user['counters']['friends']; ?>)</button>
    <button class="but_light" onclick="window.location.href='<?php print $this->createUrl('event/comingEvents', array('userId' => $user['userId'])); ?>'" type="submit"><?php print Yii::t('application', 'Мероприятия'); ?> (<?php print $user['counters']['events']; ?>)</button>
    <button class="but_blue" onclick="window.location.href='<?php print $this->createUrl('event/pastEvents', array('userId' => $user['userId'])); ?>'" type="submit"><?php print Yii::t('application', 'Прошедшие'); ?> (<?php print $user['counters']['pastEvents']; ?>)</button>
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