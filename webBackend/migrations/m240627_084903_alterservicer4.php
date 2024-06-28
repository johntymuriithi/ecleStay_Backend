<?php

use yii\db\Migration;

/**
 * Class m240627_084903_alterservicer4
 */
class m240627_084903_alterservicer4 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('servicer', 'days_stayed', $this->string(200)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240627_084903_alterservicer4 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240627_084903_alterservicer4 cannot be reverted.\n";

        return false;
    }
    */
}
