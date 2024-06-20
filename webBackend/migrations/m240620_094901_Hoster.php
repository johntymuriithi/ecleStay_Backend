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
        $this->createTable('{{%services}}', [
            'service_id' => $this->primaryKey(),
            'host_id' => $this->integer()->notNull(),
            'price' => $this->integer(200)->notNull(),
            'pricing_criteria' => $this->string(50)->notNull(),
            'type_id' => $this->integer()->notNull(),
            'county_id' => $this->integer()->notNull(),
            'description' => $this->string(500)->notNull(),
            'start_date' => $this->timestamp()->notNull(),
            'end_date' => $this->timestamp()->notNull(),
            'approved' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey(
            'fk-county',
            'services',
            'county_id',
            'counties',
            'county_id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-county', 'services');

//        $this->dropForeignKey('fk-host', 'services');

        $this->dropTable('{{%services}}');
    }
}
