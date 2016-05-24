<?php
    /* @var $this StoreController */
    /* @var $order ProductPurchase */

    $types = ProductHelper::typesToEdit();
    
    if($order->deliveryAddress)
    {
        $delivery = $order->deliveryAddress->postIndex.' '.$order->deliveryAddress->city.', '.$order->deliveryAddress->street.' '.$order->deliveryAddress->home;
        if($order->deliveryAddress->corp)
        {
            $delivery .= ' '.$order->deliveryAddress->corp;
        }
        if($order->deliveryAddress->apartment)
        {
            $delivery .= ' '.$order->deliveryAddress->apartment;
        }
        
        $delivery .= '<br />';
        $delivery .= Yii::t('application', 'E-mail').': '.$order->deliveryAddress->email;
        $delivery .= '<br />';
        $delivery .= Yii::t('application', 'Телефон').': '.$order->deliveryAddress->phone;
    }
    else
    {
        $delivery = Yii::t('application', 'без доставки');
    }
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Заказ #').$order->productPurchaseId, array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Бонусный магазин'); ?></h1>

<?php
    $this->widget('zii.widgets.CDetailView', array(
        'htmlOptions' => array(
            'class' => 'table table-striped table-condensed table-hover',
        ),
        'data' => $order,
        'attributes' => array(
            'productPurchaseId',
            array(
                'label' => Yii::t('application', 'Дата заказа'),
                'type' => 'raw',
                'value' => date(Yii::app()->params['dateTimeFormat'], $order->dateCreated)
            ),
            'purchaseCode',
            array(
                'label' => Yii::t('application', 'Артикул'),
                'name' => 'product.articleCode'
            ),
            array(
                'label' => Yii::t('application', 'Товар'),
                'type' => 'raw',
                'value' => TbHtml::link($order->product->name, $this->createUrl('store/update', array('id' => $order->productId)))
            ),
            
            array(
                'label' => Yii::t('application', 'Стоимость'),
                'type' => 'raw',
                'value' => Yii::t('application', 'n==1#1 балл|n<5#{n} балла|n>4#{n} баллов', array($order->pointsCount))
            ),
            array(
                'label' => Yii::t('application', 'Тип'),
                'type' => 'raw',
                'value' => $types[$order->product->type]
            ),
            array(
                'label' => Yii::t('application', 'Заказчик'),
                'type' => 'raw',
                'value' => TbHtml::link('('.$order->userId.') '.$order->user->name, $this->createUrl('user/view', array('id' => $order->userId)))
            ),
            'comment',
            array(
                'label' => Yii::t('application', 'Доставка'),
                'type' => 'raw',
                'value' => $delivery
            ),
        ),
    ));
?>