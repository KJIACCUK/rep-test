<?php

class m160524_084125_add_user_table extends CDbMigration
{
	public function up()
	{
		  $this->addColumn('user','firstTimeActivated','datetime NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('user', 'firstTimeActivated');
	}
}