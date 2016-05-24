<?php

    class EventGalleryHelper
    {
        public static function getDefaultAlbumImage()
        {
            return '/content/images/events/album_default.jpg';
        }

        public static function exportAlbum(EventGalleryAlbum $album, $image, $albumImage)
        {
            $data = array(
                'albumId' => (int)$album->eventGalleryAlbumId,
                'name' => (string)$album->name,
                'isDefault' => (bool)$album->isDefault,
                'albumImage' => CommonHelper::getImageLink(self::getDefaultAlbumImage(), $albumImage),
                'images' => array()
            );

            foreach($album->images as $i => $item)
            {
                $data['images'][$i] = self::exportImage($item, $image);
                if($i == 0)
                {
                    $data['albumImage'] = CommonHelper::getImageLink($data['images'][$i]['originalImage'], $albumImage);
                }
            }

            return $data;
        }

        public static function exportImage(EventGalleryImage $item, $image)
        {
            return array(
                'imageId' => $item->eventGalleryImageId,
                'eventId' => $item->eventId,
                'albumId' => $item->eventGalleryAlbumId,
                'image' => CommonHelper::getImageLink($item->image, $image),
                'originalImage' => $item->image,
                'dateCreated' => date(Yii::app()->params['dateTimeFormat'], $item->dateCreated)
            );
        }

        public static function getGallery(Event $event, $image, $albumImage, $albumId = null)
        {
            $data = array();
            $criteria = new CDbCriteria();
            $criteria->alias = 'ga';
            $criteria->with = array('images');
            $criteria->addColumnCondition(array('ga.eventId' => $event->eventId));

            if($albumId)
            {
                $criteria->addColumnCondition(array('ga.eventGalleryAlbumId' => $albumId));
            }

            $albums = EventGalleryAlbum::model()->findAll($criteria);
            foreach($albums as $item)
            {
                $data[] = EventGalleryHelper::exportAlbum($item, $image, $albumImage);
            }

            return $data;
        }
        
        public static function countPhotos($eventId)
        {
            return (int)EventGalleryImage::model()->countByAttributes(array('eventId' => $eventId));
        }
        
        /**
         * 
         * @param Event $event
         * @return EventGalleryAlbum|null
         */
        public static function createDefaultAlbum(Event $event)
        {
            $album = new EventGalleryAlbum();
            $album->eventId = $event->eventId;
            $album->name = $event->name;
            $album->isDefault = 1;
            if($album->save())
            {
                return $album;
            }
            
            return null;
        }
        
    }
    