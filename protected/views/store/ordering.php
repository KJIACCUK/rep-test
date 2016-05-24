<?php
    /* @var $this StoreController */
    /* @var $cs CClientScript */
    /* @var $currentUser User */
    /* @var $product Product */
    /* @var $purchaseModel ProductPurchase */
    /* @var $addressModel DeliveryAddress */
   
    $this->setPageTitle(Yii::t('application', 'Оформление заказа'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $currentUser = $this->getUser();
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'Опросы'); ?></a>
    </li>
    <li class="act">
        <a href="<?php print $this->createUrl('store/index'); ?>"><?php print Yii::t('application', 'Бонусный магазин'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('store/history'); ?>"><?php print Yii::t('application', 'Заказанные сувениры'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('promo/index'); ?>"><?php print Yii::t('application', 'Ввести промо-код'); ?></a>
    </li>
</ul>
<div class="bonus_mag">
    <div class="title_l top_pad">
        <?php print Yii::t('application', 'Оформление заказа'); ?>
    </div>
    
    <div class="line_reg" style="margin-bottom:10px;"></div>
    <ul class="list_mir ordering">
        <li>
            <a class="mir_img" href="<?php print $this->createUrl('store/detail', array('productId' => $product->productId)); ?>">
                <img src="<?php print CommonHelper::getImageLink($product->image, '130x100'); ?>" alt=""/>
            </a>
            <div class="mir_tx">
                <?php if($product->publisherName): ?>
                <p class="sm_tx"><?php print CHtml::encode($product->publisherName); ?></p>         
                <?php endif; ?>
                <p><?php print CHtml::encode($product->name); ?></p>
                <div class="date">
                    <?php if($product->dateStart): ?>
                    <?php print date(Yii::app()->params['dateFormat'], $product->dateStart); ?>
                    <?php endif; ?>
                </div>
                <p class="sm_p"><?php print Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($product->cost)); ?></p>
            </div>
        </li>
    </ul>
    
    <div class="bonus_lich">
        <div class="min_tx">
            <p><?php print Yii::t('application', 'Пользователь'); ?></p>
        </div>
        <span class="ico_frend" style="background-image:url(<?php print CommonHelper::getImageLink($currentUser->image, '82x80'); ?>);">
            <a href="<?php print $this->createUrl('user/index'); ?>"></a>
        </span>
        <div class="inf_frend">
            <p><?php print CHtml::encode($currentUser->name); ?></p>
            <p class="frend_age"><?php print UserHelper::getAge(date(Yii::app()->params['dateFormat'], $currentUser->birthday)); ?></p>
        </div>
    </div>
    
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'ordering-form',
        'enableAjaxValidation' => false
    )); ?>
    <?php /* @var $form CActiveForm */ ?>
    <?php print $form->errorSummary(array($purchaseModel, $addressModel)); ?>
    
    <?php if($product->type == Product::TYPE_WITH_SERTIFICATE): ?>
    <div class="line_reg" style="margin-bottom:10px;"></div>
    <div>
        <p><?php print Yii::t('application', 'После покупки товара на ваш E-mail в течение 24 часов будет отправлен сертификат'); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if($product->type == Product::TYPE_WITH_RECEIPT_ADDRESS): ?>
    <div class="line_reg" style="margin-bottom:10px;"></div>
    <div>
        <p>
            <?php print Yii::t('application', 'Товар можно забрать по адресу'); ?> <br />
            <?php print CHtml::encode($product->receiptAddress); ?>
        </p>
    </div>
    <?php endif; ?>
    
    <?php if($product->type == Product::TYPE_WITH_DELIVERY): ?>
    <div class="kont_d">
        <div class="inp_tx customtx black_t eml">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($addressModel, 'email', array('style' => 'text-decoration:underline;')); ?>
        </div>
        <div class="phone_c min_tx">
            <div class=" code_p black_t">
                <?php print $form->dropDownList($addressModel, 'phoneCode', Yii::app()->params['phoneCodes'], array('class' => 'select-field')); ?>
            </div>
            <div class="inp_tx customtx black_t phon_num">
                <div class="inp_txr"></div>
                <div class="inp_txl"></div>
                <?php print $form->textField($addressModel, 'phoneNumber', array('placeholder' => Yii::t('application', 'Номер'))); ?>
            </div>
        </div>
    </div>
    <div class="line_reg" style="margin-bottom:10px;"></div>
    
    <div class="min_tx dost_kont">
        <p><?php print Yii::t('application', 'Адрес доставки'); ?></p>
        <div class="inp_tx customtx index_d">
                <div class="inp_txr"></div>
                <div class="inp_txl"></div>
                <?php print $form->textField($addressModel, 'postIndex', array('placeholder' => Yii::t('application', 'Индекс'))); ?>
        </div>
        <div class="inp_tx customtx street_d">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($addressModel, 'city', array('placeholder' => Yii::t('application', 'Город'))); ?>
        </div>
        <div class="clr"></div>
        <div class="inp_tx customtx street_d">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($addressModel, 'street', array('placeholder' => Yii::t('application', 'Улица'))); ?>
        </div>
        <div class="inp_tx customtx dom_d">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($addressModel, 'home', array('placeholder' => Yii::t('application', 'Дом'))); ?>
        </div>
        <div class="inp_tx customtx dom_d">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($addressModel, 'corp', array('placeholder' => Yii::t('application', 'Корп.'))); ?>
        </div>
        <div class="inp_tx customtx dom_d">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($addressModel, 'apartment', array('placeholder' => Yii::t('application', 'Кв.'))); ?>
        </div>
        <div class="clr"></div>
    </div>
    <?php endif; ?>
    
    <div class="line_reg" style="margin-bottom:10px;"></div>
    <div class="dost_coment">
        <p><?php print Yii::t('application', 'Комментарии'); ?></p>
        <?php print $form->textArea($purchaseModel, 'comment', array('rows' => 5, 'cols' => 50)); ?>
        <button class="but_light" type="submit"><?php print Yii::t('application', 'Заказать'); ?></button>
        <button onclick="window.location.href='<?php print $this->createUrl('store/detail', array('productId' => $product->productId)); ?>'" class="but_blue" type="button"><?php print Yii::t('application', 'Отменить'); ?></button>
    </div>

    <div class="clr"></div>
<?php $this->endWidget(); ?>
</div>