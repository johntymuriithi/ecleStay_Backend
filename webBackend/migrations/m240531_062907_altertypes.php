<?php

use yii\db\Migration;

/**
 * Class m240531_062907_altertypes
 */
class m240531_062907_altertypes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->update('{{%types}}', ['category' => 1]);
        $this->alterColumn('{{%types}}', 'category', $this->integer(1000)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('types', 'category');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240531_062907_altertypes cannot be reverted.\n";

        return false;
    }
    */
}
