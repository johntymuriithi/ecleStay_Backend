<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m240520_130500_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create the user table
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->notNull(),
            'second_name' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Create the trigger function for automatic updating of the updated_at column
        $this->execute('
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = NOW();
                RETURN NEW;
            END;
            $$ language \'plpgsql\';
        ');

        // Create the trigger for the user table
        $this->execute('
            CREATE TRIGGER update_user_updated_at
            BEFORE UPDATE ON {{%user}}
            FOR EACH ROW
            EXECUTE FUNCTION update_updated_at_column();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop the trigger first
        $this->execute('DROP TRIGGER IF EXISTS update_user_updated_at ON {{%user}}');

        // Drop the trigger function
        $this->execute('DROP FUNCTION IF EXISTS update_updated_at_column()');

        // Drop the user table
        $this->dropTable('{{%user}}');
    }
}

?>
