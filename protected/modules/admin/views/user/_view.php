<?php
/* @var $this UserController */
/* @var $data User */
?>

<div class="view">

    	<b><?php echo CHtml::encode($data->getAttributeLabel('userId')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->userId),array('view','id'=>$data->userId)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('accountId')); ?>:</b>
	<?php echo CHtml::encode($data->accountId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phoneCode')); ?>:</b>
	<?php echo CHtml::encode($data->phoneCode); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('birthday')); ?>:</b>
	<?php echo CHtml::encode($data->birthday); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('image')); ?>:</b>
	<?php echo CHtml::encode($data->image); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('messenger')); ?>:</b>
	<?php echo CHtml::encode($data->messenger); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('messengerLogin')); ?>:</b>
	<?php echo CHtml::encode($data->messengerLogin); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('favoriteMusicGenre')); ?>:</b>
	<?php echo CHtml::encode($data->favoriteMusicGenre); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('favoriteCigaretteBrand')); ?>:</b>
	<?php echo CHtml::encode($data->favoriteCigaretteBrand); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('login')); ?>:</b>
	<?php echo CHtml::encode($data->login); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('password')); ?>:</b>
	<?php echo CHtml::encode($data->password); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('points')); ?>:</b>
	<?php echo CHtml::encode($data->points); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('isFilled')); ?>:</b>
	<?php echo CHtml::encode($data->isFilled); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('isVerified')); ?>:</b>
	<?php echo CHtml::encode($data->isVerified); ?>
	<br />

	*/ ?>

</div>