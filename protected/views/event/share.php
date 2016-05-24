<?php
    /* @var $this EventController */
    /* @var $event array */
    /* @var $cs CClientScript */
    
    $language = Yii::app()->language;
    
    $this->setPageTitle(Yii::t('application', 'Мероприятие'));
    $this->layout = '//layouts/inner_unauthorized';
    $cs = Yii::app()->clientScript;
    
    $cs->registerCoreScript('jquery');
    $cs->registerCoreScript('jquery.ui');
    $cs->registerCssFile('/css/jquery-ui-1.10.4.css');

    $cs->registerMetaTag('text/html; charset=utf-8', null, 'Content-Type');
    $cs->registerMetaTag($language, 'language');
    $cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');
    
    $cs->registerMetaTag(CHtml::encode($event['name']), null, null, array('property' => 'og:title'));
    $cs->registerMetaTag('article', null, null, array('property' => 'og:type'));
    if($event['hasImage'])
    {
        $cs->registerMetaTag($event['image'], null, null, array('property' => 'og:image'));
    }
    $cs->registerMetaTag($this->createUrl('event/share', array('eventId' => $event['eventId'])), null, null, array('property' => 'og:url'));
    $cs->registerMetaTag(Yii::t('application', 'Я Буду там. Скачивай приложение Android “БУДУТАМ” и узнавай какие вечеринки выбирают в твоем городе.').'('.Yii::app()->params['androidStoreLink'].')', null, null, array('property' => 'og:description'));
    
    $cs->registerScript('fb_share', "
        $.getScript('//connect.facebook.net/ru_RU/all.js', function(){
        
            FB.init({
                appId: '".Yii::app()->params['facebook']['appId']."',
                version: 'v2.0'
            });
            
            $('#fbShare').click(function(){
            
                FB.login(function(){
                
                    FB.ui({method: 'share',
                        href: '".$this->createUrl('event/share', array('eventId' => $event['eventId']))."'
                    });
                    
                });
                
                return false;
            });
            
        });
    ");
    
?>
<div class="mirop">
    <img id="eventImageView" src="<?php print $event['image']; ?>" alt=""/>
    <?php if($event['isMine']): ?>
    <div id="upladImage">
        <a id="btnUploadImage" href="#"><?php print Yii::t('application', 'Загрузить изображение'); ?></a>
        <input id="uploadImageInput" style="display: none;" type="file" name="Event[imageFile]">
        <div id="uploadImageProgressBar" class="meter" style="display: none; margin: auto;">
            <span style="width: 0%"></span>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="kratk_inf">
        <div class="mest_mir">
            <p class="lf_f"><?php print CHtml::encode($event['publisherName']); ?></p>
            <p class="rg_f"><?php print CHtml::encode($event['city'].($event['street']?', '.$event['street']:'').($event['houseNumber']?', '.$event['houseNumber']:'')); ?></p>
        </div>
        <div class="name_mir">
            <a id="fbShare" class="faceb_p" href="#"><?php print Yii::t('application', 'Поделиться'); ?></a>
            <p><?php print CHtml::encode($event['name']); ?></p>
        </div>
        <div class="date_m">
            <div class="lf_f"><?php print $event['dateStart'].', '.$event['timeStart'].($event['timeEnd']?' - '.$event['timeEnd']:''); ?></div>
            <div class="rg_f"><?php print Yii::t('application', 'Категория'); ?>: <span><?php print CHtml::encode($event['category']); ?></span></div>
        </div>
        <div class="frend_m">
            <?php if($event['subscribersCount'] >= 10): ?>
                <a class="lf_f" href="<?php print $this->createUrl('event/subscribers', array('eventId' => $event['eventId'])); ?>"><?php print Yii::t('application', 'Подписано: {count} чел.', array('{count}' => $event['subscribersCount'])); ?></a>
            <?php else: ?>
                <a class="lf_f" style="text-decoration: none;" onclick="return false;" href="#"><?php print Yii::t('application', 'Подписано: {count} чел.', array('{count}' => $event['subscribersCount'])); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="line_reg"></div>
    
    <p>
        <?php print CHtml::encode($event['description']); ?>
        <?php if($event['isRelax']): ?>
        <br /><?php print Yii::t('application', 'Источник - портал'); ?> <a href="http://www.relax.by" target="_blank">relax.by</a>
        <?php endif; ?>
    </p>
    
    <?php print $this->renderPartial('_gallery_detail_block', array('event' => $event)); ?>
            
</div>