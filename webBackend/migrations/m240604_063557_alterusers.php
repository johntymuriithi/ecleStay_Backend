<?php

use yii\db\Migration;

/**
 * Class m240604_063557_alterusers
 */
class m240604_063557_alterusers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'login_trials', $this->integer(5));
        $this->update('user', ['login_trials' => 0]);
        $this->addColumn('user', 'blocked', $this->boolean());
        $this->update('user', ['blocked' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'login_trails');
        $this->dropColumn('user', 'blocked');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240604_063557_alterusers cannot be reverted.\n";

        return false;
    }
    */
}
