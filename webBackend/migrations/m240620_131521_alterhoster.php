<?php

use yii\db\Migration;

/**
 * Class m240620_131521_alterhoster
 */
class m240620_131521_alterhoster extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('hoster', 'user_id', $this->integer()->notNull());
        $this->dropColumn('hoster', 'order_id');

        $this->addForeignKey(
            'fk-user',
            'hoster',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('hoster', 'user_id');
        $this->dropForeignKey('fk-user', 'hoster');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240620_131521_alterhoster cannot be reverted.\n";

        return false;
    }
    */
}
