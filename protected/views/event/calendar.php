<?php
    /* @var $this EventController */
    /* @var $events array */
    /* @var $eventCounts array */
    /* @var $year string */
    /* @var $month string */
    /* @var $cs CClientScript */
    /* @var $city City */

    $this->setPageTitle(Yii::t('application', 'Календарь'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScriptFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', CClientScript::POS_HEAD);

    $cities = CityHelper::getCities();
    $cityNames = array();
    foreach($cities as $item)
    {
        $cityNames[$item['name']] = $item['name'];
    }
    
    $cs->registerScript('ymaps', "
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
                                        $('#calendarFilterCity option').each(function(){
                                            if($(this).val() == foundedCity && $(this).prop('selected') == false)
                                            {
                                                $(this).prop('selected', true);
                                                $('#calendarFilterCity').trigger('refresh');
                                                $('#calendarFilterCity').trigger('change');
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
        }
        
        ymaps.ready(init);
    ");
    
    $cs->registerScript('calendar', "

        $('#calendarTable').on('click', '.calendarDay', function(){
            $('.wind_com').hide();
            if(!$(this).hasClass('old_m'))
            {
                var tooltip = $(this).find('.wind_com');
                tooltip.show();
            }
            
            return false;
        });
        
        $('#calendarTable').on('click', '.event-link', function(){
            window.location.href = $(this).attr('href');
            return false;
        });
        
        $(document).on('click', ':not(.calendarDay)', function(){
            $('.wind_com').hide();
            return true;
        });
        
        $('.calendar-filter').change(function(){
            var city = $('#calendarFilterCity').val();
            var month = $('#calendarFilterMonth').val();
            var year = $('#calendarFilterYear').val();

            $.ajax({
                url: '".$this->createUrl('event/calendar')."',
                type: 'GET',
                data: {'city': city, 'month': month, 'year': year},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                    $('#calendarTable').html(data);
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                }
            });
        });
        
    ");
?>
<div class="title_l top_pad">
    <?php print Yii::t('application', 'Календарь'); ?>
</div>
<div class="line_reg"></div>

<div style="margin-bottom: 20px;">
    <div class="black_t" style="float: left;">
        <?php print CHtml::dropDownList('city', $city->name, $cityNames, array('id' => 'calendarFilterCity', 'class' => 'calendar-filter select-field')); ?>
    </div>
    <div class="date_r min_tx black_t" style="float: right;">
        <?php print CHtml::dropDownList('month', $month, Yii::app()->locale->getMonthNames('wide', true), array('id' => 'calendarFilterMonth', 'class' => 'calendar-filter select-field')); ?>
        <?php print CHtml::dropDownList('year', $year, array_combine(range(1900, date('Y', strtotime('+5 year'))), range(1900, date('Y', strtotime('+5 year')))), array('id' => 'calendarFilterYear', 'class' => 'calendar-filter select-field')); ?>
    </div>
    <div class="clr"></div>
</div>

<div id="calendarTable">
    <?php print $this->renderPartial('_calendar_table', array('events' => $events, 'eventCounts' => $eventCounts, 'city' => $city, 'year' => $year, 'month' => $month)); ?>
</div>

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