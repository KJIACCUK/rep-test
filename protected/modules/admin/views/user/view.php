<?php
    /* @var $this UserController */
    /* @var $model User */

    function getMessenger(User $user)
    {
        if($user->messenger && $user->messengerLogin)
        {
            if($user->messenger == 'skype')
            {
                return TbHtml::linkButton('Skype: '.$user->messengerLogin, array('url' => 'skype:'.$user->messengerLogin, 'icon' => TbHtml::ICON_HEADPHONES));
            }
            else
            {
                return 'Hangouts: '.$user->messengerLogin;
            }
        }

        return '';
    }
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Пользователь #').$model->userId, array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Пользователи'); ?></h1>

<h3><?php print Yii::t('application', 'Пользователь'); ?></h3>

<?php
    $this->widget('zii.widgets.CDetailView', array(
        'htmlOptions' => array(
            'class' => 'table table-striped table-condensed table-hover',
        ),
        'data' => $model,
        'attributes' => array(
            'userId',
            'name',
            array(
                'name' => 'email',
                'type' => 'raw',
                'value' => $model->email?TbHtml::link($model->email, 'mailto:'.$model->email):''
            ),
            array(
                'label' => Yii::t('application', 'Телефон'),
                'type' => 'raw',
                'value' => $model->phone?'+375 ('.$model->phoneCode.') '.$model->phone:''
            ),
            array(
                'name' => 'birthday',
                'value' => $model->birthday?date(Yii::app()->params['dateFormat'], $model->birthday):''
            ),
            array(
                'name' => 'image',
                'type' => 'raw',
                'value' => TbHtml::imagePolaroid(CommonHelper::getImageLink($model->image, '82x80'))
            ),
            array(
                'name' => 'messenger',
                'type' => 'raw',
                'value' => getMessenger($model)
            ),
            'favoriteMusicGenre',
            'favoriteCigaretteBrand',
            'login',
            array(
                'name' => 'points',
                'value' => Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($model->points))
            ),
            array(
                'name' => 'isFilled',
                'type' => 'raw',
                'value' => CommonHelper::yesnoToGridValue($model->isFilled)
            ),
            array(
                'name' => 'isVerified',
                'type' => 'raw',
                'value' => CommonHelper::yesnoToGridValue($model->isVerified)
            ),
            array(
                'name' => 'account.isActive',
                'type' => 'raw',
                'value' => $model->account->isActive?TbHtml::linkButton(Yii::t('application', 'Включен'), array('url' => $this->createUrl('disable', array('id' => $model->userId)), 'icon' => TbHtml::ICON_OK_SIGN, 'color' => TbHtml::BUTTON_COLOR_SUCCESS)):TbHtml::linkButton(Yii::t('application', 'Отключен'), array('url' => $this->createUrl('enable', array('id' => $model->userId)), 'icon' => TbHtml::ICON_OFF, 'color' => TbHtml::BUTTON_COLOR_WARNING))
            ),
            array(
                'label' => Yii::t('application', 'Дата регистрации'),
                'name' => 'account.dateCreated',
                'value' => date(Yii::app()->params['dateTimeFormat'], $model->account->dateCreated)
            )
        ),
    ));
?>

<h3><?php print Yii::t('application', 'Верификация'); ?></h3>
<div class="well form">
    <?php if($model->isVerified): ?>
        <div>
            <?php if($model->verification && $model->verification->comment): ?>
                <?php print CHtml::encode($model->verification->comment); ?>
            <?php else: ?>
                <i><?php print Yii::t('application', 'нет комментариев'); ?></i>
            <?php endif; ?>
        </div>
        <?php if($model->verification && $model->verification->attachmentFilePath): ?>
            <div>
                <?php print TbHtml::link(Yii::t('application', 'Приложение'), $model->verification->attachmentFilePath); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'verification-form',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));
        ?>

        <?php print $form->errorSummary($verification); ?>
        <?php /* @var $form TbActiveForm */ ?>

        <?php print $form->textAreaControlGroup($verification, 'comment', array('rows' => 6, 'span' => 8)); ?>
        <?php print $form->fileFieldControlGroup($verification, 'attachmentFile', array('span' => 8)); ?>

        <div class="form-actions">
            <?php
            echo TbHtml::submitButton(Yii::t('application', 'Верифицировать'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
            ?>
        </div>

        <?php $this->endWidget(); ?>
    <?php endif; ?>
</div>