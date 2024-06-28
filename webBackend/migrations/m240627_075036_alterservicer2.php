<?php

use yii\db\Migration;

/**
 * Class m240627_075036_alterservicer2
 */
class m240627_075036_alterservicer2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('servicer', 'user_id', $this->integer()->notNull());

        $this->addForeignKey(
            'fk-user',
            'servicer',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('servicer', 'user_id');
        $this->dropForeignKey('fk-user', 'servicer');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240627_075036_alterservicer2 cannot be reverted.\n";

        return false;
    }
    */
}
