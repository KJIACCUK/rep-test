<?php

    class UserAvatar extends Widget
    {
        public $image;
        public $saveUrl;

        public function run()
        {
            $this->render('user_avatar');
        }

    }
    