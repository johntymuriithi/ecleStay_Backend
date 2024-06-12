<?php

use yii\db\Migration;

/**
 * Class m240601_091334_alterservices
 */
class m240601_091334_alterservices extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('services', 'service_name', $this->string(200));
        $this->update('services', ['service_name' => 'One Stop Hotel']);
        $this->alterColumn('services', 'service_name', $this->string(200)->notNull());
        $this->addColumn('services', 'guests', $this->integer(200)->defaultValue(null));
        $this->addColumn('services', 'bedroom', $this->integer(200)->defaultValue(null));
        $this->addColumn('services', 'beds', $this->integer(200)->defaultValue(null));
        $this->addColumn('services', 'bath', $this->integer(200)->defaultValue(null));
        $this->addColumn('services', 'cancellation_policy', $this->string(200)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('services', 'bedroom');
        $this->dropColumn('services', 'beds');
        $this->dropColumn('services', 'bath');
        $this->dropColumn('services', 'guests');
        $this->dropColumn('services', 'cancellation_policy');
        $this->dropColumn('services', 'service_name');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240601_091334_alterservices cannot be reverted.\n";

        return false;
    }
    */
}
