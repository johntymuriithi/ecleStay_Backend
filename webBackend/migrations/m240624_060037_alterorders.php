<?php

use yii\db\Migration;

/**
 * Class m240624_060037_alterorders
 */
class m240624_060037_alterorders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orders', 'amount', $this->integer()->notNull());
        $this->addColumn('orders', 'guests_number', $this->integer()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('orders', 'amount');
        $this->dropColumn('orders', 'guests_number');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240624_060037_alterorders cannot be reverted.\n";

        return false;
    }
    */
}
