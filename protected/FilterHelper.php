<?php

    class FilterHelper
    {

        public static function checkPlatform($platform)
        {
            return in_array($platform, array(UserApiToken::PLATFORM_ANDROID, UserApiToken::PLATFORM_IOS));
        }

        public static function checkNotEmpty($value)
        {
            return !empty($value);
        }

        public static function checkImage($image)
        {
            if($image)
            {
                $image = explode('x', $image);
                if(is_array($image) && (count($image) == 2))
                {
                    return true;
                }
            }
            return false;
        }

        public static function checkSocial($social)
        {
            return in_array($social, array(UserSocial::TYPE_FACEBOOK, UserSocial::TYPE_VKONTAKTE, UserSocial::TYPE_TWITTER));
        }

        public static function checkMessenger($messenger)
        {
            return in_array($messenger, array_keys(Yii::app()->params['messengers']));
        }
        
        public static function checkCity($city)
        {
            return (bool)YandexMapsHelper::getCityByName($city);
        }
        
        public static function checkEventExists($eventId)
        {
            return (bool)Event::model()->findByPk($eventId);
        }
        
        public static function checkEventIsOwner($eventId)
        {
            $event = Event::model()->findByPk($eventId);
            /* @var $event Event */
            $controller = Yii::app()->getController();
            /* @var $controller ApiController */
            return ($event->userId == $controller->getUser()->userId);
        }
        
        public static function checkEventAlbumExists($albumId)
        {
            return (bool)EventGalleryAlbum::model()->findByPk($albumId);
        }
        
        public static function checkEventImageExists($imageId)
        {
            return (bool)EventGalleryImage::model()->findByPk($imageId);
        }
        
        public static function checkEventAlbumIsOwner($albumId)
        {
            $album = EventGalleryAlbum::model()->with('event')->findByPk($albumId);
            /* @var $album EventGalleryAlbum */
            $controller = Yii::app()->getController();
            /* @var $controller ApiController */
            return ($album->event->userId == $controller->getUser()->userId);
        }
        
        public static function checkEventImageIsOwner($imageId)
        {
            $image = EventGalleryImage::model()->with('event')->findByPk($imageId);
            /* @var $image EventGalleryImage */
            $controller = Yii::app()->getController();
            /* @var $controller ApiController */
            return ($image->event->userId == $controller->getUser()->userId);
        }
        
        public static function checkUserExists($userId)
        {
            $user = User::model()->findByPk($userId);
            return (bool)$user;
        }
        
        public static function checkFriendExists($friendId)
        {
            $controller = Yii::app()->getController();
            /* @var $controller ApiController */
            $user = UserFriend::model()->findByAttributes(array('userId' => $controller->getUser()->userId, 'friendId' => $friendId));
            return (bool)$user;
        }
        
        public static function checkResearchExists($marketingResearchId)
        {
            $research = MarketingResearch::model()->findByAttributes(array('marketingResearchId' => $marketingResearchId, 'isEnabled' => 1));
            return (bool)$research;
        }
        
        public static function checkProductExists($productId)
        {
            $product = Product::model()->findByAttributes(array('productId' => $productId, 'isActive' => 1));
            return (bool)$product;
        }
        

    }
    