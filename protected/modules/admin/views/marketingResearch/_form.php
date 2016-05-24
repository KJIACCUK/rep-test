<?php
    /* @var $this MarketingResearchController */
    /* @var $model MarketingResearch */
    /* @var $cs CClientScript */

    $cs = Yii::app()->clientScript;

    $cs->registerScriptFile('/js/summernote.js');
    $cs->registerScriptFile('/js/summernote-ru-RU.js');
    $cs->registerCssFile('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');
    $cs->registerCssFile('/css/summernote.css');


    $cs->registerScript('manage-variants', "
        
        function sendFile(file,editor,welEditable) {
            data = new FormData();
            data.append('file', file);
            console.log('image upload:', file, editor, welEditable);
            console.log(data);
            $.ajax({
                data: data,
                type: 'POST',
                url: '".$this->createUrl('uploadImage')."',
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    editor.insertImage(welEditable, url);
                }
            });
        }
        
        function recalcNewVariants() {
            $('#variantsPanel .newVariant').each(function(index, item){
                $(item).attr('id', 'new_variant_'+index);
                $(item).find('input').attr('name', 'MarketingResearchVariant[new_'+index+']');
            });
        }

        function addVariant(value)
        {
            value = value || '';
            var id = arguments[1] || false;
            var newVariantsCount = $('#variantsPanel .newVariant').length;
            var template = '';
            if(id) {
                var template = '<div id=\"variant_'+id+'\">'+
                    '".TbHtml::textField('MarketingResearchVariant[{id}]', '{value}', array('id' => false, 'span' => 7))."'+
                    '".TbHtml::linkButton(Yii::t('application', 'Удалить'), array('class' => 'btnRemoveVariant', 'size' => TbHtml::BUTTON_SIZE_MINI))."'+
                    '</div>';
            } else {
                var template = '<div class=\"newVariant\" id=\"new_variant_\">'+
                    '".TbHtml::textField('MarketingResearchVariant[new]', '{value}', array('id' => false, 'span' => 7))."'+
                    '".TbHtml::linkButton(Yii::t('application', 'Удалить'), array('class' => 'btnRemoveVariant', 'size' => TbHtml::BUTTON_SIZE_MINI))."'+
                    '</div>';
            }

            template = template.replace('{value}', value);
            if(id) {
                template = template.replace('{id}', id);
            }
            $('#variantsPanel').append(template);
            recalcNewVariants();
        }
        
        function changeVariantsPanelVisibility()
        {
            if($('#MarketingResearch_type').val() == '".MarketingResearch::TYPE_CUSTOM_TEXT."')
            {
                $('#variantsWell').hide();
            }
            else
            {
                $('#variantsWell').show();
            }
        }

        $('#btnAddVariant').click(function(){
            addVariant();
            return false;
        });
        
        $('body').on('click', '.btnRemoveVariant', function(){
            $(this).parent().remove();
            recalcNewVariants()
            return false;
        });
        
        $('#MarketingResearch_type').change(function(){
            changeVariantsPanelVisibility();
        });
        
        var variants = ".CJSON::encode($model->variantsData).";
            
        for(var i in variants)
        {
            if(String(i).substring(0, 4) == 'new_') {
                addVariant(variants[i]);
            } else {
                addVariant(variants[i], i);
            }
        }
        
        changeVariantsPanelVisibility();
        
        $('#contentWysiwyg').summernote({
            height: 300,
            width: 765,
            lang: 'ru-RU',
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
        });
        
        $('#contentWysiwyg').code($('#MarketingResearch_content').val());
        
        $('#marketing-research-form').submit(function(){
            $('#MarketingResearch_content').val($('#contentWysiwyg').code());
            return true;
        });
    ");
?>

<div class="form">

    <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'marketing-research-form',
            'enableAjaxValidation' => false,
        ));
    ?>

    <?php echo $form->errorSummary($model); ?>
    <?php /* @var $form TbActiveForm */ ?>

    <?php echo $form->dropDownListControlGroup($model, 'type', MarketingResearchHelper::typesToDropDown(), array('span' => 8)); ?>

    <div id="variantsWell" class="well well-small span7">
        <h4><?php print Yii::t('application', 'Варианты ответов'); ?></h4>
        <div id="variantsPanel">

        </div>
        <?php print TbHtml::linkButton(Yii::t('application', 'Добавить'), array('id' => 'btnAddVariant', 'size' => TbHtml::BUTTON_SIZE_MINI)); ?>
    </div>
    <div class="clearfix"></div>

    <?php echo $form->textFieldControlGroup($model, 'name', array('span' => 8, 'maxlength' => 255)); ?>

    <?php echo $form->textAreaControlGroup($model, 'content', array('rows' => 6, 'span' => 8, 'style' => 'display: none;')); ?>

    <div id="contentWysiwyg"></div>

    <?php echo $form->checkBoxControlGroup($model, 'isEnabled', array('span' => 5)); ?>

    <div class="form-actions">
        <?php
            echo TbHtml::submitButton($model->isNewRecord?Yii::t('application', 'Добавить'):Yii::t('application', 'Сохранить'), array(
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