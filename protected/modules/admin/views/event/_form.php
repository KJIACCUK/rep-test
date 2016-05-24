<?php
/* @var $this EventController */
/* @var $model Event */
/* @var $cs CClientScript */

$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery');
$cs->registerCoreScript('jquery.ui');
$cs->registerCssFile($cs->getCoreScriptUrl().'/jui/css/base/jquery-ui.css');

$products = array(
    '' => ''
);
$productRows = Product::model()->findAll();
foreach ($productRows as $item) {
    $products[$item->productId] = $item->name;
}

$cs->registerScript('events', "
        
        var resultPoint = {};

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
                        var result = [];
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
            select: function(event, ui){
                $('#Event_street').val('');
                $('#Event_houseNumber').val('');
                $('#Event_latitude').val('');
                $('#Event_longitude').val('');
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
                            var result = [];
                            resultPoint = {};
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
                                                resultPoint[geoObject.name] = geoObject.Point.pos;
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
            select: function(event, ui){
                $('#Event_houseNumber').val('');
                if(typeof resultPoint[ui.item.value] !== 'undefined')
                {
                    var coordinates = resultPoint[ui.item.value].split(' ');
                    $('#Event_latitude').val(coordinates[1]);
                    $('#Event_longitude').val(coordinates[0]);
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
                            var result = [];
                            resultPoint = {};
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
                                                    resultPoint[geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.Thoroughfare.Premise.PremiseNumber] = geoObject.Point.pos;
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
            select: function(event, ui){
                if(typeof resultPoint[ui.item.value] !== 'undefined')
                {
                    var coordinates = resultPoint[ui.item.value].split(' ');
                    $('#Event_latitude').val(coordinates[1]);
                    $('#Event_longitude').val(coordinates[0]);
                }
            },
            minLength: 1,
            appendTo: '#Event_autocomplete'
        });
        
        $().change();
    ");

if (!$model->isNewRecord && $model->relaxId && $model->status == Event::STATUS_WAITING) {
    $cs->registerScript('event_publish', "
        $('#btnSaveAndPublish').click(function(){
            if(confirm('".Yii::t('application', 'Все ошибки исправлены? Можно публиковать?')."')) {
                $('#relaxSaveAndPublish').val(1);
                return true;
            }
            return false;
        });
    "); 
}
$relaxErrorString = '';
if ($model->relaxParsingErrors) {
    $relaxErrorsData = EventHelper::getRelaxErrors($model->relaxParsingErrors);
    $relaxErrorString = '<h5>'.Yii::t('application', 'Ошибки парсинга').'</h5>';
    foreach ($relaxErrorsData as $label => $error) {
        $relaxErrorString .= '<p>'.TbHtml::labelTb($label, array('color' => TbHtml::LABEL_COLOR_DEFAULT)).' - '.$error.'</p>';
    }
    $relaxErrorString = TbHtml::alert(TbHtml::ALERT_COLOR_ERROR, $relaxErrorString, array('closeText' => false));
}
?>

<div class="form">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'product-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>

    <?php print $form->errorSummary($model); ?>
    <?php /* @var $form TbActiveForm */ ?>

    <?php if (!$model->isNewRecord && $model->relaxId): ?>
        <?php echo TbHtml::uneditableFieldControlGroup(Yii::t('application', 'Взято с Relax.by. Оригинал - ').TbHtml::link($model->relaxUrl, $model->relaxUrl, array('target' => '_blank')), array('span' => 8)); ?>
    <?php endif; ?>
    
    <?php print $relaxErrorString; ?>

    <?php echo $form->textFieldControlGroup($model, 'publisherName', array('span' => 8, 'maxlength' => 255)); ?>
    <?php echo $form->textFieldControlGroup($model, 'name', array('span' => 8, 'maxlength' => 255)); ?>
    <?php echo $form->dropDownListControlGroup($model, 'category', array_combine(Yii::app()->params['eventCategories'], Yii::app()->params['eventCategories']), array('span' => 8)); ?>

    <?php print $form->fileFieldControlGroup($model, 'imageFile', array('span' => 8)); ?>
    <?php if ($model->image): ?>
        <div>
            <?php print TbHtml::imagePolaroid($model->image, '', array('class' => 'span8', 'style' => 'margin-left: 0px;')); ?>
            <div class="clearfix"></div>
        </div> 
    <?php endif; ?>

    <?php echo $form->textAreaControlGroup($model, 'description', array('rows' => 6, 'span' => 8)); ?>

    <div>
        <?php echo $form->textFieldControlGroup($model, 'city', array('span' => 2, 'groupOptions' => array('class' => 'pull-left', 'style' => 'margin-right: 10px;'))); ?>
        <?php echo $form->textFieldControlGroup($model, 'street', array('rows' => 6, 'span' => 5, 'groupOptions' => array('class' => 'pull-left', 'style' => 'margin-right: 10px;'))); ?>
        <?php echo $form->textFieldControlGroup($model, 'houseNumber', array('span' => 1, 'maxlength' => 10, 'groupOptions' => array('class' => 'pull-left'))); ?>
        <div class="clearfix"></div>
    </div>

    <div>
        <?php echo $form->textFieldControlGroup($model, 'latitude', array('span' => 3, 'groupOptions' => array('class' => 'pull-left', 'style' => 'margin-right: 10px;'))); ?>
        <?php echo $form->textFieldControlGroup($model, 'longitude', array('span' => 3, 'groupOptions' => array('class' => 'pull-left'))); ?>
        <div class="clearfix"></div>
    </div>

    <?php echo $form->dropDownListControlGroup($model, 'productId', $products, array('span' => 8, 'maxlength' => 11)); ?>

    <?php echo $form->textFieldControlGroup($model, 'dateStart', array('span' => 8, 'maxlength' => 10, 'placeholder' => date('d.m.Y'))); ?>

    <?php echo $form->textFieldControlGroup($model, 'timeStart', array('span' => 8, 'maxlength' => 5, 'placeholder' => date('H:i'))); ?>

    <?php echo $form->textFieldControlGroup($model, 'timeEnd', array('span' => 8, 'maxlength' => 5, 'placeholder' => date('H:i', strtotime('+2 hours')))); ?>
    
    <div class="form-actions">
        <?php if(!$model->isNewRecord && $model->relaxId && $model->status == Event::STATUS_WAITING): ?>
            <?php
            echo TbHtml::submitButton(Yii::t('application', 'Сохранить и опубликовать'), array(
                'id' => 'btnSaveAndPublish',
                'color' => TbHtml::BUTTON_COLOR_SUCCESS,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
            ?>
            <?php echo $form->hiddenField($model, 'relaxSaveAndPublish', array('id' => 'relaxSaveAndPublish')); ?>
        <?php endif; ?>
        <?php
        echo TbHtml::submitButton($model->isNewRecord?Yii::t('application', 'Добавить'):Yii::t('application', 'Сохранить'), array(
            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
            'size' => TbHtml::BUTTON_SIZE_LARGE,
        ));
        ?>
        <?php
        echo TbHtml::link(Yii::t('application', 'Отмена'), $this->createUrl('index'));
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<div id="Event_autocomplete"></div>