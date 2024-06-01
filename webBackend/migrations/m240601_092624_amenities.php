<?php

use yii\db\Migration;

/**
 * Class m240601_092624_amenities
 */
class m240601_092624_amenities extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%amenities}}', [
            'amenity_id' => $this->primaryKey(),
            'amenity_name' => $this->string(200)->notNull(),
            'service_id' => $this->integer(100)
        ]);

        $this->addForeignKey(
            'fk-services',
            'amenities',
            'service_id',
            'services',
            'service_id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fkfk-services', '{{%amenities}}');
        $this->dropTable('{{%amenities}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240601_092624_amenities cannot be reverted.\n";

        return false;
    }
    */
}
