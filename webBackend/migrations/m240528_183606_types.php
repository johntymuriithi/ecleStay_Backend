<?php

use yii\db\Migration;

/**
 * Class m240528_183606_types
 */
class m240528_183606_types extends Migration
{
    /**
     * {@inheritdoc}
     */
        public function safeUp()
    {
        $this->createTable('{{%types}}', [
            'type_id' => $this->primaryKey(),
            'type_name' => $this->string(200)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%types}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240528_183606_types cannot be reverted.\n";

        return false;
    }
    */
}
