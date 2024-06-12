<?php

use yii\db\Migration;

/**
 * Class m240527_082051_activate
 */
class m240527_082051_activate extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'activationToken', $this->string(255)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'activationToken');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240527_082051_activate cannot be reverted.\n";

        return false;
    }
    */
}
