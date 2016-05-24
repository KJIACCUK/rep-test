<?php
    /* @var $this EventController */
    /* @var $model Event */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Добавить мероприятие'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    $cs->registerCssFile($cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css'); 
    
    $cs->registerScript('ymaps_autocomplete', "

        $('#Event_city').autocomplete({
            source: function(request, response){
                $.ajax({
                    url: 'https://geocode-maps.yandex.ru/1.x/',
                    data: {
                        'geocode': '".Yii::t('application', 'Беларусь, город')." '+request.term,
                        'lang': 'ru_RU',
                        'format': 'json',
                        'kind': 'locality',
                        'results': 7
                    },
                    success: function(data){
                        result = [];
                        if(typeof(data.response) != 'undefined')
                        {
                            for(var i in data.response.GeoObjectCollection.featureMember)
                            {
                                var geoObject = data.response.GeoObjectCollection.featureMember[i].GeoObject;
                                if(geoObject.metaDataProperty.GeocoderMetaData.kind == 'locality')
                                {
                                    result.push(geoObject.name);
                                }
                            }
                        }
                        response(result);
                    },
                    dataType: 'json'
                });
            },
            minLength: 3,
            appendTo: '#Event_autocomplete'
        });
        
        $('#Event_street').autocomplete({
            source: function(request, response){
                var city = $('#Event_city').val();
                if(city.length)
                {
                    $.ajax({
                        url: 'https://geocode-maps.yandex.ru/1.x/',
                        data: {
                            'geocode': '".Yii::t('application', 'Беларусь, город')." '+city+', '+request.term,
                            'lang': 'ru_RU',
                            'format': 'json',
                            'kind': 'street',
                            'results': 50
                        },
                        success: function(data){
                            result = [];
                            if(typeof(data.response) != 'undefined')
                            {
                                for(var i in data.response.GeoObjectCollection.featureMember)
                                {
                                    var geoObject = data.response.GeoObjectCollection.featureMember[i].GeoObject;
                                    if(geoObject.metaDataProperty.GeocoderMetaData.kind == 'street' && geoObject.metaDataProperty.GeocoderMetaData.precision == 'street')
                                    {
                                        if(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.CountryName == '".Yii::t('application', 'Беларусь')."')
                                        {
                                            if(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.LocalityName.toLowerCase() == city.toLowerCase())
                                            {
                                                result.push(geoObject.name);
                                                if(result.length == 7)
                                                {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            response(result);
                        },
                        dataType: 'json'
                    });
                }
                else
                {
                    response([]);
                }
            },
            minLength: 3,
            appendTo: '#Event_autocomplete'
        });
        
        $('#Event_houseNumber').autocomplete({
            source: function(request, response){
                var city = $('#Event_city').val();
                var street = $('#Event_street').val();
                if(city.length && street.length)
                {
                    $.ajax({
                        url: 'https://geocode-maps.yandex.ru/1.x/',
                        data: {
                            'geocode': '".Yii::t('application', 'Беларусь, город')." '+city+', '+street+' '+request.term,
                            'lang': 'ru_RU',
                            'format': 'json',
                            'kind': 'house',
                            'results': 50
                        },
                        success: function(data){
                            result = [];
                            if(typeof(data.response) != 'undefined')
                            {
                                for(var i in data.response.GeoObjectCollection.featureMember)
                                {
                                    var geoObject = data.response.GeoObjectCollection.featureMember[i].GeoObject;
                                    if(geoObject.metaDataProperty.GeocoderMetaData.kind == 'house' && (geoObject.metaDataProperty.GeocoderMetaData.precision == 'number' || geoObject.metaDataProperty.GeocoderMetaData.precision == 'exact' || geoObject.metaDataProperty.GeocoderMetaData.precision == 'near' || geoObject.metaDataProperty.GeocoderMetaData.precision == 'range'))
                                    {
                                        if(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.CountryName == '".Yii::t('application', 'Беларусь')."')
                                        {
                                            if(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.LocalityName.toLowerCase() == city.toLowerCase())
                                            {
                                                if (typeof(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.Thoroughfare.Premise.PremiseNumber) != 'undefined')
                                                {
                                                    result.push(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.Thoroughfare.Premise.PremiseNumber);
                                                    if(result.length == 7)
                                                    {
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            response(result);
                        },
                        dataType: 'json'
                    });
                }
                else
                {
                    response([]);
                }
            },
            minLength: 1,
            appendTo: '#Event_autocomplete'
        });

    ");
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('event/index'); ?>"><?php print Yii::t('application', 'Мероприятия'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('event/myEvents'); ?>"><?php print Yii::t('application', 'Мои Мероприятия'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Создать мероприятие'); ?></a>
    </li>
</ul>
<div class="mir_add_bl">
    <div class="title_l"><?php print Yii::t('application', 'Добавить мероприятие'); ?></div>
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'registration-form',
        'enableAjaxValidation' => false,
        'focus' => array($model, 'name'),
    )); ?>

    <?php /* @var $form CActiveForm */ ?>

    <?php print $form->errorSummary($model); ?>
    
    <div class="min_tx">
        <p><?php print $form->label($model, 'name'); ?></p>
        <div class="inp_tx customtx">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'name'); ?>
        </div>
    </div>
    <div class="clr"></div>
    
    <div class="date_r min_tx">
        <p><?php print $form->label($model, 'dateStart', array('for' => 'Event_dateStartDay')); ?></p>
        <?php print $form->dropDownList($model, 'dateStartDay', CommonHelper::getRange(1, 31), array('class' => 'select-field')); ?>
        <?php print $form->dropDownList($model, 'dateStartMonth', Yii::app()->locale->getMonthNames('wide', true), array('class' => 'select-field')); ?>
        <?php print $form->dropDownList($model, 'dateStartYear', array_combine(range(date('Y'), date('Y', strtotime('+5 year'))), range(date('Y'), date('Y', strtotime('+5 year')))), array('class' => 'select-field')); ?>
    </div>
    <div class="clr"></div>
    
    <div class="time_c min_tx">
        <p><?php print $form->label($model, 'timeStart', array('for' => 'Event_timeStartHours')); ?></p>
        <?php print $form->dropDownList($model, 'timeStartHours', CommonHelper::getRange(0, 23), array('class' => 'select-field')); ?>
        <?php print $form->dropDownList($model, 'timeStartMinutes', CommonHelper::getRange(0, 59, 15), array('class' => 'select-field')); ?>
    </div>
    <div style="float: left; width: 82px;">&nbsp;</div>
    <div class="time_c min_tx">
        <p><?php print $form->label($model, 'timeEnd', array('for' => 'Event_timeEndHours')); ?></p>
        <?php print $form->dropDownList($model, 'timeEndHours', CommonHelper::getRange(0, 23), array('class' => 'select-field')); ?>
        <?php print $form->dropDownList($model, 'timeEndMinutes', CommonHelper::getRange(0, 59, 15), array('class' => 'select-field')); ?>
    </div>
    <div class="clr"></div>
    
    <br/>
    
    <div class="title_l"><?php print Yii::t('application', 'Место проведения'); ?></div>
    <div class="line_reg"></div>
    <div class="mest_pr">
        <div class="inp_tx customtx" style="width: 200px; float: left; margin-right: 20px;">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'city', array('placeholder' => Yii::t('application', 'Город'))); ?>
        </div>
        <div class="inp_tx customtx street">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'street', array('placeholder' => Yii::t('application', 'Улица'))); ?>
        </div>
        <div class="inp_tx customtx street_h">
            <div class="inp_txr"></div>
            <div class="inp_txl"></div>
            <?php print $form->textField($model, 'houseNumber', array('placeholder' => Yii::t('application', 'Дом'))); ?>
        </div>
    </div>
    <div class="clr"></div>
    
    <div class="line_reg"></div>
    <div class="min_tx">
        <p><?php print $form->label($model, 'category'); ?></p>
        <?php print $form->dropDownList($model, 'category', array_combine(Yii::app()->params['eventCategories'], Yii::app()->params['eventCategories']), array('class' => 'select-field')); ?>
    </div>
    
    <div class="check_mir">
        <?php print$form->radioButtonList($model, 'eventAccess', EventHelper::getAccessList(), array('template' => '<div class="event_access_wrap">{input} {label}</div>', 'separator' => '')); ?>
    </div>
    <div class="clr"></div>
    
    <div class="mir_text">
        <p><?php print $form->label($model, 'description'); ?></p>
        <?php print $form->textArea($model, 'description', array('rows' => 5, 'cols' => 50)); ?>
    </div>
    
    <?php print CHtml::submitButton(Yii::t('application', 'Создать и отправить на модерацию'), array('id' => 'btnAdd', 'class' => 'but_blue add_mod')); ?>
    <a href="<?php print $this->createUrl('event/myEvents'); ?>" class="btn-cancel"><?php print Yii::t('application', 'Отмена'); ?></a>
    <div class="clr"></div>
    
    <?php $this->endWidget(); ?>
    
</div>
<div id="Event_autocomplete"></div>