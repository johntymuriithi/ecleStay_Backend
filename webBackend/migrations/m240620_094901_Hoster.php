<?php

use yii\db\Migration;

/**
 * Class m240620_094901_Hoster
 */
class m240620_094901_Hoster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hoster}}', [
            'hoster_id' => $this->primaryKey(),
            'host_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(), // this the fk of guest
            'rating' => $this->integer(200)->notNull(),
            'description' => $this->string(500)->notNull(),
            'review_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'approved' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey(
            'fk-host',
            'hoster',
            'host_id',
            'hosts',
            'host_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order',
            'hoster',
            'order_id',
            'orders',
            'order_id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-host', 'hoster');
        $this->dropForeignKey('fk-order', 'hoster');

//        $this->dropForeignKey('fk-host', 'services');

        $this->dropTable('{{%hoster}}');
    }
}
