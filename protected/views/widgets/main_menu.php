<?php
    /* @var $this MainMenu */
    /* @var $controller WebController */
?>
<ul class="up_menu">
    <li<?php print ($activeMenu == 'events')?' class="cur"':''; ?>><a href="<?php print $controller->createUrl('event/index'); ?>"><?php print Yii::t('application', 'Мероприятия'); ?></a></li>
    <li<?php print ($activeMenu == 'calendar')?' class="cur"':''; ?>><a href="<?php print $controller->createUrl('event/calendar'); ?>"><?php print Yii::t('application', 'Календарь'); ?></a></li>
    <li<?php print ($activeMenu == 'map')?' class="cur"':''; ?>><a href="<?php print $controller->createUrl('event/map'); ?>"><?php print Yii::t('application', 'Карта'); ?></a></li>
    <li<?php print ($activeMenu == 'pro')?' class="cur"':''; ?>><a href="<?php print $controller->createUrl('marketingResearch/index'); ?>"><?php print Yii::t('application', 'PRO-раздел'); ?></a></li>
    <li<?php print ($activeMenu == 'my_page')?' class="cur"':''; ?>><a href="<?php print $controller->createUrl('user/index'); ?>"><?php print Yii::t('application', 'Личная страница'); ?></a></li>
    <li<?php print ($activeMenu == 'settings')?' class="cur"':''; ?>><a href="<?php print $controller->createUrl('user/settings'); ?>"><?php print Yii::t('application', 'Настройки'); ?></a></li>
</ul>