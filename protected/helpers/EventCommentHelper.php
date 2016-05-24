<?php

    class EventCommentHelper
    {

        public static function export(EventComment $comment, $image)
        {
            return array(
                'commentId' => (int)$comment->eventCommentId,
                'publisherName' => (string)$comment->user->name,
                'userId' => (int)$comment->userId,
                'image' => CommonHelper::getImageLink($comment->user->image, $image),
                'content' => (string)$comment->content,
                'dateCreated' => date(Yii::app()->params['dateTimeFormat'], $comment->dateCreated)
            );
        }

        public static function getComments(Event $event, $offset, $limit, $image)
        {
            $data = array();
            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array('eventId' => $event->eventId));
            $criteria->with = array('user');
            $criteria->offset = $offset;
            $criteria->limit = $limit;
            $criteria->order = 'dateCreated DESC';
            $comments = EventComment::model()->findAll($criteria);
            foreach($comments as $item)
            {
                /* @var $item EventComment */
                $data[] = self::export($item, $image);
            }

            return $data;
        }

    }
    