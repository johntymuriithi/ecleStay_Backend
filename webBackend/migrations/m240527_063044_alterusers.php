<?php

use yii\db\Migration;

/**
 * Class m240527_063044_alterusers
 */
class m240527_063044_alterusers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('user', 'userActive', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'userActive');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240527_063044_alterusers cannot be reverted.\n";

        return false;
    }
    */
}
