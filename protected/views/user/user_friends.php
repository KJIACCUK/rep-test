<?php
    /* @var $this UserController */
    /* @var $users array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Друзья пользователя'));
    $this->layout = '//layouts/main';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('user_friends', "
        var offset = ".$this->usersLimit.";
        var limit = ".$this->usersLimit.";
        var allLoaded = false;
 
        function loadUsers()
        {
            if(allLoaded)
            {
                return false;
            }
            $.ajax({
                url: '".$this->createUrl('user/userFriends', array('userId' => Web::getParam('userId')))."',
                type: 'GET',
                data: {'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#usersList').append(data);
                    offset = $('#usersList li').length;
                    if(data.length == 0)
                    {
                        allLoaded = true;
                    }
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                }
            });
        }

        $(window).scroll(function()
        {
            if (document.body.scrollHeight - $(this).scrollTop()  <= $(this).height())
            {
                loadUsers();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadUsers();
            }
        });
        
        $('#usersList').on('click', '.add_frend', function() {
            var self = this;
            var userId = $(this).attr('id');
            userId = userId.replace('invite_user_', '');
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
<div class="title_l">
    <div><?php print Yii::t('application', 'Друзья пользователя'); ?></div>
    <div class="clr"></div>
</div>
<div class="line_reg"></div>
<ul id="usersList" class="list_frend">
    <?php print $this->renderPartial('_user_friends_items', array('users' => $users)); ?>
</ul>