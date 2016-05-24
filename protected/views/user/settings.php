<?php
    /* @var $this UserController */
    /* @var $settings array */
    
    $notificationLabels = UserNotificationsHelper::getSettingLabels();
    
    $this->setPageTitle(Yii::t('application', 'Настройки'));
    
    $cs = Yii::app()->clientScript;
?>
<div class="opros">
    <div class="title_l top_pad">
        <?php print Yii::t('application', 'Настройки'); ?>
    </div>
    <ul class="list_opros nastroy">
        <li>
            <div class="ico_n">
                <img src="images/ico_nastr_03.gif"/>
            </div>
            <a href="<?php print $this->createUrl('user/feedback'); ?>"><?php print Yii::t('application', 'Сообщить о проблеме'); ?></a>
        </li>
        <li>
            <div class="ico_n" style="margin-top:-4px;">
                <img src="images/ico_nastr_06.gif"/>
            </div>
            <a href="<?php print $this->createUrl('user/help'); ?>"><?php print Yii::t('application', 'Справка'); ?></a>
        </li>
        <li>
            <div class="ico_n">
                <img src="images/ico_nastr_09.gif"/>
            </div>
            <a href="#" onclick="return false;"><?php print Yii::t('application', 'Push-уведомления'); ?> </a>
        </li>
        <li class="act_li">
            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'settings-form',
                'enableAjaxValidation' => false
            )); ?>
            <ul>
                <?php foreach($settings as $settingKey => $isChecked): ?>
                    <?php if(UserNotificationSetting::SETTING_ANDROID_ENABLE_VIBRATION == $settingKey): ?>
                    <?php continue; ?>
                    <?php endif; ?>
                    <?php if(UserNotificationSetting::SETTING_NEW_MARKETING_RESEARCH == $settingKey): ?>
                    <li><?php print CHtml::checkBox('UserNotificationSetting['.$settingKey.']', true, array('id' => 'setting_'.$settingKey, 'class' => 'checkbox-field', 'disabled' => true)); ?><?php print CHtml::label($notificationLabels[$settingKey], 'setting_'.$settingKey); ?></li>
                    <?php else: ?>
                    <li><?php print CHtml::checkBox('UserNotificationSetting['.$settingKey.']', $isChecked, array('id' => 'setting_'.$settingKey, 'class' => 'checkbox-field')); ?><?php print CHtml::label($notificationLabels[$settingKey], 'setting_'.$settingKey); ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <?php print CHtml::submitButton(Yii::t('application', 'Сохранить'), array('id' => 'btnComplete', 'class' => 'but_light', 'style' => 'float: right;')); ?>
            <div class="clr"></div>
            <?php $this->endWidget(); ?>
        </li>
        <li>
            <div class="ico_n" style="margin-top:-6px;">
                <img src="/images/ico_nastr_12.gif"/>
            </div>
            <a onclick="return confirm('<?php print Yii::t('application', 'Вы уверены, что хотите сбросить все настройки?'); ?>');" href="<?php print $this->createUrl('user/settings', array('reset' => 1)); ?>"><?php print Yii::t('application', 'Сброс всех настроек'); ?></a>
        </li>
        <li>
            <div class="ico_n" style="margin-top:-6px; margin-left: 5px; margin-right: 21px;">
                <img src="/images/exit.png"/>
            </div>
            <a onclick="return confirm('<?php print Yii::t('application', 'Вы уверены, что хотите выйти из приложения?'); ?>');" href="<?php print $this->createUrl('site/logout'); ?>"><?php print Yii::t('application', 'Выйти из приложения'); ?></a>
        </li>
    </ul>
    <div class="clr"></div>
</div>