<?php

use yii\db\Migration;

/**
 * Class m240531_062420_Categories
 */
class m240531_062420_Categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categories}}', [
            'category_id' => $this->primaryKey(),
            'category_name' => $this->string(200)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%categories}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240531_062420_Categories cannot be reverted.\n";

        return false;
    }
    */
}
