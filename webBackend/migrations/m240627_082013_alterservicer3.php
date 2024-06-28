<?php

use yii\db\Migration;

/**
 * Class m240627_082013_alterservicer3
 */
class m240627_082013_alterservicer3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('servicer', 'order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240627_082013_alterservicer3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240627_082013_alterservicer3 cannot be reverted.\n";

        return false;
    }
    */
}
