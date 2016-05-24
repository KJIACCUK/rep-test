<?php

    class AdminController extends CController
    {

        public $layout = 'main';
        public $mainMenu = array();

        /**
         *
         * @var Employee
         */
        protected $_employee;

        protected function beforeAction($action)
        {
            if(($employee = $this->getEmployee()))
            {
                switch($employee->account->type)
                {
                    case Account::TYPE_ADMIN:
                        $this->mainMenu = array(
                            array('label' => Yii::t('application', 'Главная'), 'url' => $this->createUrl('default/index'), 'active' => $this->isMainMenuItemActive('default')),
                            array('label' => Yii::t('application', 'Сотрудники'), 'active' => $this->isMainMenuItemActive('employee'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Администраторы'), 'url' => $this->createUrl('employee/index', array('type' => 'administrators'))),
                                    array('label' => Yii::t('application', 'Модераторы'), 'url' => $this->createUrl('employee/index', array('type' => 'moderators'))),
                                    array('label' => Yii::t('application', 'Операторы'), 'url' => $this->createUrl('employee/index', array('type' => 'operators'))),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('employee/create')),
                                )),
                            array('label' => Yii::t('application', 'Пользователи'), 'active' => $this->isMainMenuItemActive('user'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Пользователи'), 'url' => $this->createUrl('user/index')),
                                    array('label' => Yii::t('application', 'Нотификации'), 'url' => $this->createUrl('user/notifications')),
                                    array('label' => Yii::t('application', 'Экспорт'), 'url' => $this->createUrl('user/export')),
                                )),
                            array('label' => Yii::t('application', 'Верификация'), 'active' => $this->isMainMenuItemActive('userVerification'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Заявки'), 'url' => $this->createUrl('userVerification/index')),
                                    array('label' => Yii::t('application', 'История'), 'url' => $this->createUrl('userVerification/history'))
                                )),
                            array('label' => Yii::t('application', 'Мероприятия'), 'active' => $this->isMainMenuItemActive('event'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Глобальные'), 'url' => $this->createUrl('event/index')),
                                    array('label' => Yii::t('application', 'Пользовательские'), 'url' => $this->createUrl('event/users')),
                                    array('label' => Yii::t('application', 'На проверке'), 'url' => $this->createUrl('event/onValidation')),
                                    array('label' => Yii::t('application', 'На обработке с Relax.by'), 'url' => $this->createUrl('event/fromRelax')),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('event/create')),
                                )),
                            array('label' => Yii::t('application', 'Маркетинговые исследования'), 'active' => $this->isMainMenuItemActive('marketingResearch'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Исследования'), 'url' => $this->createUrl('marketingResearch/index')),
                                    array('label' => Yii::t('application', 'Статистика'), 'url' => $this->createUrl('marketingResearch/statistics')),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('marketingResearch/create')),
                                )),
                            array('label' => Yii::t('application', 'Бонусный магазин'), 'active' => $this->isMainMenuItemActive('store') || $this->isMainMenuItemActive('promo'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Категории'), 'url' => $this->createUrl('store/indexCategory')),
                                    array('label' => Yii::t('application', 'Товары'), 'url' => $this->createUrl('store/index')),
                                    array('label' => Yii::t('application', 'Заказы'), 'url' => $this->createUrl('store/orders')),
                                    array('label' => Yii::t('application', 'Экспорт'), 'url' => $this->createUrl('store/export')),
                                    array('label' => Yii::t('application', 'Промо-коды'), 'url' => $this->createUrl('promo/index')),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('store/create')),
                                )),
                            array('label' => Yii::t('application', 'Настройки'), 'url' => $this->createUrl('setting/index'), 'active' => $this->isMainMenuItemActive('settings'))
                        );
                        break;

                    case Account::TYPE_MODERATOR:
                        $this->mainMenu = array(
                            array('label' => Yii::t('application', 'Главная'), 'url' => $this->createUrl('default/index'), 'active' => $this->isMainMenuItemActive('default')),
                            array('label' => Yii::t('application', 'Пользователи'), 'active' => $this->isMainMenuItemActive('user'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Пользователи'), 'url' => $this->createUrl('user/index')),
                                    //array('label' => Yii::t('application', 'Нотификации'), 'url' => $this->createUrl('user/notifications')),
                                    array('label' => Yii::t('application', 'Экспорт'), 'url' => $this->createUrl('user/export')),
                                )),
                            array('label' => Yii::t('application', 'Верификация'), 'active' => $this->isMainMenuItemActive('userVerification'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Заявки'), 'url' => $this->createUrl('userVerification/index')),
                                    array('label' => Yii::t('application', 'История'), 'url' => $this->createUrl('userVerification/history'))
                                )),
                            array('label' => Yii::t('application', 'Мероприятия'), 'active' => $this->isMainMenuItemActive('event'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Глобальные'), 'url' => $this->createUrl('event/index')),
                                    array('label' => Yii::t('application', 'Пользовательские'), 'url' => $this->createUrl('event/users')),
                                    array('label' => Yii::t('application', 'На проверке'), 'url' => $this->createUrl('event/onValidation')),
                                    array('label' => Yii::t('application', 'На обработке с Relax.by'), 'url' => $this->createUrl('event/fromRelax')),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('event/create')),
                                )),
                            array('label' => Yii::t('application', 'Маркетинговые исследования'), 'active' => $this->isMainMenuItemActive('marketingResearch'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Исследования'), 'url' => $this->createUrl('marketingResearch/index')),
                                    array('label' => Yii::t('application', 'Статистика'), 'url' => $this->createUrl('marketingResearch/statistics')),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('marketingResearch/create')),
                                )),
                            array('label' => Yii::t('application', 'Бонусный магазин'), 'active' => $this->isMainMenuItemActive('store') || $this->isMainMenuItemActive('promo'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Категории'), 'url' => $this->createUrl('store/indexCategory')),
                                    array('label' => Yii::t('application', 'Товары'), 'url' => $this->createUrl('store/index')),
                                    array('label' => Yii::t('application', 'Заказы'), 'url' => $this->createUrl('store/orders')),
                                    array('label' => Yii::t('application', 'Экспорт'), 'url' => $this->createUrl('store/export')),
                                    array('label' => Yii::t('application', 'Промо-коды'), 'url' => $this->createUrl('promo/index')),
                                    TbHtml::menuDivider(),
                                    array('label' => TbHtml::icon(TbHtml::ICON_PLUS).Yii::t('application', 'Добавить'), 'url' => $this->createUrl('store/create')),
                                ))
                        );
                        break;

                    case Account::TYPE_OPERATOR:
                        $this->mainMenu = array(
                            array('label' => Yii::t('application', 'Главная'), 'url' => $this->createUrl('default/index'), 'active' => $this->isMainMenuItemActive('default')),
                            array('label' => Yii::t('application', 'Пользователи'), 'active' => $this->isMainMenuItemActive('user'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Пользователи'), 'url' => $this->createUrl('user/index')),
                                    array('label' => Yii::t('application', 'Экспорт'), 'url' => $this->createUrl('user/export')),
                                )),
                            array('label' => Yii::t('application', 'Верификация'), 'active' => $this->isMainMenuItemActive('userVerification'),
                                'items' => array(
                                    array('label' => Yii::t('application', 'Заявки'), 'url' => $this->createUrl('userVerification/index')),
                                    array('label' => Yii::t('application', 'История'), 'url' => $this->createUrl('userVerification/history'))
                                ))
                        );
                        break;
                }
            }

            return parent::beforeAction($action);
        }

        public function isMainMenuItemActive($controllerId)
        {
            if($this->getId() == $controllerId)
            {
                return true;
            }
            return false;
        }

        public function getEmployee()
        {
            if(!Yii::app()->user->getIsGuest())
            {
                if(!$this->_employee)
                {
                    $account = Account::model()->findByPk(Yii::app()->user->getId());
                    /* @var $account Account */
                    if($account && $account->isActive && in_array($account->type, array(Account::TYPE_ADMIN, Account::TYPE_MODERATOR, Account::TYPE_OPERATOR)))
                    {
                        $this->_employee = Employee::model()->with('account')->findByAttributes(array('accountId' => $account->accountId));
                    }
                    else
                    {
                        throw new CHttpException(404, Yii::t('application', 'Пользователь не найден. Возможно, Ваш аккаунт был заблокирован или удален.'));
                    }
                }

                return $this->_employee;
            }

            return null;
        }

        public function setPageTitle($value)
        {
            $value = $value.' - '.Yii::app()->name;
            parent::setPageTitle($value);
            return $this;
        }

    }
    