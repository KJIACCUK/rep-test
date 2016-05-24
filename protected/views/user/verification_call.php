<?php
    /* @var $cs CClientScript */
    /* @var $this UserController */   
    /* @var $messenger string */
    /* @var $messengerLogin string */
    
    $this->layout = '//layouts/empty';
    $title = ($messenger == 'skype')?Yii::t('application', 'Звонок через Skype'):Yii::t('application', 'Звонок через Hangouts');
    $this->setPageTitle($title);
    $cs = Yii::app()->clientScript;
    
    if($messenger == 'skype')
    {
        $callAction = "window.location.href = 'skype:".Yii::app()->params['skypeLogin']."';";
    }
    else
    {
        $callAction = "window.open('https://hangoutsapi.talkgadget.google.com/hangouts/_?gid=".Yii::app()->params['hangoutsAppId']."');";
    }
    
    $cs->registerScript('verification_call', "
        $('#verification_call-form').submit(function(){

            var messengerLogin = $('#messengerLoginField').val();
            if (messengerLogin.length)
            {
                $.ajax({
                    url: '".$this->createUrl('user/verificationCall')."',
                    type: 'GET',
                    data: {'messenger': '".$messenger."', 'messengerLogin': messengerLogin},
                    dataType: 'json',
                    success: function(data, status, xhr)
                    {
                        if(data.success)
                        {
                            ".$callAction."
                        }
                        else
                        {
                            alertError(data.message);
                        }
                    },
                    error: function(xhr, status)
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при звонке. Попробуйте перезагрузить.')."');
                    }
                });
            }

            return false;
        });
    ");
    
?>
<div class="osn_b login_b">
    <div class="bl_login verif_p">
        <h1 style="margin-top:70px;"><?php print $title; ?></h1>
        
            <?php print CHtml::beginForm('', 'post', array(
                'id' => 'verification_call-form',
            )); ?>

            <div class="skype_l min_tx">
                <p><?php print Yii::app()->params['messengers'][$messenger]; ?></p>
                <div class="inp_tx customtx">
                    <div class="inp_txr"></div>
                    <div class="inp_txl"></div>
                    <?php print CHtml::textField('messengerLogin', $messengerLogin, array('id' => 'messengerLoginField', 'placeholder' => Yii::t('application', 'Логин'))); ?>
                </div>
            </div>

            <?php print CHtml::submitButton(Yii::t('application', 'Позвонить'), array('class' => 'but_light zak_zv')); ?>
            <a style="color: #ffffff; font-size: 16px; margin-left: 20px;" href="<?php print $this->createUrl('user/index'); ?>"><?php print Yii::t('application', 'Отменить'); ?></a>
        <?php print CHtml::endForm(); ?>
    </div>
    <div class="footer_b"></div>
</div>