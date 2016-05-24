<?php

    class m140802_144447_points_init_data extends CDbMigration
    {

        public function up()
        {
            $this->alterColumn('point', 'pointKey', 'varchar(50) NOT NULL AFTER `pointId`');
            
            $this->insert('point', array(
                'pointKey' => Point::KEY_SOCIAL_INVITE,
                'pointsCount' => 1
            ));

            $this->insert('point', array(
                'pointKey' => Point::KEY_VERIFICATION,
                'pointsCount' => 1
            ));

            $this->insert('point', array(
                'pointKey' => Point::KEY_MARKETING_RESEARCH_VISIT,
                'pointsCount' => 1
            ));

            $this->insert('point', array(
                'pointKey' => Point::KEY_MARKETING_RESEARCH_ANSWER,
                'pointsCount' => 1
            ));

            $this->insert('point', array(
                'pointKey' => Point::KEY_EVENT_CREATE,
                'pointsCount' => 1
            ));
        }

        public function down()
        {
            $this->delete('point');
            $this->alterColumn('point', 'pointKey', 'varchar(20) NOT NULL AFTER `pointId`');
        }

    }
    