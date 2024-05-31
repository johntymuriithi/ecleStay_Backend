<?php

use yii\db\Migration;


class  m240531_081355_altertypess extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%types}}', 'hosts_id', $this->integer(100));
        $this->update('{{%types}}', ['hosts_id' => 14]);
        $this->alterColumn('{{%types}}', 'hosts_id', $this->integer(100)->notNull());

        $this->addForeignKey(
            'fk-category',
            'types',
            'category',
            'categories',
            'category_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-hosts',
            'types',
            'hosts_id',
            'hosts',
            'host_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-category', 'types');
        $this->dropForeignKey('fk-hosts', 'types');
        $this->dropColumn('types', 'hosts_id');
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
