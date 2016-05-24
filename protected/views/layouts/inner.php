<?php
    /* @var $cs CClientScript */
    /* @var $this Controller */
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
    
    $cs->registerScript('fb_async_init', "
        window.fbAsyncInit = function () {
            FB.init({
                appId: '".Yii::app()->params['facebook']['appId']."',
                version: 'v2.0'
            });

            FB.Canvas.setSize({height: $('body').height()});
        }
        
        $.getScript('/js/fb-scroll.js', function(){
            $.getScript('//connect.facebook.net/ru_RU/all.js');
        });
    ");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php print CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
        <div class="osn_b ">
            <div class="header_b">
                <?php $this->widget('application.widgets.HeaderCounters'); ?>
                <?php $this->widget('application.widgets.MainMenu'); ?>
            </div>
            <div class="content_bl child">
                <?php echo $content; ?>
            </div>
            <div class="footer_b"></div>
        </div>
        <?php $this->widget('application.widgets.FlashMessage'); ?>
    </body>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-35241890-27', 'auto');
        ga('send', 'pageview');
    </script>
</html>
<?php SocketIoHelper::registerScript(); ?>