<?php

use yii\db\Migration;

/**
 * Class m240718_115746_alterorderss
 */
class m240718_115746_alterorderss extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%orders}}', 'host_id', $this->integer(100));
        $this->update('{{%orders}}', ['host_id' => 62]);
        $this->alterColumn('{{%orders}}', 'host_id', $this->integer(100)->notNull());

        $this->addForeignKey(
            'fk-orders',
            'orders',
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
        $this->dropForeignKey('fk-orders', 'orders');
        $this->dropColumn('orders', 'host_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240718_115746_alterorderss cannot be reverted.\n";

        return false;
    }
    */
}
