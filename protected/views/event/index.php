<?php
    /* @var $this EventController */
    /* @var $events array */
    /* @var $cs CClientScript */
    /* @var $city City */
    /* @var $order string */
    
    $this->setPageTitle(Yii::t('application', 'Мероприятия'));
    $this->layout = '//layouts/inner';
    $cs = Yii::app()->clientScript;
    
    $cs->registerScriptFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', CClientScript::POS_HEAD);
    
    $cities = CityHelper::getCities();
    $cityNames = array();
    foreach($cities as $item)
    {
        $cityNames[$item['name']] = $item['name'];
    }
    
    $cs->registerScript('events', "
        var city = '".$city->name."';
        var order = '".$order."';
        var offset = ".$this->eventsLimit.";
        var limit = ".$this->eventsLimit.";
        var allLoaded = false;
        var isRequesting = false;
 
        function loadEvents()
        {
            if(allLoaded || isRequesting)
            {
                return false;
            }
            isRequesting = true;
            $.ajax({
                url: '".$this->createUrl('event/index')."',
                type: 'GET',
                data: {'city': city, 'offset': offset, 'limit': limit, 'order': order},
                dataType: 'html',
                success: function(data, status, xhr)
                {
                
                    $('#eventsList').append(data);
                    $('.eventOrder').removeClass('act');
                    $('#eventOrder-'+order).addClass('act');
                    offset = $('#eventsList li').length;

                    if(data.length == 0)
                    {
                        allLoaded = true;
                    }
                    isRequesting = false;
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при загрузке страницы. Попробуйте перезагрузить.')."');
                }
            });
        }

        $(window).scroll(function()
        {
            if (document.body.scrollHeight - $(this).scrollTop()  <= $(this).height())
            {
                loadEvents();
            }
        });
        
        $(document).on('fb-scroll', function(evt, info){
            if (info.viewportBottomPercent == 100)
            {
                loadEvents();
            }
        });
        
        $('#cityFilter').change(function(){
            city = $(this).val();
            $('#eventsList').empty();
            offset = 0;
            allLoaded = false;
            loadEvents();
        });
        
        $('.eventOrder').click(function(){
            order = $(this).attr('id').replace('eventOrder-', '');
            $('#eventsList').empty();
            offset = 0;
            allLoaded = false;
            loadEvents();
            return false;
        });
        
        $('#eventsList').on('click', '.but_budu', function() {
            var self = this;
            var eventId = $(this).attr('id').replace('eventSubscribe_', '');
            var url = '';
            if ($(this).hasClass('act'))
            {
                url = '".$this->createUrl('event/unsubscribe')."';
            }
            else
            {
                url = '".$this->createUrl('event/subscribe')."';
            }
            $.ajax({
                url: url,
                type: 'GET',
                data: {'eventId': eventId},
                dataType: 'json',
                success: function(data, status, xhr)
                {
                    if(typeof(data['success']) != 'undefined')
                    {
                        if (data.success)
                        {
                            if ($(self).hasClass('act'))
                            {
                                alertSuccess('".Yii::t('application', 'Вы отказались от участия в мероприятия.')."');
                                $(self).removeClass('act'); 
                            }
                            else
                            {
                                alertSuccess('".Yii::t('application', 'Вы подписались на участие в мероприятии.')."');
                                $(self).addClass('act');
                            }
                        }
                        else
                        {
                            alertError(data.message);
                        }
                    }
                    else
                    {
                        alertError('".Yii::t('application', 'Что-то произошло при обновлении подписки. Попробуйте перезагрузить страницу.')."');
                    }
                },
                error: function(xhr, status)
                {
                    alertError('".Yii::t('application', 'Что-то произошло при обновлении подписки. Попробуйте перезагрузить страницу.')."');
                }
            });

            return false;
        });

    ");
    
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
                                        $('#cityFilter option').each(function(){
                                            if($(this).val() == foundedCity && $(this).prop('selected') == false)
                                            {
                                                $(this).prop('selected', true);
                                                $('#cityFilter').trigger('refresh');
                                                $('#cityFilter').trigger('change');
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
    
?>
<ul class="navi_menu">
    <li class="act">
        <a onclick="return false;" href="#"><?php print Yii::t('application', 'Мероприятия'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('event/myEvents'); ?>"><?php print Yii::t('application', 'Мои Мероприятия'); ?></a>
    </li>
    <li>
        <a href="<?php print $this->createUrl('events/add'); ?>"><?php print Yii::t('application', 'Создать мероприятие'); ?></a>
    </li>
</ul>
<div class="filter_city black_t">
    <?php print CHtml::dropDownList('city', $city->name, $cityNames, array('id' => 'cityFilter', 'class' => 'select-field')); ?>
</div>
<div class="right_filter">
    <a id="eventOrder-<?php print EventHelper::GET_EVENTS_ORDER_DATE_START; ?>" href="#" class="eventOrder <?php print ($order == EventHelper::GET_EVENTS_ORDER_DATE_START)?' act':''; ?>"><?php print Yii::t('application', 'Ближайшие'); ?></a>
    <a id="eventOrder-<?php print EventHelper::GET_EVENTS_ORDER_SUBSCRIBERS_COUNT; ?>" href="#" class="eventOrder <?php print ($order == EventHelper::GET_EVENTS_ORDER_SUBSCRIBERS_COUNT)?' act':''; ?>"><?php print Yii::t('application', 'Массовые'); ?></a>
    <a id="eventOrder-<?php print EventHelper::GET_EVENTS_ORDER_DATE_CREATED; ?>" href="#" class="eventOrder <?php print ($order == EventHelper::GET_EVENTS_ORDER_DATE_CREATED)?' act':''; ?>"><?php print Yii::t('application', 'Новые'); ?></a>
</div>
<div class="clr"></div>
<div class="line_reg" style="margin:20px 0px 0px;"></div>
<?php if(count($events)): ?>
    <ul id="eventsList" class="list_mir">
        <?php print $this->renderPartial('_events_items', array('events' => $events)); ?>
    </ul>
<?php else: ?>
    <p><?php print Yii::t('application', 'Мероприятий нет'); ?></p>
<?php endif; ?>
