<?php
    /* @var $this MarketingResearchController */
    /* @var $researches MarketingResearch[] */
?>
<?php foreach($researches as $item): ?>
<li>
    <a <?php print $item->isAnsweredInList?'':' class="research-new"';?> href="<?php print $this->createUrl('marketingResearch/detail', array('marketingResearchId' => $item->marketingResearchId)); ?>">
        <?php print CHtml::encode($item->name); ?>
    </a>
</li>
<?php endforeach; ?>