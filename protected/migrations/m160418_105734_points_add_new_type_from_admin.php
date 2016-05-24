<?php

class m160418_105734_points_add_new_type_from_admin extends CDbMigration
{

    public function up()
    {
        $this->insert('point', array(
          'pointKey' => Point::KEY_FROM_ADMIN,
          'pointsCount' => 0
        ));
    }

    public function down()
    {
        $this->delete('point', array(
          'pointKey' => Point::KEY_FROM_ADMIN,
        ));
    }

}
