<?php
    /* @var $this UserController */
    /* @var $user array */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Пригласить'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('user_invite', "
        
        function getAge(birthday)
        {
            return birthday;
        }
        
        function addUser(name, image, birthday)
        {
            var template = '<li>'+
                                '<div class=\"check_lf\">'+
                                    '<input type=\"checkbox\" class=\"check_b checkbox-field\"/>'+
                                '</div>'+
                                '<span class=\"ico_frend\" style=\"background-image:url('+image+');\">'+
                                    '<a href=\"#\"></a>'+
                                '</span>'+
                                '<div class=\"inf_frend\">'+
                                    '<p>'+name+'</p>'+
                                    '<p class=\"frend_age\">'+birthday+'</p>'+
                                '</div>'+
                            '</li>';
                            
            $('#usersList').append(template);                
        }
        
        var offset = 0;
        var limit = ".$this->usersInviteLimit.";
        var allLoaded = false;
        
        $.getScript('//connect.facebook.net/ru_RU/all.js', function(){
            FB.init({
                appId: '".Yii::app()->params['facebookAppId']."',
                version: 'v2.0'
            });
            
            

            FB.login(function(){
            
                FB.ui({method: 'apprequests',
                    message: 'YOUR_MESSAGE_HERE'
                }, function(response){
                  console.log(response);
                });

            });
        });
    ");
?>

<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('user/friends'); ?>"><?php print Yii::t('application', 'Друзья'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('user/search'); ?>"><?php print Yii::t('application', 'Найти друзей'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href=""><?php print Yii::t('application', 'Пригласить'); ?></a>
    </li>
</ul>

<ul id="usersList" class="list_frend"></ul>

<button class="but_light add_one" style="margin-right: 120px; width: 150px;" type="submit"><?php print Yii::t('application', 'Выделить всех'); ?></button>
<button class="but_blue add_all" type="submit"><?php print Yii::t('application', 'Пригласить'); ?></button>