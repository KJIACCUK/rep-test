<?php
    /* @var $this UserVerificationController */
    /* @var $model UserVerificationRequest */

    function getMessenger($messenger, $messengerLogin)
    {
        if($messenger && $messengerLogin)
        {
            if($messenger == 'skype')
            {
                return TbHtml::linkButton('Skype: '.$messengerLogin, array('url' => 'skype:'.$messengerLogin, 'icon' => TbHtml::ICON_HEADPHONES));
            }
            else
            {
                return 'Hangouts: '.$messengerLogin;
            }
        }

        return '';
    }

    $employee = Employee::model()->findByPk($model->employeeId);
    if($employee)
    {
        $employee = $employee->name;
    }
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Заявка #').$model->userVerificationRequestId, array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Верификация'); ?></h1>

<h3><?php print Yii::t('application', 'Заявка'); ?></h3>
<?php
    $this->widget('zii.widgets.CDetailView', array(
        'htmlOptions' => array(
            'class' => 'table table-striped table-condensed table-hover',
        ),
        'data' => $model,
        'attributes' => array(
            'userVerificationRequestId',
            array(
                'name' => 'messenger',
                'type' => 'raw',
                'value' => $model->isPhotoVerification?'':getMessenger($model->messenger, $model->messengerLogin)
            ),
            array(
                'label' => Yii::t('application', 'Дата и время звонка'),
                'type' => 'raw',
                'name' => 'callDate',
                'value' => $model->isMissed?Yii::t('application', 'Пропущенный вызов'):($model->isPhotoVerification?Yii::t('application', 'Верификация по фото'):TbHtml::labelTb(date(Yii::app()->params['dateFormat'], $model->callDate), array('color' => TbHtml::LABEL_COLOR_SUCCESS)).' '.TbHtml::labelTb(date('H:i', $model->callDate), array('color' => TbHtml::LABEL_COLOR_INFO)))
            ),
            array(
                'name' => 'photoAttachment',
                'type' => 'raw',
                'value' => $model->isPhotoVerification?'<a href="'.$model->photoAttachment.'" target="_blank" title="'.Yii::t('application', 'Нажмите для просмотра').'">'.TbHtml::imagePolaroid(CommonHelper::getImageLink($model->photoAttachment, '82x80')).'</a>':''
            ),
            array(
                'name' => 'dateCreated',
                'value' => date(Yii::app()->params['dateTimeFormat'], $model->dateCreated)
            ),
            array(
                'label' => Yii::t('application', 'Заявку закрыл'),
                'name' => 'callDate',
                'value' => $employee?$employee:Yii::t('application', 'Сотрудник удален')
            ),
            array(
                'name' => 'isVerified',
                'type' => 'raw',
                'value' => CommonHelper::yesnoToGridValue($model->isVerified)
            ),
            array(
                'type' => 'raw',
                'name' => 'status',
                'value' => TbHtml::labelTb(Yii::t('application', 'Закрыта'), array('color' => TbHtml::LABEL_COLOR_SUCCESS))
            ),
        ),
    ));
?>

<h3><?php print Yii::t('application', 'Пользователь'); ?></h3>

<?php
    $this->widget('zii.widgets.CDetailView', array(
        'htmlOptions' => array(
            'class' => 'table table-striped table-condensed table-hover',
        ),
        'data' => $model->user,
        'attributes' => array(
            'userId',
            'name',
            array(
                'name' => 'email',
                'type' => 'raw',
                'value' => $model->user->email?TbHtml::link($model->user->email, 'mailto:'.$model->user->email):''
            ),
            array(
                'label' => Yii::t('application', 'Телефон'),
                'type' => 'raw',
                'value' => $model->user->phone?'+375 ('.$model->user->phoneCode.') '.$model->user->phone:''
            ),
            array(
                'name' => 'birthday',
                'value' => $model->user->birthday?date(Yii::app()->params['dateFormat'], $model->user->birthday):''
            ),
            array(
                'name' => 'image',
                'type' => 'raw',
                'value' => TbHtml::imagePolaroid(CommonHelper::getImageLink($model->user->image, '82x80'))
            ),
            array(
                'name' => 'messenger',
                'type' => 'raw',
                'value' => getMessenger($model->user->messenger, $model->user->messengerLogin)
            ),
            'favoriteMusicGenre',
            'favoriteCigaretteBrand',
            'login',
            array(
                'name' => 'points',
                'value' => Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($model->user->points))
            ),
            array(
                'name' => 'isFilled',
                'type' => 'raw',
                'value' => CommonHelper::yesnoToGridValue($model->user->isFilled)
            ),
            array(
                'name' => 'isVerified',
                'type' => 'raw',
                'value' => CommonHelper::yesnoToGridValue($model->user->isVerified)
            ),
            array(
                'name' => 'account.isActive',
                'type' => 'raw',
                'value' => CommonHelper::yesnoToGridValue($model->user->account->isActive)
            ),
            array(
                'label' => Yii::t('application', 'Дата регистрации'),
                'name' => 'account.dateCreated',
                'value' => date(Yii::app()->params['dateTimeFormat'], $model->user->account->dateCreated)
            )
        ),
    ));
?>

<h3><?php print Yii::t('application', 'Решение'); ?></h3>

<div class="well form">
    <div>
        <?php if($model->comment): ?>
                <?php print CHtml::encode($model->comment); ?>
            <?php else: ?>
                <i><?php print Yii::t('application', 'нет комментариев'); ?></i>
        <?php endif; ?>
    </div>
    <?php if($model->attachment): ?>
        <div>
            <?php print TbHtml::link(Yii::t('application', 'Приложение'), $model->attachment); ?>
        </div>
    <?php endif; ?>

</div>