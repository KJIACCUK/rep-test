<h1><?php print Yii::t('application', 'Нотификации (тестовый режим)'); ?></h1>
<div class="well well-small form">
<?php echo CHtml::beginForm() ?>
    <div class="control-group" style="margin-bottom: 20px">
        <?php echo CHtml::label('Текст нотификации','Notification[notification_text]',array('style'=>'margin-right: 10px')); ?>
        <?php echo CHtml::textField('Notification[text]','',array('class' => 'span10','size'=>128)); ?>
    </div>
    <div class="control-group">
        <?php echo CHtml::submitButton('Отправить нотификацию',array('class'=>'btn btn-primary')); ?>
        <?php echo CHtml::dropDownList('Notification[user_type]','',array(
          1=>'Всем пользователям',
          2=>'Пользователям, не посещавшим приложение неделю',
          3=>'Всем верифицированным пользователям',
          4=>'Всем неверифицированным пользователям',
        ),array('style'=>'margin: 0'))?>
    </div>
<?php echo CHtml::endForm() ?>
</div>
