<?php

    class EmailHelper
    {
        const TYPE_REGISTRATION = 'registration';
        const TYPE_SOCIAL_REGISTRATION = 'social_registration';
        const TYPE_RESET_PASSWORD = 'reset_password';
        const TYPE_FEEDBACK = 'feedback';
        const TYPE_DAILY_NOTIFICATIONS = 'daily_notifications';
        const TYPE_PURCHASE_WITH_SERTIFICATE = 'purchase_with_sertificate';
        const TYPE_PURCHASE_WITH_RECEIPT_ADDRESS = 'purchase_with_receipt_address';
        const TYPE_PURCHASE_WITH_DELIVERY = 'purchase_with_delivery';
        const TYPE_VERIFICATION_APPROVED = 'verification_approved';
        const TYPE_VERIFICATION_DECLINED = 'verification_declined';
        
        public static function send($email, $type, $params, $attachments = array())
        {
            $message = new YiiMailMessage();
            $message->view = $type;
            $message->setBody($params, 'text/html');
            $message->subject = self::getSubject($type);
            if(!is_array($email))
            {
                $email = array($email);
            }
            
            if($attachments)
            {
                foreach($attachments as $attach)
                {
                    $message->attach(Swift_Attachment::fromPath(Yii::getPathOfAlias('webroot').$attach));
                }
            }
            
            foreach($email as $e)
            {
                $message->addTo($e);
            }
            
            $message->from = Yii::app()->params['noReplyEmail'];
            return Yii::app()->mail->send($message);
        }
        
        private static function getSubject($type)
        {
            $subject = '';
            switch($type)
            {
                case self::TYPE_REGISTRATION:
                    $subject = Yii::t('application', 'Регистрация нового участника в приложении БУДУТАМ');
                    break;
                
                case self::TYPE_RESET_PASSWORD:
                    $subject = Yii::t('application', 'Восстановление пароля участника в приложении БУДУТАМ');
                    break;
                
                case self::TYPE_SOCIAL_REGISTRATION:
                    $subject = Yii::t('application', 'Регистрация нового участника в приложении БУДУТАМ');
                    break;
                
                case self::TYPE_FEEDBACK:
                    $subject = Yii::t('application', 'Сообщение о проблеме в приложении БУДУТАМ (Android|FB)');
                    break;
                
                case self::TYPE_DAILY_NOTIFICATIONS:
                    $subject = Yii::t('application', 'Ваши новые события в приложении БУДУТАМ');
                    break;
                
                case self::TYPE_PURCHASE_WITH_SERTIFICATE:
                    $subject = Yii::t('application', 'Заказ электронного товара в бонусном магазине приложения БУДУТАМ');
                    break;
                
                case self::TYPE_PURCHASE_WITH_RECEIPT_ADDRESS:
                    $subject = Yii::t('application', 'Заказ товара в бонусном магазине приложения БУДУТАМ без доставки');
                    break;
                
                case self::TYPE_PURCHASE_WITH_DELIVERY:
                    $subject = Yii::t('application', 'Заказ товара в бонусном магазине приложения БУДУТАМ с доставкой');
                    break;
                
                case self::TYPE_VERIFICATION_APPROVED:
                    $subject = Yii::t('application', 'Верификация в приложении БУДУТАМ');
                    break;
                
                case self::TYPE_VERIFICATION_DECLINED:
                    $subject = Yii::t('application', 'Верификация в приложении БУДУТАМ');
                    break;
            }
            
            return $subject;
        }
    }
    