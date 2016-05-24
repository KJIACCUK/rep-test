<?php

    class m140801_170244_marketing_research_part extends CDbMigration
    {

        public function up()
        {
            $this->execute("
                CREATE TABLE `marketing_research` ( 
                    `marketingResearchId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `type` varchar(20) NOT NULL,
                    `name` varchar(255) NOT NULL,
                    `content` text NULL,
                    `isEnabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
                    `isPushed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`marketingResearchId`)  
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->execute("
                CREATE TABLE `marketing_research_variant` (
                    `marketingResearchVariantId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `marketingResearchId` int(11) UNSIGNED NOT NULL,
                    `variant` varchar(255) NOT NULL,
                    PRIMARY KEY (`marketingResearchVariantId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->execute("
                CREATE TABLE `marketing_research_answer_text` (
                    `marketingResearchAnswerTextId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `marketingResearchId` int(11) UNSIGNED NOT NULL,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `answer` text NOT NULL,
                    PRIMARY KEY (`marketingResearchAnswerTextId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci; 
            ");

            $this->execute("
                CREATE TABLE `marketing_research_answer_variant` (
                    `marketingResearchAnswerVariantId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `marketingResearchId` int(11) UNSIGNED NOT NULL,
                    `marketingResearchVariantId` int(11) UNSIGNED NOT NULL,
                    `userId` int(11) UNSIGNED NOT NULL,
                    PRIMARY KEY (`marketingResearchAnswerVariantId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->execute("
                CREATE TABLE `marketing_research_user_answer` (
                    `marketingResearchUserAnswerId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `marketingResearchId` int(11) UNSIGNED NOT NULL,
                    `userId` int(11) UNSIGNED NOT NULL,
                    `dateCreated` int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (`marketingResearchUserAnswerId`)
                ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;
            ");

            $this->addForeignKey('fk_marketing_research_variant_marketing_research_1', 'marketing_research_variant', 'marketingResearchId', 'marketing_research', 'marketingResearchId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_marketing_research_answer_text_marketing_research_1', 'marketing_research_answer_text', 'marketingResearchId', 'marketing_research', 'marketingResearchId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_marketing_research_answer_text_user_1', 'marketing_research_answer_text', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_ma_re_an_va_ma_re_1', 'marketing_research_answer_variant', 'marketingResearchId', 'marketing_research', 'marketingResearchId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_marketing_research_answer_variant_user_1', 'marketing_research_answer_variant', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_ma_re_an_va_ma_re_va_1', 'marketing_research_answer_variant', 'marketingResearchVariantId', 'marketing_research_variant', 'marketingResearchVariantId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_ma_re_user_an_ma_re_1', 'marketing_research_user_answer', 'marketingResearchId', 'marketing_research', 'marketingResearchId', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk_marketing_research_user_answer_user_1', 'marketing_research_user_answer', 'userId', 'user', 'userId', 'CASCADE', 'CASCADE');
        }

        public function down()
        {
            $this->dropForeignKey('fk_marketing_research_variant_marketing_research_1', 'marketing_research_variant');
            $this->dropForeignKey('fk_marketing_research_answer_text_marketing_research_1', 'marketing_research_answer_text');
            $this->dropForeignKey('fk_marketing_research_answer_text_user_1', 'marketing_research_answer_text');
            $this->dropForeignKey('fk_ma_re_an_va_ma_re_1', 'marketing_research_answer_variant');
            $this->dropForeignKey('fk_marketing_research_answer_variant_user_1', 'marketing_research_answer_variant');
            $this->dropForeignKey('fk_ma_re_an_va_ma_re_va_1', 'marketing_research_answer_variant');
            $this->dropForeignKey('fk_ma_re_user_an_ma_re_1', 'marketing_research_user_answer');
            $this->dropForeignKey('fk_marketing_research_user_answer_user_1', 'marketing_research_user_answer');

            $this->dropTable('marketing_research_user_answer');
            $this->dropTable('marketing_research_answer_variant');
            $this->dropTable('marketing_research_answer_text');
            $this->dropTable('marketing_research_variant');
            $this->dropTable('marketing_research');
        }

    }
    