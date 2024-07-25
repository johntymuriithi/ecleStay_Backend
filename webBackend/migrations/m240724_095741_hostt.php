<?php

use yii\db\Migration;

/**
 * Class m240724_095741_hostt
 */
class m240724_095741_hostt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%hosts}}', 'number', $this->string(100)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240724_095741_hostt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240724_095741_hostt cannot be reverted.\n";

        return false;
    }
    */
}
