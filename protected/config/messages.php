<?php

    return array(
        'sourcePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR,
        'messagePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'messages',
        'languages' => array('en', 'ru'),
        'fileTypes' => array('php', 'phtml'),
        'exclude' => array('.git', '.gitignore', 'migrations', 'runtime', 'config'),
    );