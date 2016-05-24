<?php

    class ChatController extends ApiController
    {
        public function filters()
        {
            return array(
                array('ApiAccessControlFilter'),
                array('ApiProfileIsFilledFilter'),
                array(
                    'ExistsFilter + getMessages',
                    'param' => 'userId',
                    'function' => 'FilterHelper::checkFriendExists',
                    'errorMessage' => Yii::t('application', 'Пользователь не найден')
                ),
            );
        }
        
        public function actionGetMessages()
        {
            $currentUser = $this->getUser();
            
            $recipient = User::model()->findByPk(Api::getParam('userId'));
            /* @var $recipient User */
            
            $criteria = new CDbCriteria();
            $criteria->index = 'userMessageId';
            $criteria->addCondition('(userId = :userId AND recipientId = :recipientId) OR (userId = :recipientId AND recipientId = :userId)');
            $criteria->offset = Api::getParam('offset', 0);
            $criteria->limit = Api::getParam('limit', 50);
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
            
            $response = array();
            $response['messages'] = array();
            
            foreach($messages as $item)
            {
                /* @var $item UserMessage */
                $response['messages'][] = ChatHelper::exportMessage($item, true);
            }
            
            Api::respondSuccess($response);
        }
    }