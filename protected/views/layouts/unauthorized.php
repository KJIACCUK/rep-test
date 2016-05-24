<?php
/* @var $cs CClientScript */
/* @var $this SiteController */
$cs = Yii::app()->clientScript;
$language = Yii::app()->language;

$cs->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type');
$cs->registerMetaTag($language, 'language');
$cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');

$cs->registerCoreScript('jquery');
$cs->registerScriptFile('/js/jquery.formstyler.js');
$cs->registerScriptFile('/js/main.js');

$cs->registerCssFile('https://fonts.googleapis.com/css?family=Ubuntu:300,400,500&subset=latin,cyrillic');
$cs->registerCssFile('https://fonts.googleapis.com/css?family=Ubuntu+Mono|Ubuntu+Condensed&subset=latin,cyrillic');

$cs->registerCssFile('/css/jquery.formstyler.css');
$cs->registerCssFile('/css/main.css');

$subclass = '';
if ($this->action->getId() == 'registration') {
    $subclass = 'regis_b';
}

$bodyclass = '';
if ($this->action->getId() == 'login') {
    $bodyclass = ' class="page_login"';
}

if ($this->action->getId() == 'login') {
    $cs->registerScriptFile('/js/jquery.cookie.js');
    $cs->registerScript('fb_fix', "
        $.getScript('//connect.facebook.net/ru_RU/all.js', function(){

            FB.init({
                appId: '".Yii::app()->params['facebook']['appId']."',
                version: 'v2.0'
            });

            window.fbAsyncInit = function () {
                var popupIsShowed = $.cookie('auth_popup');
                if(!popupIsShowed)
                {
                    $('#authPopupDialog').dialog('open');
                    $('body').on('click', '#authPopupContent', function(){
                        $('#authPopupDialog').dialog('close');
                        return false;
                    });
                    $('body').on('click', '#authPopupBtnClose', function(){
                        $('#authPopupDialog').dialog('close');
                        return false;
                    });
                    $.cookie('auth_popup', '1', {expires: 365});
                }
                FB.Canvas.setSize({width: 820, height: 677});
            }
        });
    ");
} else {
    $cs->registerScript('fb_fix', "
    $.getScript('//connect.facebook.net/ru_RU/all.js', function(){

        FB.init({
            appId: '".Yii::app()->params['facebook']['appId']."',
            version: 'v2.0'
        });

        window.fbAsyncInit = function () {
            FB.Canvas.setSize({height: $('body').height()});
        }
    });
");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php print CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body<?php print $bodyclass; ?>>
        <div class="osn_b login_b">
            <div class="bl_login <?php print $subclass; ?>">
                <a href="<?php print $this->createUrl('site/index'); ?>" id="logo"></a>
                <div class="clr"></div>
                <h1><?php print CHtml::encode($this->pageName); ?></h1>
                <!--                <div class="line_reg"></div>-->
                <?php echo $content; ?>
            </div>
            <div class="footer_b"></div>
        </div>
<?php $this->widget('application.widgets.FlashMessage'); ?>
    </body>
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-35241890-27', 'auto');
        ga('send', 'pageview');
    </script>
</html>