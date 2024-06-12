<?php

use yii\db\Migration;

/**
 * Class m240528_184446_updateServices
 */
class m240528_184446_updateServices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addForeignKey(
            'fk-host',
            'services',
            'host_id',
            'hosts',
            'host_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-host', 'services');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240528_184446_updateServices cannot be reverted.\n";

        return false;
    }
    */
}
