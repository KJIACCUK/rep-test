<html>
    <body>
        <p><strong>Тема</strong>: <?php print $title; ?></p>
        <p><strong>Описание:</strong> <?php print $description; ?></p>
        <p><strong>Пользователь:</strong> <?php print CHtml::encode($user->name); ?></p>
    </body>
</html>