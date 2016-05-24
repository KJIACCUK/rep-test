<?php
    /* @var $this EventController */
    /* @var $events array */
    /* @var $cs CClientScript */
    /* @var $city City */

    $this->setPageTitle(Yii::t('application', 'Карта'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    $cs->registerScriptFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', CClientScript::POS_HEAD);

    $cities = CityHelper::getCities();
    $citiesToJS = array();
    $cityNames = array();
    foreach($cities as $item)
    {
        $cityNames[$item['name']] = $item['name'];
        $citiesToJS[$item['name']] = $item;
    }
    
    $cs->registerScript('map', "
        var budutamMap;
        var cities = ".CJSON::encode($citiesToJS).";

        function init(){

            var geolocation = ymaps.geolocation;
            
            function findCurrentCity(coordinates)
            {
                var coordinatesString = coordinates[1]+','+coordinates[0];
                $.ajax({
                    url: 'https://geocode-maps.yandex.ru/1.x/',
                    data: {
                        'geocode': coordinatesString,
                        'lang': 'ru_RU',
                        'format': 'json',
                        'kind': 'locality',
                        'results': 50
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
                                    if(geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.CountryName == '".Yii::t('application', 'Беларусь')."')
                                    {
                                        var foundedCity = geoObject.metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.Locality.LocalityName;
                                        $('#mapFilterCity option').each(function(){
                                            if($(this).val() == foundedCity && $(this).prop('selected') == false)
                                            {
                                                $(this).prop('selected', true);
                                                $('#mapFilterCity').trigger('refresh');
                                                $('#mapFilterCity').trigger('change');
                                            }
                                        });
                                        break;
                                    }
                                }
                            }
                        }
                    },
                    dataType: 'json'
                });
            }
            
            geolocation.get({
                provider: 'yandex',
                mapStateAutoApply: true
            }).then(function (result) {
                if(typeof result.geoObjects.position !== 'undefined')
                {
                    findCurrentCity(result.geoObjects.position);
                }
                else
                {
                    geolocation.get({
                        provider: 'browser',
                        mapStateAutoApply: true
                    }).then(function (result) {
                        if(typeof result.geoObjects.position !== 'undefined')
                        {
                            findCurrentCity(result.geoObjects.position);
                        }
                    });
                }
            });

            function createMap()
            {
                var currentCity = cities[$('#mapFilterCity').val()];
                if(budutamMap)
                {
                    budutamMap.destroy();
                    budutamMap = null;
                }
                
                budutamMap = new ymaps.Map('budutamMap', {
                    center: [currentCity.center.latitude, currentCity.center.longitude], 
                    bounds: [
                                [currentCity.boundary.lowerCorner.latitude, currentCity.boundary.lowerCorner.longitude],
                                [currentCity.boundary.upperCorner.latitude, currentCity.boundary.upperCorner.longitude]
                            ]
                });
                
                budutamMap.controls
                    .remove('mapTools')
                    .remove('miniMap')
                    .remove('searchControl')
                    .remove('trafficControl')
                    .remove('typeSelector')
                    .remove('smallZoomControl')
                    .remove('scaleLine');

                ymaps.option.presetStorage.add('budutam#GlobalIcon', {
                    'iconLayout': 'default#image',
                    'iconImageHref': '".Yii::app()->request->getBaseUrl(true)."/images/map_b3.png',
                    'iconImageSize': [36, 50],
                    'iconImageOffset': [-18, -50],
                });
                
                ymaps.option.presetStorage.add('budutam#SubscribedIcon', {
                    'iconLayout': 'default#image',
                    'iconImageHref': '".Yii::app()->request->getBaseUrl(true)."/images/map_b1.png',
                    'iconImageSize': [36, 50],
                    'iconImageOffset': [-18, -50],
                });
                
                ymaps.option.presetStorage.add('budutam#MineIcon', {
                    'iconLayout': 'default#image',
                    'iconImageHref': '".Yii::app()->request->getBaseUrl(true)."/images/map_b2.png',
                    'iconImageSize': [36, 50],
                    'iconImageOffset': [-18, -50],
                });

                var objectManager = new ymaps.ObjectManager({
                    clusterize: true
                });
                
                budutamMap.geoObjects.add(objectManager);
                
                $.ajax({
                    url: '".$this->createUrl('event/map')."',
                    type: 'GET',
                    data: {'city': currentCity.name},
                    dataType: 'json',
                    success: function(data, status, xhr)
                    {
                        if(data.success)
                        {
                            objectManager.add(data.data);
                        }
                        else
                        {
                            alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                        }
                    },
                    error: function(xhr, status)
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                    }
                });
            }
            
            $('#mapFilterCity').change(function(){
                createMap();
            });

            createMap();
            
        }
        
        ymaps.ready(init);
        
    ", CClientScript::POS_HEAD);
    
?>
<div class="title_l top_pad">
    <?php print Yii::t('application', 'Интерактивная карта'); ?>
</div>
<div class="line_reg"></div>

<div style="margin-bottom: 20px;">
    <div class="black_t">
        <?php print CHtml::dropDownList('city', $city->name, $cityNames, array('id' => 'mapFilterCity', 'class' => 'select-field')); ?>
    </div>
    <div class="clr"></div>
</div>

<div id="budutamMap" class="maps_m" style="width: 740px; height: 620px;" ></div>

<ul class="colen_poyasn">
    <li>
        <div class="color_k day_red"></div>
        <?php print Yii::t('application', 'Глобальные мероприятия'); ?>
    </li>
    <li>
        <div class="color_k day_blue"></div>
        <?php print Yii::t('application', 'Подписанные мероприятия'); ?>
    </li>
    <li>
        <div class="color_k day_blue2"></div>
        <?php print Yii::t('application', 'Ваши мероприятия'); ?>
    </li>
</ul>
<div class="clr"></div>