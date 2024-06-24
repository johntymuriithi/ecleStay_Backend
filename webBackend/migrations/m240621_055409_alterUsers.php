<?php

use yii\db\Migration;

/**
 * Class m240621_055409_alterUsers
 */
class m240621_055409_alterUsers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'profilePic', $this->string(200)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'profilePic');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240621_055409_alterUsers cannot be reverted.\n";

        return false;
    }
    */
}
