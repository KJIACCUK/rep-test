<?php
    /* @var $this PromoController */
    /* @var $cs CClientScript */
    /* @var $model PromoForm */
   
    $this->setPageTitle(Yii::t('application', 'Опросы'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'Опросы'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('store/index'); ?>"><?php print Yii::t('application', 'Бонусный магазин'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('store/history'); ?>"><?php print Yii::t('application', 'Заказанные сувениры'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;"><?php print Yii::t('application', 'Ввести промо-код'); ?></a>
    </li>
</ul>
<div class="opros">
    <div class="title_l top_pad">
        <?php print Yii::t('application', 'Получил промо-код и не знаешь что с ним делать?'); ?>
    </div>
    <ul id="promoInstructionList">
        <li>Введи промо-код и нажми «Отправить»</li>
        <li>Дополнительные бонусные баллы будут зачислены на твой <a href="<?php print $this->createUrl('store/index'); ?>">счет</a></li>
        <li>Меняй бонусные баллы на сувениры и билеты на концерты в бонусном магазине</li>
    </ul>
    <div class="promoFormWrap">
        <div class="promoFormIcon"></div>
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'promo-form',
            'enableAjaxValidation' => false
        )); ?>
        <?php /* @var $form CActiveForm */ ?>
        <?php print $form->errorSummary($model); ?>
        
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'code', array('placeholder' => 'Введите промо-код')); ?>
        </div>
        
        <button id="promoBtnSubmit" class="but_light" type="submit"><?php print Yii::t('application', 'Отправить'); ?></button>
        
        <div class="clr"></div>
        <?php $this->endWidget(); ?>
    </div>
    <div class="clr"></div>
</div>