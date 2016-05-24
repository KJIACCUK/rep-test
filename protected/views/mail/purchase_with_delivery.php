<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <body>
        <table cellpadding="0" cellspacing="0" style="margin:0 auto; width:811px;">
            <tr>
                <td colspan="2"><img alt="" src="<?php print Yii::app()->params['mainHost']; ?>/images/mail/header.jpeg" /></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="padding:37px 36px 85px; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:1.3;">
                        <div style="font-weight:bold; font-size:26px; margin-bottom:16px;">Здравствуйте, пользователь <?php print CHtml::encode($user->name); ?>!</div>
                        Вы оформили электронный товар <?php print CHtml::encode($product->name); ?> в бонусном магазине приложения БУДУТАМ.<br />
                        <strong>Код заказа:</strong><?php print CHtml::encode($purchaseCode); ?><br />
                        Адрес доставки:<br />
                        <?php print CHtml::encode($address->postIndex); ?>, <?php print CHtml::encode($address->city); ?>, <?php print CHtml::encode($address->street); ?> <?php print CHtml::encode($address->home); ?><?php print $address->corp?' '.CHtml::encode($address->corp):''; ?><?php print $address->apartment?' - '.CHtml::encode($address->apartment):''; ?>
                        
                        <br /><br /><br />
                        Данное письмо сформировано автоматически и не требует ответа.<br />
                        Если вы считаете, что данное сообщение отправлено вам ошибочно, просто проигнорируйте его.<br />
                        Техподдержка БУДУТАМ.
                    </div>
                </td>
            </tr>
            <tr>
                <td style="padding-left:36px; font-size:16px;">Скачивайте приложение или пользуйтесь им в социальных сетях:</td>
                <td><a href="<?php print Yii::app()->params['androidStoreLink']; ?>"><img alt="" src="<?php print Yii::app()->params['mainHost']; ?>/images/mail/google_play.png" /></a> <a href="<?php print Yii::app()->params['facebookLink']; ?>"><img alt="" src="<?php print Yii::app()->params['mainHost']; ?>/images/mail/facebook.png" /></a></td>
            </tr>
        </table>
    </body>
</html>
