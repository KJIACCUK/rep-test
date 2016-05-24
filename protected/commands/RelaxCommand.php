<?php

class RelaxCommand extends CConsoleCommand
{
    const RELAX_API_URL = 'http://api.relax.by/v3/json';
    const EVENT_TYPE_CONCERT = 'concert';
    const EVENT_TYPE_PARTY = 'party';
    const RELAX_CONCERT_ID = 340;
    const RELAX_PARTY_ID = 309;
    const EVENT_PARSING_PERIOD = 1209600;

    private $displayMessages;

    /**
     *
     * @var City[]
     */
    private $_cities = array();

    public function actionIndex()
    {
        return 'Budutam relax import command';
    }

    public function actionImport($displayMessages = false)
    {
        set_time_limit(0);
        $this->displayMessages = (bool)$displayMessages;
        $this->importCities();
        $this->importEvents(self::EVENT_TYPE_CONCERT);
        $this->importEvents(self::EVENT_TYPE_PARTY);
    }

    private function importCities()
    {
        $this->message('Import cities:');
        $found = 0;
        $exists = 0;
        $imported = 0;
        $errors = 0;

        if (($data = $this->doRequest('/geo/getCities/')) && isset($data['cities'])) {
            foreach ($data['cities'] as $item) {
                if (isset($item['id']) && isset($item['name']) && isset($item['latitude']) && isset($item['longitude'])) {
                    if (in_array($item['name'], array('Минск', 'Гомель', 'Гродно', 'Витебск', 'Брест', 'Могилев'))) {
                        $found++;
                        if (($city = City::model()->findByAttributes(array('name' => $item['name'])))) {
                            /* @var $city City */
                            $city->relaxCityId = $item['id'];
                            $this->_cities[] = $city;
                            $exists++;
                        } elseif (($city = YandexMapsHelper::getCityByCoordinates($item['latitude'], $item['longitude']))) {
                            /* @var $city City */
                            $city->relaxCityId = $item['id'];
                            $this->_cities[] = $city;
                            $imported++;
                        } else {
                            $this->log('Cant find or import city - "'.$item['name'].'"', $item);
                            $errors++;
                        }
                    }
                } else {
                    $this->log('Wrong city format', $item);
                    $errors++;
                }
            }
        }

        $this->message('Import cities done. Found - '.$found.', already exists - '.$exists.', imported - '.$imported.', errors - '.$errors.'.');
    }

    private function importEvents($type)
    {
        $params = array(
            'rubricId' => '',
            'cityId' => '',
            'date' => mktime(date('H'), 0, 0, date('n'), date('j'), date('Y')),
            'count' => 20,
            'offset' => 0
        );
        $now = time();
        switch ($type) {
            case self::EVENT_TYPE_CONCERT:
                $category = 'Концерт';
                $params['rubricId'] = self::RELAX_CONCERT_ID;
                break;
            case self::EVENT_TYPE_PARTY:
                $category = 'Вечеринка';
                $params['rubricId'] = self::RELAX_PARTY_ID;
                break;
        }

        foreach ($this->_cities as $city) {
            $params['cityId'] = $city->relaxCityId;
            $params['offset'] = 0;
            $contunueRequesting = true;
            $found = 0;
            $exists = 0;
            $imported = 0;
            $errors = 0;
            switch ($type) {
                case self::EVENT_TYPE_CONCERT:
                    $this->message('Import concerts ('.$city->name.'):');
                    break;
                case self::EVENT_TYPE_PARTY:
                    $this->message('Import parties ('.$city->name.'):');
                    break;
            }
            while ($contunueRequesting && ($data = $this->doRequest('/afisha/getEventsList/', $params))) {
                if (isset($data['events'])) {
                    foreach ($data['events'] as $item) {
                        if (isset($item['id'])) {
                            if (isset($item['date']) && (int)$item['date'] > ($now + self::EVENT_PARSING_PERIOD)) {
                                $contunueRequesting = false;
                                continue;
                            }
                            $found++;
                            if (
                            Event::model()->exists('relaxId = :relaxId', array(':relaxId' => $item['id'])) ||
                            EventRelaxDeleted::model()->exists('relaxId = :relaxId', array(':relaxId' => $item['id']))
                            ) {
                                $exists++;
                            } elseif ($this->importEventDetail($item, $city, $category)) {
                                $imported++;
                            } else {
                                $this->log('Cant find or import event - "'.$item['id'].'"', $item);
                                $errors++;
                            }
                        } else {
                            $this->log('Wrong event item format', $item);
                            $errors++;
                        }
                    }

                    $params['offset'] += $params['count'];
                } else {
                    $this->log('Wrong events list format', $data);
                    $contunueRequesting = false;
                }
            }
            
            switch ($type) {
                case self::EVENT_TYPE_CONCERT:
                    $this->message('Import concerts ('.$city->name.') done. Found - '.$found.', already exists - '.$exists.', imported - '.$imported.', errors - '.$errors.'.');
                    break;
                case self::EVENT_TYPE_PARTY:
                    $this->message('Import parties ('.$city->name.') done. Found - '.$found.', already exists - '.$exists.', imported - '.$imported.', errors - '.$errors.'.');
                    break;
            }
        }
    }

    /**
     * 
     * @param array $listItem
     * @param City $city
     * @param string $category
     * @return boolean
     */
    private function importEventDetail($listItem, $city, $category)
    {
        $params = array(
            'eventId' => $listItem['id'],
            'cityId' => $city->relaxCityId
        );

        if (($data = $this->doRequest('/afisha/getEvent/', $params)) && isset($data['event'])) {
            $event = $data['event'];
            if (isset($event['id']) && isset($event['title']) && isset($event['url'])) {

                $relaxParsingErrors = array();

                $newEvent = new Event('relax_insert');
                $newEvent->category = $category;
                $newEvent->cityId = $city->cityId;
                $newEvent->relaxId = $listItem['id'];
                $newEvent->relaxUrl = $event['url'];
                $newEvent->isGlobal = 1;
                $newEvent->isPublic = 1;
                $newEvent->status = Event::STATUS_WAITING;
                $newEvent->image = EventHelper::getDefaultImage();
                $newEvent->dateCreated = time();

                if (strlen($event['title']) > 255) {
                    $newEvent->name = mb_substr($event['title'], 0, 255);
                    $relaxParsingErrors['name'] = 'TOO_LONG';
                } else {
                    $newEvent->name = $event['title'];
                }

                $image = null;
                if (isset($event['poster'])) {
                    $image = $this->downloadEventImage($event['poster']);
                }

                if (isset($event['params']) && is_array($event['params'])) {
                    foreach ($event['params'] as $item) {
                        if (isset($item['key']) && isset($item['value']) && $item['key'] == 'Описание') {
                            $newEvent->description = trim($item['value']);
                        }
                    }
                }

                if (isset($listItem['date'])) {
                    $newEvent->dateStart = (int)$listItem['date'];
                    if (isset($listItem['times']) && is_array($listItem['times']) && !empty($listItem['times'])) {
                        $newEvent->dateStart += (int)$listItem['times'][0];
                        $newEvent->timeStart = date('H:i', $newEvent->dateStart);
                    }
                } elseif (isset($event['timetable']) && isset($event['timetable']['dates']) && is_array($event['timetable']['dates']) && !empty($event['timetable']['dates'])) {
                    if (isset($event['timetable']['dates'][0]['from'])) {
                        $newEvent->dateStart = $event['timetable']['dates'][0]['from'];
                        if (isset($event['timetable']['dates'][0]['sessions']) && is_array($event['timetable']['dates'][0]['sessions']) && !empty($event['timetable']['dates'][0]['sessions'])) {
                            if (isset($event['timetable']['dates'][0]['sessions'][0]['time'])) {
                                $newEvent->dateStart = $event['timetable']['dates'][0]['from'] + $event['timetable']['dates'][0]['sessions'][0]['time'];
                                $newEvent->timeStart = date('H:i', $newEvent->dateStart);
                            }
                        }
                    }
                }

                if (isset($event['timetable'])) {
                    if (isset($event['timetable']['place'])) {
                        if (isset($event['timetable']['place']['title'])) {
                            if (strlen($event['timetable']['place']['title']) > 255) {
                                $newEvent->publisherName = mb_substr($event['timetable']['place']['title'], 0, 255);
                                $relaxParsingErrors['publisherName'] = 'TOO_LONG';
                            } else {
                                $newEvent->publisherName = $event['timetable']['place']['title'];
                            }
                        }

                        if (isset($event['timetable']['place']['address']) && isset($event['timetable']['place']['address']['text'])) {
                            if (($address = YandexMapsHelper::findPlaceByAddress($event['timetable']['place']['address']['text']))) {
                                $newEvent->street = $address[0];
                                $newEvent->houseNumber = $address[1];
                                $newEvent->latitude = $address[2];
                                $newEvent->longitude = $address[3];
                            }
                        }
                    }
                }

                if (empty($image)) {
                    $relaxParsingErrors['image'] = 'NO_DATA';
                }

                if (empty($newEvent->description)) {
                    $newEvent->description = 'NO_DATA';
                    $relaxParsingErrors['description'] = 'NO_DATA';
                }

                if (empty($newEvent->publisherName)) {
                    $newEvent->publisherName = 'NO_DATA';
                    $relaxParsingErrors['publisherName'] = 'NO_DATA';
                }

                if (empty($newEvent->street)) {
                    $relaxParsingErrors['street'] = 'NO_DATA';
                }

                if (empty($newEvent->latitude)) {
                    $relaxParsingErrors['latitude'] = 'NO_DATA';
                    $newEvent->latitude = $city->latitude;
                }

                if (empty($newEvent->longitude)) {
                    $relaxParsingErrors['longitude'] = 'NO_DATA';
                    $newEvent->longitude = $city->longitude;
                }

                if (empty($newEvent->dateStart)) {
                    $newEvent->dateStart = 0;
                    $relaxParsingErrors['dateStart'] = 'NO_DATA';
                }

                if (empty($newEvent->timeStart)) {
                    $newEvent->timeStart = '00:00';
                    $relaxParsingErrors['timeStart'] = 'NO_DATA';
                }

                if (!empty($relaxParsingErrors)) {
                    $newEvent->relaxParsingErrors = CJSON::encode($relaxParsingErrors);
                } else {
                    $newEvent->status = Event::STATUS_APPROVED;
                }

                $transaction = Yii::app()->db->beginTransaction();
                if (!$newEvent->save()) {
                    $this->log('Cant save event, relax ID - '.$listItem['id'], $newEvent->getErrors());
                    $transaction->rollback();
                    return false;
                }

                if (!EventGalleryHelper::createDefaultAlbum($newEvent)) {
                    $this->log('Cant save event default album, relax ID - '.$listItem['id'], $newEvent->getErrors());
                    $transaction->rollback();
                    return false;
                }

                if ($image) {
                    Yii::setPathOfAlias('webroot', Yii::app()->basePath.DIRECTORY_SEPARATOR.'..');
                    $imagePath = Yii::getPathOfAlias('webroot.content.images.events').'/'.CommonHelper::generateImageName($newEvent->eventId).substr($event['poster'], strrpos($event['poster'], '.'));
                    $imagePath = str_replace('\\', '/', $imagePath);

                    if (!file_put_contents($imagePath, $image)) {
                        $this->log('Cant write image file', $imagePath);
                        $transaction->rollback();
                        return false;
                    }

                    $imageFile = WideImage::load($imagePath);
                    $bgColor = $imageFile->allocateColor(32, 39, 78);

                    if ($imageFile->getWidth() < 740) {
                        $imageFile = $imageFile->resizeCanvas(740, 555, 'center', 'center', $bgColor, 'up');
                        $imageFile->saveToFile($imagePath);
                    }

                    $newEvent->image = str_replace(Yii::getPathOfAlias('webroot'), '', $imagePath);
                    $newEvent->save(false);
                }
                
                $transaction->commit();
                sleep(1);
                return true;
            } else {
                $this->log('Wrong event detail format', $event);
            }
        } else {
            $this->log('Event '.$listItem['id'].' not found', $data);
        }

        return false;
    }

    private function downloadEventImage($url)
    {
        $httpClient = new EHttpClient($url, array(
            'maxredirects' => 0,
            'timeout' => 30));

        $response = $httpClient->request('GET');

        if ($response->isSuccessful()) {
            return $response->getRawBody();
        }

        return null;
    }

    private function doRequest($uri, $params = array())
    {
        $httpClient = new EHttpClient(self::RELAX_API_URL.$uri, array(
            'maxredirects' => 0,
            'timeout' => 30));

        if ($params) {
            $httpClient->setParameterGet($params);
        }
        $response = $httpClient->request('GET');

        if ($response->isError()) {
            $this->log('Relax API request error', array('uri' => $uri, 'params' => $params, 'response' => $response->getRawBody()));
            return null;
        }

        return CJSON::decode($response->getBody());
    }

    private function message($message)
    {
        if ($this->displayMessages) {
            print $message."\n";
        }
    }

    private function log($message, $data = array())
    {
        if ($data) {
            $message .= "\n";
            $message .= 'data:';
            $message .= "\n";
            $message .= var_export($data, true);
        }
        $this->message($message);
        Yii::log($message, CLogger::LEVEL_ERROR, 'relax_api');
    }
}