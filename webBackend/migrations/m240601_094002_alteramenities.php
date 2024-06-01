<?php

use yii\db\Migration;

/**
 * Class m240601_094002_alteramenities
 */
class m240601_094002_alteramenities extends Migration
{


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-services', '{{%amenities}}');
        $this->dropTable('{{%amenities}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240601_094002_alteramenities cannot be reverted.\n";

        return false;
    }
    */
}
