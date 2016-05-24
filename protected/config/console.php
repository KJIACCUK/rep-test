<?php
$localConfig = require dirname(__FILE__).DIRECTORY_SEPARATOR.'console.local.php';
return CMap::mergeArray(array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => 'Будутам',
    'sourceLanguage' => 'ru',
    'language' => 'ru',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.helpers.*',
        'application.filters.*',
        'application.extensions.EWideImage.WideImage',
        'application.extensions.mail.*',
        'application.extensions.EHttpClient.*',
        'application.extensions.googlegcm.GCM',
        'application.extensions.curl.Curl'
    ),
    // application components
    'components' => array(
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db',
            'itemTable' => 'auth_item',
            'itemChildTable' => 'auth_item_child',
            'assignmentTable' => 'auth_assignment',
            'defaultRoles' => array('guest')
        ),
        'db' => array(),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
        'cache' => array(
            'class' => 'system.caching.CDbCache',
        ),
        'mail' => array(
            'class' => 'application.extensions.mail.YiiMail',
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.yandex.ru',
                'username' => 'support@budutam.by',
                'password' => '23fgX56mAD19',
                'port' => '25',
            ),
            'viewPath' => 'application.views.mail',
            'dryRun' => false
        ),
        'googlegcm' => array(
            'class' => 'application.extensions.googlegcm.GCM',
            //'apiKey' => 'AIzaSyBcL5MEzMrT-jJl0dvqOEBXQ6KpPd0ekGI',
            'apiKey' => 'AIzaSyC6_XuA9gLxYGQHvazPye2aSCC_Jow1aRE',
        ),
        'curl' => array(
            'class' => 'application.extensions.curl.Curl',
        )
    ),
    'params' => array(
        'hostname' => 'localhost',
        'noReplyEmail' => 'support@budutam.by',
        'feedbackEmail' => array('i.chehovskaya@artox.com', 'anton.ogy@gmail.com', 'support@budutam.by'),
        'facebookLink' => 'https://apps.facebook.com/buduttam',
        'androidStoreLink' => '',
        'facebook' => array(
            'appId' => '1135320649830761',
            'secret' => 'da3ede583559cfbc6e3daa4053598568',
        ),
        'vkontakte' => array(
            'appId' => '4418537',
            'secret' => 'OqwxoP7VN6uLxOgkE32w',
        ),
        'twitter' => array(
            'key' => 'a8NB0kmW95cz2VaMJ0pma82a7',
            'secret' => 'MxFqmXw1KohUr2euVU0gCA5jh3oC8SuEAEuUWsA4l2oioTm1CJ',
        ),
        'salt' => 'SM7LadZt5S8NgfBrzYPK',
        'dateFormat' => 'd.m.Y',
        'dateTimeFormat' => 'd.m.Y H:i',
        'loginRememberTime' => (3600 * 24 * 365), // 1 year
        'phoneCodes' => array(
            '29' => '029',
            '44' => '044',
            '33' => '033',
            '25' => '025',
            '17' => '017'
        ),
        'messengers' => array(
            'skype' => 'Skype',
            'hangouts' => 'Hangouts',
        ),
        'musicGenres' => array(
            'Alternative',
            'Blues',
            'Chanson',
            'Classical',
            'Club',
            'Country',
            'Dab step',
            'Dance',
            'Disco',
            'Drum & Bass',
            'Electro',
            'Folk',
            'Funk',
            'Hardcore',
            'Hip-Hop',
            'House',
            'Industrial',
            'Instrumental',
            'Jazz',
            'Metal',
            'Minimal',
            'Pop',
            'Pop-Rock',
            'Punk',
            'Rap',
            'Reggae',
            'Retro',
            'R&B',
            'Rock',
            'Techno',
            'Trance',
            'Other'
        ),
        'cigaretteBrands' => array(
            'Winston',
            'Camel',
            'Palmal',
            'Kent'
        ),
        'eventCategories' => array(
            'Встреча',
            'Вечеринка',
            'Концерт',
            'Ночной клуб',
            'Выставка',
            'Завтрак',
            'Обед',
            'Ужин',
            'Другое',
            'День Рождения',
            'Праздник',
            'After Party'
        ),
        'skypeLogin' => 'yuri.petrukovich',
        'hangoutsLogin' => 'petrukovich1984',
        'hangoutsAppId' => '517404585667',
        'nodeServerUrl' => 'https://budutam.by:3000/',
        'nodeServerSecure' => false,
        'eventSubscribersCountToPoints' => 50
    ),
), $localConfig);
