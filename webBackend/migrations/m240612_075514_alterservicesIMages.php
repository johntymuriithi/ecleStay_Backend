<?php

use yii\db\Migration;

/**
 * Class m240612_075514_alterservicesIMages
 */
class m240612_075514_alterservicesIMages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%serviceImages}}', [
            'image_id' => $this->primaryKey(),
            'service_id' => $this->integer(200)->notNull(),
            'service_image' => $this->string(200)->notNull(),
        ]);

        $this->addForeignKey(
            'fk-serviceImages',
            'serviceImages',
            'service_id',
            'services',
            'service_id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-serviceImages', 'serviceImages');
        $this->dropTable('{{%serviceImages}}');
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240612_075514_alterservicesIMages cannot be reverted.\n";

        return false;
    }
    */
}
