<?php

use yii\db\Migration;

/**
 * Class m240620_113807_alterservicer
 */
class m240620_113807_alterservicer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('hoster', 'reviewTrials', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('hoster', 'reviewTrials');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240620_113807_alterservicer cannot be reverted.\n";

        return false;
    }
    */
}
