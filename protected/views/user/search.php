<?php
    /* @var $this UserController */
    /* @var $users array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Найти друзей'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('search', "
        var searchQuery = '';
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
                url: '".$this->createUrl('user/search')."',
                type: 'GET',
                data: {'search': searchQuery, 'offset': offset, 'limit': limit},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#usersList').append(data);
                    offset = $('#usersList li').length;
                    FB.Canvas.setSize({height: $('body').height()});
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
        
        $('#searchUser').keyup(function(){
            searchQuery = $(this).val();
            if(searchQuery.length > 2 || searchQuery.length == 0)
            {
                offset = 0;
                allLoaded = false;
                $('#usersList').empty();
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
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('user/friends'); ?>"><?php print Yii::t('application', 'Друзья'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Найти друзей'); ?></a>
    </li>
    <li>
        <?php $this->widget('application.widgets.UserInviteSocialButton'); ?>
    </li>
</ul>

<div class="inp_tx customtx search_fr">
    <div class="inp_txr"></div>
    <div class="inp_txl"></div>
    <input id="searchUser" name="search" placeholder="<?php print Yii::t('application', 'Найти друга'); ?>" type="text"/>
</div>
<?php if(count($users)): ?>
    <ul id="usersList" class="list_frend">
        <?php print $this->renderPartial('_search_items', array('users' => $users)); ?>
    </ul>
<?php else: ?>
    <?php print Yii::t('application', 'Не удалось найти ни одного пользователя'); ?>
<?php endif; ?>