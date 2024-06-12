<?php

use yii\db\Migration;

/**
 * Class m240601_093532_roles
 */
class m240601_093532_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%roles}}', [
            'role_id' => $this->primaryKey(),
            'role_name' => $this->string(200)->notNull(),
            'service_id' => $this->integer(100)
        ]);

        $this->addForeignKey(
            'fk-roles',
            'roles',
            'service_id',
            'services',
            'service_id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-roles', '{{%roles}}');
        $this->dropTable('{{%roles}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240601_093532_roles cannot be reverted.\n";

        return false;
    }
    */
}
