<?php

use yii\db\Migration;

/**
 * Class m240603_082705_orders
 */
class m240603_082705_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orders}}', [
            'order_id' => $this->primaryKey(),
            'user_id' => $this->integer(200)->notNull(),
            'service_id' => $this->integer(200)->notNull(),
            'begin_date' => $this->timestamp()->notNull(),
            'end_date' => $this->timestamp()->notNull(),
            'paid' => $this->boolean()->defaultValue(false)->notNull(),
            'billing_address' => $this->string(100)->notNull(),
            'city' => $this->string(30)->notNull(),
            'state' => $this->string(30)->notNull(),
            'zip_code' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-user',
            'orders',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-service',
            'orders',
            'service_id',
            'services',
            'service_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user', 'orders');
        $this->dropForeignKey('fk-service', 'orders');
        $this->dropTable('{{%orders}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240603_082705_orders cannot be reverted.\n";

        return false;
    }
    */
}
