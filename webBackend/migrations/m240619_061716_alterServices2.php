<?php

use yii\db\Migration;

/**
 * Class m240619_061716_alterServices2
 */
class m240619_061716_alterServices2 extends Migration
{
    public function safeUp()
    {

        $this->addForeignKey(
            'fk-types',
            'services',
            'type_id',
            'types',
            'type_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-types', 'services');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240619_061716_alterServices2 cannot be reverted.\n";

        return false;
    }
    */
}
