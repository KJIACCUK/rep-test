<?php

    class m140622_143409_user_remove_xmpp extends CDbMigration
    {

        public function up()
        {
            $this->dropColumn('user', 'xmppChatLogin');
            $this->dropColumn('user', 'xmppChatPassword');
        }

        public function down()
        {
            $this->addColumn('user', 'xmppChatLogin', 'VARCHAR(255) NOT NULL AFTER `password`');
            $this->addColumn('user', 'xmppChatPassword', 'VARCHAR(255) NOT NULL AFTER `xmppChatLogin`');
        }
    }
    