<?php
    /* @var $this EventController */
    /* @var $users array */
    /* @var $event Event */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Пригласить на мероприятие'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('event_invites', "
        $('#btnSelectAll').click(function(){
            $(':checkbox.check_b').prop('checked', true).trigger('refresh');
        });

    ");
?>
<?php $this->widget('application.widgets.EventSubpageTitle', array('title' => Yii::t('application', 'Пригласить друзей на мероприятие'), 'eventId' => $event->eventId, 'eventName' => $event->name)); ?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'event_invites-form',
    'enableAjaxValidation' => false
)); ?>

<?php /* @var $form CActiveForm */ ?>

<ul class="list_frend">
    <?php foreach($users as $item): ?>
    <li>
        <div class="check_lf">
            <?php print CHtml::checkBox('userIds[]', $item['isInvited'], array('value' => $item['userId'], 'class' => 'check_b checkbox-field')); ?>
        </div>
        <span class="ico_frend" style="background-image:url(<?php print $item['image']; ?>);">
            <a href="<?php print $this->createUrl('user/detail', array('userId' => $item['userId'])); ?>"></a>
        </span>
        <div class="inf_frend">
            <p><?php print CHtml::encode($item['name']); ?></p>
            <p class="frend_age"><?php print UserHelper::getAge($item['birthday']); ?></p>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<button id="btnSelectAll" class="but_light add_one" style="margin-right: 120px; width: 150px;" type="button"><?php print Yii::t('application', 'Выделить всех'); ?></button>
<button class="but_blue add_all" style="float: right;margin-right: 0;" type="submit"><?php print Yii::t('application', 'Пригласить'); ?></button>
<?php print CHtml::hiddenField('save', 1); ?>
<?php $this->endWidget(); ?>