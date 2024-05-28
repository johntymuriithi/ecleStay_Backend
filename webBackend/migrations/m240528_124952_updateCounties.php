<?php

use yii\db\Migration;

/**
 * Class m240528_124952_updateCounties
 */
class m240528_124952_updateCounties extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%counties}}', [
            'county_id' => $this->primaryKey(),
            'county_name' => $this->string(50)->notNull(),
            'county_code' => $this->integer(10)->notNull(),
            'county_url' => $this->string(255)->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%counties}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240528_094140_counties cannot be reverted.\n";

        return false;
    }
    */
}
