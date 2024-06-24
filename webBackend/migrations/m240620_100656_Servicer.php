<?php

use yii\db\Migration;

/**
 * Class m240620_100656_Servicer
 */
class m240620_100656_Servicer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%servicer}}', [
            'servicer_id' => $this->primaryKey(),
            'service_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(), // this the fk of guest
            'rating' => $this->integer(200)->notNull(),
            'description' => $this->string(500)->notNull(),
            'cleanliness' => $this->integer()->notNull(),
            'location' => $this->integer()->notNull(),
            'communication' => $this->integer()->notNull(),
            'days_stayed' => $this->integer()->notNull(),
            'review_date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'approved' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey(
            'fk-service',
            'servicer',
            'service_id',
            'services',
            'service_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order',
            'servicer',
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
        $this->dropForeignKey('fk-service', 'servicer');
        $this->dropForeignKey('fk-order', 'servicer');

//        $this->dropForeignKey('fk-host', 'services');

        $this->dropTable('{{%servicer}}');
    }
}
