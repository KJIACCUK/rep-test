<?php

class m290416_183946_poinst_add_ew_types extends CDbMigration
{

    public function up()
    {
        $this->insert('point', array(
            'pointKey' => Point::KEY_TEN_EVENTS_SUBSCRIBED,
            'pointsCount' => 10
        ));

        $this->insert('point', array(
            'pointKey' => Point::KEY_SOCIAL_SHARE,
            'pointsCount' => 10
        ));
    }

    public function down()
    {
        $this->delete('point', array(
            'pointKey' => Point::KEY_TEN_EVENTS_SUBSCRIBED,
        ));

        $this->delete('point', array(
            'pointKey' => Point::KEY_SOCIAL_SHARE,
        ));
    }

}
