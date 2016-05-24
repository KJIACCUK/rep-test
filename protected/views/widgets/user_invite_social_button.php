<?php
    /* @var $this UserInviteSocialButton */
    /* @var $cs CClientScript */

    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('user_invite_social_button', "
        
        $.getScript('//connect.facebook.net/ru_RU/all.js', function(){
        
            FB.init({
                appId: '".Yii::app()->params['facebook']['appId']."',
                version: 'v2.0'
            });
            
            $('#userInviteSocialBtn').click(function(){
            
                FB.login(function(loginResponce){
                
                    FB.ui({
                        method: 'apprequests',
                        display: 'popup',
                        title: '".Yii::t('application', 'Скачивай и регистрируйся в приложении БУДУТАМ')."',
                        message: '".Yii::t('application', 'Как быть в курсе всех тусовок? Как не пропустить самые большие события? Приложение “БУДУТАМ” поможет словить ритм окружения.')."'
                    }, function(response){
                        if((typeof response.request !== 'undefined') && (typeof response.to !== 'undefined'))
                        {
                            $.ajax({
                                url: '".$this->getController()->createUrl('user/saveInvitation')."',
                                type: 'POST',
                                data: {'accessToken': loginResponce.authResponse.accessToken, 'to': response.to},
                                dataType: 'json',
                                success: function(data, status, xhr)
                                {
                                    if(data.success)
                                    {
                                        alertSuccess('".Yii::t('application', 'Приглашения вашим друзьям отправлены.')."');
                                    }
                                    else
                                    {
                                        alertError(data.message);
                                    }
                                },
                                error: function(xhr, status)
                                {
                                    alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                                }
                            });
                        }
                    });
                }, {scope: 'user_friends'});
                
                return false;
            });
            
        });
    ");
?>
<a id="userInviteSocialBtn" href="#"><?php print Yii::t('application', 'Пригласить'); ?></a>