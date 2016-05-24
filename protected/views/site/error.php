<?php
    $this->layout = '//layouts/unauthorized';
    $this->setPageTitle(Yii::t('application', 'Ошибка {code}', array('{code}' => $code)))->setPageName(Yii::t('application', 'Ошибка {code}', array('{code}' => $code)));
?>
<p><?php echo CHtml::encode($message); ?></p>