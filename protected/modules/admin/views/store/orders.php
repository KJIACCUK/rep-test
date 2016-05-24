<?php
    /* @var $this StoreController */
    /* @var $orders ProductPurchase[] */
    /* @var $dataProvider CActiveDataProvider */
    /* @var $cs CClientScript */
    
    $cs = Yii::app()->clientScript;
    
    $cs->registerScript('orders', "
        $('#btnOrdersFilterReset').click(function(){
            $('#search').val('');
            $('#dateStart').val('');
            $('#dateEnd').val('');
            window.location.href = '".$this->createUrl('orders')."';
        });
    ");
    
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Заказы'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<div style="margin-top: 30px;">
    <?php echo TbHtml::beginFormTb(TbHtml::FORM_LAYOUT_INLINE); ?>
    <?php echo TbHtml::textField('search', Web::getParam('search'), array('id' => 'search', 'placeholder' => Yii::t('application', 'Введите ID, название товара, имя пользователя или код'), 'size' => TbHtml::INPUT_SIZE_XXLARGE)); ?>
    <?php echo TbHtml::textField('dateStart', Web::getParam('dateStart'), array('id' => 'dateStart', 'placeholder' => date(Yii::app()->params['dateFormat']), 'size' => TbHtml::INPUT_SIZE_SMALL)); ?>
    <?php echo TbHtml::textField('dateEnd', Web::getParam('dateEnd'), array('id' => 'dateEnd', 'placeholder' => date(Yii::app()->params['dateFormat']), 'size' => TbHtml::INPUT_SIZE_SMALL)); ?>
    <?php echo TbHtml::submitButton(Yii::t('application', 'Найти'), array('color' => TbHtml::BUTTON_COLOR_INVERSE)); ?>
    <?php echo TbHtml::button(Yii::t('application', 'Сбросить'), array('id' => 'btnOrdersFilterReset')); ?>
    <?php echo TbHtml::endForm(); ?>
</div>


<div class="view" style="padding: 10px; margin-bottom: 10px;">
    <div class="pull-left" style="width: 60px;">
        <?php print Yii::t('application', 'ID'); ?>
    </div>
    <div class="pull-left" style="width: 130px;">
        <?php print Yii::t('application', 'Дата заказа'); ?>
    </div>

    <div class="pull-left" style="width: 100px;">
        <?php print Yii::t('application', 'Стоимость'); ?>
    </div>

    <div class="pull-left" style="width: 300px;">
        <?php print Yii::t('application', 'Товар'); ?>
    </div>

    <div class="pull-left" style="width: 300px;">
        <?php print Yii::t('application', 'Пользователь'); ?>
    </div>

    <div class="pull-left" style="width: 100px;">
        <?php print Yii::t('application', 'Код заказа'); ?>
    </div>

    <div class="clearfix"></div>
</div>

<?php
    $this->widget('bootstrap.widgets.TbListView', array(
        'id' => 'ordersListView',
        'dataProvider' => $dataProvider,
        'itemView' => '_order_view',
    ));
?>

<?php    
    Yii::app()->clientScript->registerScript('orders_list', "
        $.fn.yiiListView.update('ordersListView', {
            data: {
                'search': '".Web::getParam('search', '')."',
                'dateStart': '".Web::getParam('dateStart', '')."',
                'dateEnd': '".Web::getParam('dateEnd', '')."'
            }
        });
    ");
?>
