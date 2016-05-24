<?php

    class ChatController extends WebController
    {
        public $messagesLimit = 50;
        
        /**
         * @return array action filters
         */
        public function filters()
        {
            return array(
                'accessControl',
                array('ProfileIsFilledFilter')
            );
        }

        /**
         * Specifies the access control rules.
         * This method is used by the 'accessControl' filter.
         * @return array access control rules
         */
        public function accessRules()
        {
            return array(
                array('allow',
                    'actions' => array('index', 'dialog'),
                    'roles' => array('user'),
                ),
                array('deny', // deny all users
                    'users' => array('*'),
                ),
            );
        }

        public function actionIndex()
        {
            $currentUser = $this->getUser();
            $friends = array();

            foreach($currentUser->friends as $user)
            {
                $data = UserHelper::export($user, 'online, friends');
                $data['image'] = CommonHelper::getImageLink($user->image, '82x80');
                $friends[] = $data;
            }
            
            $unreadedMessagesByUserCount = array();
            
            $messages = UserMessage::model()->findAllByAttributes(array('recipientId' => $currentUser->userId, 'isReaded' => 0));
            foreach($messages as $item)
            {
                /* @var $item UserMessage */
                if(!isset($unreadedMessagesByUserCount[$item->userId]))
                {
                    $unreadedMessagesByUserCount[$item->userId] = 0;
                }
                $unreadedMessagesByUserCount[$item->userId]++;
            }

            $this->render('index', array(
                'friends' => $friends,
                'unreadedMessagesByUserCount' => $unreadedMessagesByUserCount
            ));
        }
        
        public function actionDialog()
        {
            $currentUser = $this->getUser();
            $recipient = User::model()->findByPk(Web::getParam('userId'));
            /* @var $recipient User */
            
            $criteria = new CDbCriteria();
            $criteria->index = 'userMessageId';
            $criteria->addCondition('(userId = :userId AND recipientId = :recipientId) OR (userId = :recipientId AND recipientId = :userId)');
            $criteria->offset = Web::getParam('offset', 0);
            $criteria->limit = Web::getParam('limit', $this->messagesLimit);
            $criteria->order = 'dateCreated DESC';
            $criteria->params[':userId'] = $currentUser->userId;
            $criteria->params[':recipientId'] = $recipient->userId;
            
            $messages = UserMessage::model()->findAll($criteria);
            $messageIds = array_keys($messages);
            
            $readedCriteria  = new CDbCriteria();
            $readedCriteria->addInCondition('userMessageId', $messageIds);
            $readedCriteria->addColumnCondition(array('recipientId' => $currentUser->userId));
            UserMessage::model()->updateAll(array('isReaded' => 1), $readedCriteria);
            
            $messages = array_reverse($messages);
            
            if(Yii::app()->request->isAjaxRequest)
            {
                $this->renderPartial('_dialog_items', array('messages' => $messages, 'recipient' => $recipient));
            }
            else
            {
                $this->render('dialog', array('messages' => $messages, 'recipient' => $recipient));
            }
        }

    }
    