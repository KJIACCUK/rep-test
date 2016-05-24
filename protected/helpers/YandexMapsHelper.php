<?php

class YandexMapsHelper
{

    /**
     * 
     * @param string $name
     * @return City|null
     */
    public static function getCityByName($name)
    {
        $city = City::model()->findByAttributes(array('name' => $name));
        if (!$city) {
            $httpClient = new EHttpClient('https://geocode-maps.yandex.ru/1.x/', array(
                'maxredirects' => 0,
                'timeout' => 30));

            $httpClient->setParameterGet(array(
                'geocode' => Yii::t('application', 'Беларусь, город').' '.$name,
                'lang' => 'ru_RU',
                'format' => 'json',
                'kind' => 'locality',
                'results' => 1
            ));
            
            $response = $httpClient->request('GET');

            if (!$response->isSuccessful()) {
                return null;
            }

            $geoData = CJSON::decode($response->getBody());
            if (!$geoData) {
                return null;
            }

            if (!isset($geoData['response']['GeoObjectCollection'])) {
                return null;
            }

            if (!((int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] > 0 && (int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['results'] == 1)) {
                return null;
            }

            if (!isset($geoData['response']['GeoObjectCollection']['featureMember'][0])) {
                return null;
            }

            $geoObject = $geoData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];

            if ($geoObject['metaDataProperty']['GeocoderMetaData']['kind'] != 'locality') {
                return null;
            }

            if (!($city = City::model()->findByAttributes(array('name' => $geoObject['name'])))) {
                $city = new City();
                $city->name = $geoObject['name'];

                // "longitude latitude"
                $coordinates = explode(' ', $geoObject['Point']['pos']);

                $city->latitude = $coordinates[1];
                $city->longitude = $coordinates[0];

                $lowerCornerCoordinates = explode(' ', $geoObject['boundedBy']['Envelope']['lowerCorner']);

                $city->lowerCornerLatitude = $lowerCornerCoordinates[1];
                $city->lowerCornerLongitude = $lowerCornerCoordinates[0];

                $upperCornerCoordinates = explode(' ', $geoObject['boundedBy']['Envelope']['upperCorner']);

                $city->upperCornerLatitude = $upperCornerCoordinates[1];
                $city->upperCornerLongitude = $upperCornerCoordinates[0];

                if (!$city->save()) {
                    return null;
                }
            }
        }

        return $city;
    }

    /**
     * 
     * @param string $name
     * @return City|null
     */
    public static function getCityByCoordinates($latitude, $longitude)
    {
        $city = City::model()->findByAttributes(array('latitude' => $latitude, 'longitude' => $longitude));
        if (!$city) {
            $httpClient = new EHttpClient('https://geocode-maps.yandex.ru/1.x/', array(
                'maxredirects' => 0,
                'timeout' => 30));

            $httpClient->setParameterGet(array(
                'geocode' => $longitude.','.$latitude,
                'lang' => 'ru_RU',
                'format' => 'json',
                'kind' => 'locality',
                'results' => 1
            ));

            $response = $httpClient->request('GET');

            if (!$response->isSuccessful()) {
                return null;
            }

            $geoData = CJSON::decode($response->getBody());
            if (!$geoData) {
                return null;
            }

            if (!isset($geoData['response']['GeoObjectCollection'])) {
                return null;
            }

            if (!((int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] > 0 && (int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['results'] == 1)) {
                return null;
            }

            if (!isset($geoData['response']['GeoObjectCollection']['featureMember'][0])) {
                return null;
            }

            $geoObject = $geoData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];

            if ($geoObject['metaDataProperty']['GeocoderMetaData']['kind'] != 'locality') {
                return null;
            }

            if (!($city = City::model()->findByAttributes(array('name' => $geoObject['name'])))) {
                $city = new City();
                $city->name = $geoObject['name'];

                // "longitude latitude"
                $coordinates = explode(' ', $geoObject['Point']['pos']);

                $city->latitude = $coordinates[1];
                $city->longitude = $coordinates[0];

                $lowerCornerCoordinates = explode(' ', $geoObject['boundedBy']['Envelope']['lowerCorner']);

                $city->lowerCornerLatitude = $lowerCornerCoordinates[1];
                $city->lowerCornerLongitude = $lowerCornerCoordinates[0];

                $upperCornerCoordinates = explode(' ', $geoObject['boundedBy']['Envelope']['upperCorner']);

                $city->upperCornerLatitude = $upperCornerCoordinates[1];
                $city->upperCornerLongitude = $upperCornerCoordinates[0];

                if (!$city->save()) {
                    return null;
                }
            }
        }

        return $city;
    }

    /**
     * 
     * @param string $city
     * @param string $street
     * @param string $houseNumber
     * @return array|null array(latitude, longitude)
     */
    public static function findAddressCoordinates($city, $street, $houseNumber = null)
    {
        $httpClient = new EHttpClient('https://geocode-maps.yandex.ru/1.x/', array(
            'maxredirects' => 0,
            'timeout' => 30));

        $geocode = Yii::t('application', 'Беларусь, город').' '.$city.', '.$street;
        if ($houseNumber) {
            $geocode .= ', '.$houseNumber;
        }

        $httpClient->setParameterGet(array(
            'geocode' => $geocode,
            'lang' => 'ru_RU',
            'format' => 'json',
            'kind' => 'house',
            'results' => 1
        ));

        $response = $httpClient->request('GET');

        if (!$response->isSuccessful()) {
            return null;
        }

        $geoData = CJSON::decode($response->getBody());
        if (!$geoData) {
            return null;
        }

        if (!isset($geoData['response']['GeoObjectCollection'])) {
            return null;
        }

        if (!((int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] > 0 && (int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['results'] == 1)) {
            return null;
        }

        if (!isset($geoData['response']['GeoObjectCollection']['featureMember'][0])) {
            return null;
        }

        $geoObject = $geoData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];

        if (!in_array($geoObject['metaDataProperty']['GeocoderMetaData']['precision'], array('exact', 'number', 'near', 'range', 'street'))) {
            return null;
        }

        if (!in_array($geoObject['metaDataProperty']['GeocoderMetaData']['kind'], array('house', 'street'))) {
            return null;
        }

        // "longitude latitude"
        $coordinates = explode(' ', $geoObject['Point']['pos']);

        $latitude = $coordinates[1];
        $longitude = $coordinates[0];

        return array($latitude, $longitude);
    }

    /**
     * 
     * @param string $address
     * @return array|null array(street, houseNumber, latitude, longitude)
     */
    public static function findPlaceByAddress($address)
    {
        $httpClient = new EHttpClient('https://geocode-maps.yandex.ru/1.x/', array(
            'maxredirects' => 0,
            'timeout' => 30));

        $httpClient->setParameterGet(array(
            'geocode' => $address,
            'lang' => 'ru_RU',
            'format' => 'json',
            'kind' => 'house',
            'results' => 1
        ));

        $response = $httpClient->request('GET');

        if (!$response->isSuccessful()) {
            return null;
        }

        $geoData = CJSON::decode($response->getBody());
        if (!$geoData) {
            return null;
        }

        if (!isset($geoData['response']['GeoObjectCollection'])) {
            return null;
        }

        if (!((int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['found'] > 0 && (int)$geoData['response']['GeoObjectCollection']['metaDataProperty']['GeocoderResponseMetaData']['results'] == 1)) {
            return null;
        }

        if (!isset($geoData['response']['GeoObjectCollection']['featureMember'][0])) {
            return null;
        }

        $geoObject = $geoData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];

        if (!in_array($geoObject['metaDataProperty']['GeocoderMetaData']['precision'], array('exact', 'number', 'near', 'range', 'street'))) {
            return null;
        }

        if (!in_array($geoObject['metaDataProperty']['GeocoderMetaData']['kind'], array('house', 'street'))) {
            return null;
        }

        $street = null;
        $houseNumber = null;

        if (isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']) &&
        isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']) &&
        isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']) &&
        isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']) &&
        isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']) &&
        isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['ThoroughfareName'])) {
            $street = $geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['ThoroughfareName'];
            if (isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']) &&
            isset($geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseNumber'])) {
                $houseNumber = $geoObject['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AdministrativeArea']['Locality']['Thoroughfare']['Premise']['PremiseNumber'];
            }
        }

        // "longitude latitude"
        $coordinates = explode(' ', $geoObject['Point']['pos']);

        $latitude = $coordinates[1];
        $longitude = $coordinates[0];

        return array($street, $houseNumber, $latitude, $longitude);
    }

    /**
     * 
     * @param Event[] $events
     * @return array
     */
    public static function exportEvents($events)
    {
        $currentUser = Yii::app()->getController()->getUser();
        /* @var $currentUser User */

        $result = array(
            'type' => 'FeatureCollection',
            'features' => array()
        );

        foreach ($events as $item) {
            /* @var $item Event */
            
            if ($item->status == Event::STATUS_WAITING && $item->userId != $currentUser->userId) {
                continue;
            }

            $preset = '';
            if ($item->isGlobal) {
                $preset = 'budutam#GlobalIcon';
            } elseif ($item->userId == $currentUser->userId) {
                $preset = 'budutam#MineIcon';
            } elseif ($item->isSubscribed) {
                $preset = 'budutam#SubscribedIcon';
            } else {
                continue;
            }

            if ($item->latitude && $item->longitude) {
                $data = array(
                    'id' => $item->eventId,
                    'type' => 'Feature',
                    'geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(
                            $item->latitude,
                            $item->longitude
                        )
                    ),
                    'properties' => array(
                        'hintContent' => CHtml::encode($item->name),
                        'balloonContent' => self::renderEventBalloonContent($item)
                    ),
                    'options' => array(
                        'preset' => $preset
                    )
                );

                $result['features'][] = $data;
            }
        }

        return $result;
    }

    private static function renderEventBalloonContent(Event $event)
    {
        $data = '<div class="map_popup">';
        $data .= '<i class="mp_arr"></i>';
        $data .= '<div class="mp_head"><a href="'.Yii::app()->createUrl('event/detail', array('eventId' => $event->eventId)).'">'.CHtml::encode($event->name).'</a></div>';
        $data .= '<div class="mp_date">'.date(Yii::app()->params['dateFormat'], $event->dateStart).' | '.$event->timeStart.'</div>';
        if ($event->subscribersCount) {
            $data .= '<p><a href="'.Yii::app()->createUrl('event/subscribers', array('eventId' => $event->eventId)).'">'.Yii::t('application', 'Подписано').' ('.$event->subscribersCount.' чел.)</a></p>';
        }
        if ($event->subscribersFriendsCount) {
            $data .= '<p><a href="'.Yii::app()->createUrl('event/subscribers', array('eventId' => $event->eventId, 'friends' => 1)).'">'.Yii::t('application', 'Друзей').' ('.$event->subscribersFriendsCount.' чел.)</a></p>';
        }
        $data .= '</div>';

        return $data;
    }
}