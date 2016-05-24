<?php

    abstract class Widget extends CWidget
    {

        public function getViewPath($checkTheme = false)
        {
            return Yii::getPathOfAlias('application.views.widgets');
        }

    }
    