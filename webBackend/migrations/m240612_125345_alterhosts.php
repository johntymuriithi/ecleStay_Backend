<?php

use yii\db\Migration;

/**
 * Class m240612_125345_alterhosts
 */
class m240612_125345_alterhosts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('hosts', 'host_image', $this->string(100)->notNull());
        $this->addColumn('hosts', 'business_doc', $this->string(100)->notNull());
        $this->addColumn('hosts', 'business_name', $this->string(100)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('hosts', 'business_doc');
        $this->dropColumn('hosts', 'hosts_image');
        $this->dropColumn('hosts', 'business_name');

    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240612_125345_alterhosts cannot be reverted.\n";

        return false;
    }
    */
}
