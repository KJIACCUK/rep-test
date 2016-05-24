<?php

    class EventSubpageTitle extends Widget
    {
        public $eventId;
        public $title;
        public $eventName;

        public function run()
        {
            $this->render('event_subpage_title');
        }

    }
    