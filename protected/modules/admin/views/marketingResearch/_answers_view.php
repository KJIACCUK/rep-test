<?php
    /* @var $this MarketingResearchController */
    /* @var $data MarketingResearchAnswerText */
?>

<div class="view" style="border: 1px solid #0066A4; border-radius: 3px; padding: 10px; margin-bottom: 10px;">
    <div>
        <strong>#<?php print $data->marketingResearchAnswerTextId; ?> <?php print CHtml::encode($data->user->name); ?></strong>
    </div>
    <div>
        <?php print CHtml::encode($data->answer); ?>
    </div>
</div>