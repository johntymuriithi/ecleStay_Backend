<?php

use yii\db\Migration;

/**
 * Class m240522_141101_user_password_tokens_table
 */
class m240522_141101_user_password_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_password_tokens_table"}}', [
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(128)->notNull(),
            'token_expiry' => $this->bigInteger()->notNull(),
            'PRIMARY KEY(user_id, token)'
        ]);

        $this->addForeignKey(
            'fk-token-password',
            'user_password_tokens_table',
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
        $this->dropForeignKey(
            'fk-token-password',
            '%user_password_tokens_table'
        );

        $this->dropTable(
            '{{%user_password_tokens_table}}'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240522_141101_user_password_tokens_table cannot be reverted.\n";

        return false;
    }
    */
}
