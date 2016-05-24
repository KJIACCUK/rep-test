<?php
    /* @var $this EventController */
    /* @var $model Event */
    /* @var $cs CClientScript */
    
    $this->setPageTitle(Yii::t('application', 'Редактировать мероприятие'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    $cs->registerScriptFile('/js/cropper.min.js');
    $cs->registerCssFile($cs->getCoreScriptUrl() . '/jui/css/base/jquery-ui.css'); 
    $cs->registerCssFile('/css/cropper.min.css');
    
    $cs->registerScriptFile('/js/jquery.iframe-transport.js');
    $cs->registerScriptFile('/js/jquery.fileupload.js');
    
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
        
        $('#btnUploadImageOpenDialog').click(function(){
            $('#btnUploadImage').remove();
            $('#uploadImageControls').hide();
            if(isCropperCreated)
            {
                $('#imagePreview > img').cropper('destroy');
                isCropperCreated = false;
            }
            $('#imagePreview > img').attr('src', '').hide();
            $('#uploadImageDialog').dialog('open');
            return false;
        });

        $('#btnSelectImage').click(function(){
            $('#uploadImageInput').click();
            return false;
        });
        
        var isCropperCreated = false;
            
        $('body').on('click', '#btnRotateLeft', function(){
            $('#imagePreview > img').cropper('rotate', -90);
            return false;
        });

        $('body').on('click', '#btnRotateRight', function(){
            $('#imagePreview > img').cropper('rotate', 90);
            return false;
        });

        $('#uploadImageInput').fileupload({
            url: '".$this->createUrl('event/saveImage', array('eventId' => $model->eventId))."',
            dataType: 'json',
            acceptFileTypes: /(\.|\/)(jpg|jpe|jpeg|png)$/i,
            maxFileSize: 2 * 1024 * 1024,
            add: function (e, data) {
                if (data.files && data.files[0]){
                    var selectedImage = data.files[0];
                    if(selectedImage.type == 'image/jpeg' || selectedImage.type == 'image/png' )
                    {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#imagePreview > img').attr('src', e.target.result).show();
                            $('#uploadImageControls').show();

                            if(isCropperCreated)
                            {
                                $('#imagePreview > img').cropper('destroy');
                                $('#btnUploadImage').remove();
                            }
                            $('#imagePreview > img').cropper({
                                aspectRatio: 740 / 555,
                                minWidth: 740,
                                minHeight: 555,
                                zoomable: false,
                                done: function(cropperData) {
                                    data.formData = {
                                        'cropper[x]': cropperData.x,
                                        'cropper[y]': cropperData.y,
                                        'cropper[width]': cropperData.width,
                                        'cropper[height]': cropperData.height,
                                        'cropper[rotate]': cropperData.rotate,
                                    };
                                }
                            });
                            isCropperCreated = true;

                            $('<button id=\"btnUploadImage\" class=\"but_blue\" type=\"button\">".Yii::t('application', 'Сохранить')."</button>')
                                .appendTo('#uploadImageButtons')
                                .click(function(){
                                    data.submit();
                                    return false;
                                });
                        }
                        reader.readAsDataURL(data.files[0]);
                    }
                    else
                    {
                        alertError('".Yii::t('application', 'Выбранный файл не является изображением. Разрешены файлы с расширением png, jpg, jpe, jpeg')."');
                    }
                }
            },
            start: function (e) {
                $('#uploadImageControls').hide();
                $('#uploadImageButtons').hide();
                $('#uploadImageProgressBar span').css('width', '0%');
                $('#uploadImageProgressBar').show();
            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#uploadImageProgressBar span').css('width', progress + '%');
            },
            done: function (e, data) {

                result = data.result;

                if(typeof(result['success']) != 'undefined')
                {
                    if(result.success)
                    {
                        alertSuccess('".Yii::t('application', 'Изображение обновлено.')."');
                        $('#eventImageView').attr('src', result.image);
                    }
                    else
                    {
                        alertError(result.message);
                    }
                }
                else
                {
                    alertError('".Yii::t('application', 'Во время загрузки произошла ошибка. Попробуйте еще раз.')."');
                }
            },
            fail: function (e, data) {
                alertError('".Yii::t('application', 'Во время загрузки произошла ошибка. Попробуйте еще раз.')."');
            },
            always: function (e, data) {
                $('#uploadImageProgressBar').hide();
                $('#uploadImageControls').hide();
                $('#uploadImageButtons').show();
                $('#imagePreview > img').cropper('destroy');
                isCropperCreated = false;
                $('#imagePreview > img').attr('src', '').hide();
                $('#uploadImageDialog').dialog('close');
            }
        });

    ");
    
    if($model->status == Event::STATUS_WAITING)
    {
        $cs->registerScript('block_save', "
            $('#btnSave').click(function(){
                alertError('".Yii::t('application', 'Нельзя редактировать мероприятие, пока оно не прошло модерацию.')."');
                return false;
            });

        ");
    }
    
?>
<ul class="navi_menu">
    <li>
        <a href="<?php print $this->createUrl('event/index'); ?>"><?php print Yii::t('application', 'Мероприятия'); ?></a>
    </li>
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Мои Мероприятия'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('events/add'); ?>"><?php print Yii::t('application', 'Добавить'); ?></a>
    </li>
</ul>
<div class="mir_add_bl">
    
    <div class="title_l"><?php print Yii::t('application', 'Редактировать мероприятие'); ?></div>
    <div class="line_reg"></div>
    
    <img id="eventImageView" src="<?php print CommonHelper::getImageLink($model->image, '740x448'); ?>" alt=""/>
    <div id="upladImage">
        <a id="btnUploadImageOpenDialog" href="#"><?php print Yii::t('application', 'Загрузить изображение'); ?></a>
    </div>
    
    
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
        <?php print $form->dropDownList($model, 'dateStartYear', array_combine(range(1900, date('Y', strtotime('+5 year'))), range(1900, date('Y', strtotime('+5 year')))), array('class' => 'select-field')); ?>
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
    
    <?php print CHtml::submitButton(Yii::t('application', 'Сохранить и отправить на модерацию'), array('id' => 'btnSave', 'class' => 'but_blue add_mod')); ?>
    <a href="<?php print $this->createUrl('event/myEvents'); ?>" class="btn-cancel"><?php print Yii::t('application', 'Отмена'); ?></a>
    <div class="clr"></div>
    
    <?php $this->endWidget(); ?>
    
</div>
<div id="Event_autocomplete"></div>


<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'uploadImageDialog',
        'scriptFile' => false,
        // additional javascript options for the dialog plugin
        'options' => array(
            'title' => Yii::t('application', 'Загрузить изображение'),
            'autoOpen' => false,
            'modal' => true,
            'resizable' => false,
            'draggable' => false,
            'width' => 760,
            'height' => 680
        ),
    ));
    ?>

<div id="uploadImageControls" style="display: none;">
    <a id="btnRotateLeft" src="#"><img src="/images/rotate-left.png"></a>
    <a id="btnRotateRight" src="#"><img src="/images/rotate-right.png"></a>
    <div style="clear: both;"></div>
</div>
<div id="imagePreview">
    <img style="display: none;" />
</div>
<div id="uploadImageButtons">
    <button id="btnSelectImage" class="but_blue" type="button"><?php print Yii::t('application', 'Выбрать изображение'); ?></button>
</div>
<div id="uploadImageLoading">
    <input id="uploadImageInput" style="display: none;" type="file" name="Event[imageFile]">
    <div id="uploadImageProgressBar" class="meter" style="display: none; margin: auto;">
        <span style="width: 0%"></span>
    </div>
</div>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>