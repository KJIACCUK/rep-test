<?php

    class ChatHelper
    {

        private static $_smileReplacement;

        public static function exportMessage(UserMessage $message, $replaceSmilesToUtfCodes = false)
        {
            return array(
                'userMessageId' => (int)$message->userMessageId,
                'userId' => (int)$message->userId,
                'recipientId' => (int)$message->recipientId,
                'message' => (string)($replaceSmilesToUtfCodes?self::replaceSmilesToUtfCodes($message->message):$message->message),
                'isReaded' => (bool)$message->isReaded,
                'dateCreated' => date(Yii::app()->params['dateTimeFormat'], $message->dateCreated),
            );
        }

        public static function replaceSmilesToUtfCodes($message)
        {
            $search = array(
                ':smile:', ':)', ':=)', ':-)',
                ':stuck_out_tongue:',
                ':hushed:',
                ':angry:', ":@", ":-@", ":=@", "x(", "x-(", "x=(", "X(", "X-(", "X=(",
                ':relaxed:',
                ":cry:", ";(", ";-(", ";=(",
                ':rage:',
                ':imp:',
                ':innocent:',
                ":heart:", "(h)", "<3", "(H)", "(l)", "(L)"
            );

            return str_replace($search, self::getSmilesReplacement(), $message);
        }

        private static function getSmilesReplacement()
        {
            if(!self::$_smileReplacement)
            {
                $replacement = array('U+263A', 'U+263A', 'U+263A', 'U+263A',
                    'U+1F61C',
                    'U+1F62F',
                    'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620', 'U+1F620',
                    'U+1F60A',
                    'U+1F622', 'U+1F622', 'U+1F622', 'U+1F622',
                    'U+1F621',
                    'U+1F47F',
                    'U+1F607',
                    'U+2764', 'U+2764', 'U+2764', 'U+2764', 'U+2764', 'U+2764'
                );
                
                foreach($replacement as $item)
                {
                    self::$_smileReplacement[] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/", "&#x\\1;", $item), ENT_NOQUOTES, 'UTF-8');
                }
            }

            return self::$_smileReplacement;
        }
    }
    