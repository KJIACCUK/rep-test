<?php
    /* @var $this AdminController */
    /* @var $cs CClientScript */
    $cs = Yii::app()->clientScript;
    $language = Yii::app()->language;

    $cs->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type');
    $cs->registerMetaTag($language, 'language');
    $cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');

    Yii::app()->bootstrap->register();

    $cs->registerCssFile('/css/admin.css');
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php print CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
        <div class="container">
            <?php print $content; ?>
        </div>
    </body>
</html>