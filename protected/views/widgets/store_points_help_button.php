<?php
    /* @var $this StorePointsHelpButton */
    /* @var $cs CClientScript */
    
    $cs = Yii::app()->clientScript;
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    
    $cs->registerScript('store_points_help_button', "
        $('.storePointsHelpButton').click(function(){
            $('#storePointsHelpDialog').dialog('open');
            return false;
        });
    ");
?>
<a class="zar_bal storePointsHelpButton" href="#"><?php print Yii::t('application', 'Как заработать баллы?'); ?></a>
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'storePointsHelpDialog',
        'scriptFile' => false,
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => Yii::t('application', 'Как заработать баллы?'),
            'autoOpen' => false,
            'modal' => true,
            'resizable' => false,
            'draggable' => false,
            'width' => 500
        ),
    ));
    ?>

<p>В приложении баллы начисляются за следующие действия:</p>
<p>- приглашение друзей в приложение (1 балл)</p>
<p>- верификации приглашенного пользователя (10 баллов)</p>
<p>- ответы на опросы (10 баллов)</p>
<p>- ежедневное посещение раздела опросов (3 балла)</p>
<p>- созданное мероприятие с подписчиками больше 10 человек (3 балла)</p>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>