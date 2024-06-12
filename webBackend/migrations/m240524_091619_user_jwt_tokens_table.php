<?php

use yii\db\Migration;

/**
 * Class m240524_091619_user_jwt_tokens_table
 */
class m240524_091619_user_jwt_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_jwt_tokens}}', [
            'jwt_id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'jwt_token' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'expires_at' => $this->timestamp()->notNull()
        ]);

        $this->addForeignKey(
            'fk-jwt_users',
            'user_jwt_tokens',
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
        $this->dropTable('[{%user_jwt_tokens}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240524_091619_user_jwt_tokens_table cannot be reverted.\n";

        return false;
    }
    */
}
