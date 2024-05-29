<?php

use yii\db\Migration;

/**
 * Class m240528_183914_hosts
 */
class m240528_183914_hosts extends Migration
{
    /**
     * {@inheritdoc}
     */
        public function safeUp()
    {
        $this->createTable('{{%hosts}}', [
            'host_id' => $this->primaryKey(),
            'about' => $this->string(100)->notNull(),
            'host_name' => $this->string(200)->notNull(),
            'language' => $this->string(500)->notNull(),
            'email' => $this->string(100)->notNull(),
            'number' => $this->integer()->notNull(),
            'picture' => $this->string(200)->notNull(),
            'county_id' => $this->integer()->notNull(),
            'approved' => $this->boolean()->defaultValue(false),
        ]);

        $this->addForeignKey(
            'fk-county',
            'hosts',
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
        $this->dropForeignKey('fk-county', 'hosts');
        $this->dropTable('{{%hosts}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240528_183914_hosts cannot be reverted.\n";

        return false;
    }
    */
}
