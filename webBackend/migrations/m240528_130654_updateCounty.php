<?php

use yii\db\Migration;

/**
 * Class m240528_130654_updateCounty
 */
class m240528_130654_updateCounty extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->alterColumn('counties', 'county_name', $this->string(51)->unique()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('counties', 'county_name');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240528_130654_updateCounty cannot be reverted.\n";

        return false;
    }
    */
}
