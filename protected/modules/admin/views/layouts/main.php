<?php
    /* @var $this AdminController */
    /* @var $cs CClientScript */
    $cs = Yii::app()->clientScript;
    $language = Yii::app()->language;

    $cs->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type');
    $cs->registerMetaTag($language, 'language');
    $cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');
    
    $cs->scriptMap = array(
        'jquery-1.10.2.js' => '/js/jquery.js'
    );

    Yii::app()->bootstrap->register();

    $cs->registerCssFile('/css/admin.css');
    
    $cs = Yii::app()->clientScript;    
    
    $cs->registerScriptFile('/js/pnotify.custom.min.js');
    $cs->registerCssFile('/css/pnotify.custom.min.css');
    
    if(Yii::app()->user->hasFlash('error'))
    {
        $cs->registerScript('flash_message', "
            new PNotify({
                title: '".Yii::app()->user->getFlash('error')."',
                type: 'error',
                icon: 'icon-warning-sign'
            });
        ");
    }
    elseif(Yii::app()->user->hasFlash('success'))
    {
        $cs->registerScript('flash_message', "
            new PNotify({
                title: '".Yii::app()->user->getFlash('success')."',
                type: 'success',
                icon: 'icon-ok'
            });
        ");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php print CHtml::encode($this->pageTitle); ?></title>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h3 class="pull-left"><?php print CHtml::encode(Yii::app()->name); ?> <span class="muted"><?php print Yii::t('application', 'панель управления'); ?></span></h3>
                <?php
                    print TbHtml::pills(array(
                                array('label' => Yii::app()->user->name, 'url' => $this->createUrl('profile/index'), 'active' => $this->isMainMenuItemActive('profile')),
                                array('label' => Yii::t('application', 'Выход'), 'url' => $this->createUrl('default/logout')),
                                    ), array('class' => 'pull-right'));
                ?>
                <div class="clearfix"></div>
                <div class="navbar">
                    <div class="navbar-inner">
                        <div class="container">
                            <?php print TbHtml::nav(null, $this->mainMenu); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <?php print $content; ?>
            </div>
            <div class="footer">

            </div>
        </div>


    </body>
</html>