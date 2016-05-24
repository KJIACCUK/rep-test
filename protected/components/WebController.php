<?php

    abstract class WebController extends CController
    {

        /**
         *
         * @var User
         */
        protected $_user;

        public function getUser()
        {
            if(!Yii::app()->user->getIsGuest())
            {
                if(!$this->_user)
                {
                    $account = Account::model()->findByPk(Yii::app()->user->getId());
                    /* @var $account Account */
                    if($account && $account->isActive && ($account->type == Account::TYPE_USER))
                    {
                        $user = User::model()->with('account', 'friendsCount', 'pushTokens', 'socials', 'unreadedMessagesCount')->findByAttributes(array('accountId' => $account->accountId));
                        /* @var $user User */
                        $this->_user = $user;
                    }
                    else
                    {
                        throw new CHttpException(404, Yii::t('application', 'Пользователь не найден. Возможно, Ваш аккаунт был заблокирован или удален.'));
                    }
                }

                return $this->_user;
            }

            return null;
        }

        /**
         *
         * @var string Page Name
         */
        public $pageName;

        /**
         * @var string the default layout for the controller view.
         */
        public $layout = '//layouts/main';

        public function setPageTitle($value)
        {
            $value = $value.' - '.Yii::app()->name;
            parent::setPageTitle($value);
            return $this;
        }

        public function setPageName($value)
        {
            $this->pageName = $value;
            return $this;
        }

    }
    