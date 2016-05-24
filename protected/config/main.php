<?php

    $localConfig = require dirname(__FILE__).DIRECTORY_SEPARATOR.'main.local.php';
    return CMap::mergeArray(array(
                'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
                'name' => 'БУДУТАМ',
                'sourceLanguage' => 'ru',
                'language' => 'ru',
                // preloading 'log' component
                'preload' => array('log'),
                // autoloading model and component classes
                'aliases' => array(
                    'bootstrap' => realpath(__DIR__.'/../extensions/bootstrap'),
                ),
                'import' => array(
                    'application.models.*',
                    'application.models.forms.*',
                    'application.components.*',
                    'application.helpers.*',
                    'application.filters.*',
                    'application.extensions.EWideImage.WideImage',
                    'application.extensions.mail.*',
                    'bootstrap.helpers.*',
                    'bootstrap.widgets.*',
                    'bootstrap.behaviors.*',
                    'application.extensions.EHttpClient.*',
                    'application.extensions.googlegcm.GCM',
                ),
                'modules' => array(
                    'api',
                    'admin'
                ),
                // application components
                'components' => array(
                    'clientScript' => array(
                        'packages' => array(
                            'jquery' => array(
                                'baseUrl' => '/js',
                                'js' => array('jquery-1.10.2.js')
                            ),
                            'jquery.ui' => array(
                                'baseUrl' => '/js',
                                'js' => array('jquery-ui-1.10.4.js')
                            ),
                        )
                    ),
                    'session' => array(
                        'class' => 'system.web.CDbHttpSession',
                        'connectionID' => 'db',
                        'sessionTableName' => 'user_session',
                    ),
                    'user' => array(
                        'allowAutoLogin' => true,
                        'autoUpdateFlash' => false
                    ),
                    'authManager' => array(
                        'class' => 'CDbAuthManager',
                        'connectionID' => 'db',
                        'itemTable' => 'auth_item',
                        'itemChildTable' => 'auth_item_child',
                        'assignmentTable' => 'auth_assignment',
                        'defaultRoles' => array('guest')
                    ),
                    'bootstrap' => array(
                        'class' => 'bootstrap.components.TbApi',
                    ),
                    'urlManager' => array(
                        'urlFormat' => 'path',
                        'showScriptName' => false,
                        'rules' => require dirname(__FILE__).DIRECTORY_SEPARATOR.'routes.php',
                    ),
                    'db' => array(),
                    'errorHandler' => array(
                        'errorAction' => 'site/error',
                    ),
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
                        'transportType' => 'php',
//                        'transportOptions' => array(
//                           'host' => 'smtp.yandex.ru',
//                           'username' => 'support@budutam.by',
//                           'password' => 'baisf6gfg235', //baisf6gfg235
//                           'port' => '465',
//                           'encryption' => 'ssl'
//                        ),
                        'viewPath' => 'application.views.mail',
                        'dryRun' => false
                    ),
                    'googlegcm' => array(
                        'class' => 'application.extensions.googlegcm.GCM',
                        //'apiKey' => 'AIzaSyBcL5MEzMrT-jJl0dvqOEBXQ6KpPd0ekGI',
                        'apiKey' => 'AIzaSyC6_XuA9gLxYGQHvazPye2aSCC_Jow1aRE',
                    ),
                ),
                'params' => array(
                    'mainHost' => 'https://budutam.by',
                    'noReplyEmail' => 'support@budutam.by',
                    'feedbackEmail' => array('support@budutam.by'),
                    'facebookLink' => 'https://apps.facebook.com/buduttam',
                    'androidStoreLink' => 'https://play.google.com/store/apps/details?id=com.artox.willbethere.android',
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
                        '25' => '025'
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
                        'Camel',
                        'Davidoff',
                        'Dunhill',
                        'Glamour',
                        'Kent',
                        'LD',
                        'Lucky Strike',
                        'Mevius',
                        'Monte Carlo',
                        'Pall Mall',
                        'Rich',
                        'Richmond',
                        'Sobranie',
                        'West',
                        'Winston',
                        'Vogue',
                        'Другой бренд'
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
                    'skypeLogin' => 'dostup_pro',
                    'hangoutsLogin' => 'dostup.pro@gmail.com',
                    'hangoutsAppId' => '989072270339',
                    'nodeServerUrl' => 'https://budutam.by:3000/',
                    'nodeServerSecret' => '4fv57h6r8kc3e5jfvk68idsds465h',
                    'nodeServerSecure' => true,
                    'eventSubscribersCountToPoints' => 10
                ),
                ), $localConfig);
