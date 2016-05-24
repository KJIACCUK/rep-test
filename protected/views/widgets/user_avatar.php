<?php
    /* @var $this UserAvatar */
    /* @var $cs CClientScript */
    
    $cs = Yii::app()->clientScript;
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    $cs->registerScriptFile('/js/jquery.iframe-transport.js');
    $cs->registerScriptFile('/js/jquery.fileupload.js');
    
    $cs->registerScript('saveAvatar', "

        $('#btnUploadAvatar').click(function(){
            $('#uploadAvatar').click();
            return false;
        });

        $('#uploadAvatar').fileupload({
            url: '".$this->getController()->createUrl($this->saveUrl)."',
            dataType: 'json',
            acceptFileTypes: /(\.|\/)(jpg|jpe|jpeg|png)$/i,
            maxFileSize: 2 * 1024 * 1024,
            start: function (e) {
                $('#btnUploadAvatar').hide();
                $('#uploadAvatarProgressBar span').css('width', '0%');
                $('#uploadAvatarProgressBar').show();
            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#uploadAvatarProgressBar span').css('width', progress + '%');
            },
            done: function (e, data) {
            
                result = data.result;

                if(typeof(result['success']) != 'undefined')
                {
                    if(result.success)
                    {
                        alertSuccess('".Yii::t('application', 'Аватар обновлен.')."');
                        $('#avatarView').css('background-image', 'url('+result.image+')');
                    }
                    else
                    {
                        alertError(result.message);
                    }
                }
                else
                {
                    alertError('".Yii::t('application', 'Во время загрузки произошла ошибка. Попробуйте еще раз.')."');
                }
            },
            fail: function (e, data) {
                alertError('".Yii::t('application', 'Во время загрузки произошла ошибка. Попробуйте еще раз.')."');
            },
            always: function (e, data) {
                $('#uploadAvatarProgressBar').hide();
                $('#btnUploadAvatar').show();
            },
        });

    ");
?>
<div class="img_pofil">
    <div id="avatarView" class="photo_log" style="background-image:url(<?php print $this->image; ?>)">
        <div></div>
    </div>
    <input id="uploadAvatar" style="display: none;" type="file" name="User[imageFile]">
    <div id="uploadAvatarProgressBar" class="meter" style="display: none;">
	<span style="width: 0%"></span>
    </div>
    <a id="btnUploadAvatar" href="#"><?php print Yii::t('application', 'Загрузить аватар'); ?></a>
</div>