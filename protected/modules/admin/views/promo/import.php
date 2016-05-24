<?php
    /* @var $this PromoController */
    /* @var $model PromoImport */
    /* @var $importStats array */
    /* @var $doImport bool */
?>

<h1><?php echo TbHtml::labelTb(Yii::t('application', 'Импорт'), array('color' => TbHtml::LABEL_COLOR_INFO, 'class' => 'page-part-name')); ?> <?php print Yii::t('application', 'Промо-коды'); ?></h1>

<div class="form">

    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'promo-form',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        ));
    ?>
    
    <div class="alert alert-block">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><?php print Yii::t('application', 'Важно!')?></h4>
        <ul>
            <li><?php print Yii::t('application', 'Файл csv должен быть создан в программах MS Word или Pages')?>;</li>
            <li><?php print Yii::t('application', 'Код берется из первой ячейки каждой строки, остальные ячейки игрорируются')?>;</li>
            <li><?php print Yii::t('application', 'В качестве кода допустимо использовать символы (a-z A-Z) и числа (0-9). Регистр не имеет значения')?>;</li>
            <li><?php print Yii::t('application', 'Максимальная длина кода - 255 символов')?>;</li>
            <li><?php print Yii::t('application', 'Пустые строки в файле пропускаются')?>;</li>
            <li><?php print Yii::t('application', 'Пробелы в начале и в конце строки убираются')?>;</li>
            <li><?php print Yii::t('application', 'Если код уже существует в базе, он игнорируется')?>.</li>
        </ul>
    </div>

    <?php echo $form->errorSummary($model); ?>
    <?php /* @var $form TbActiveForm */ ?>
    
    <?php if($doImport): ?>
    <div class="alert alert-info">
        <p><?php print Yii::t('application', 'Импорт файла завершен.')?></p>
        <p><strong><?php print Yii::t('application', 'Всего записей в файле')?></strong>: <?php print $importStats['total']?></p>
        <p><strong><?php print Yii::t('application', 'Кодов добавлено')?></strong>: <?php print $importStats['imported']?></p>
        <p><strong><?php print Yii::t('application', 'Ошибок')?></strong>: <?php print $importStats['error']?></p>
        <p><strong><?php print Yii::t('application', 'Уже существующих в базе')?></strong>: <?php print $importStats['exists']?></p>
    </div>
    <?php endif; ?>
    
    <?php echo $form->fileFieldControlGroup($model, 'importFile', array('span' => 8)); ?>

    <div class="form-actions">
        <?php
            echo TbHtml::submitButton(Yii::t('application', 'Импортировать'), array(
                'color' => TbHtml::BUTTON_COLOR_PRIMARY,
                'size' => TbHtml::BUTTON_SIZE_LARGE,
            ));
        ?>
        <?php
            echo TbHtml::link(Yii::t('application', 'Отмена'), $this->createUrl('index'));
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->