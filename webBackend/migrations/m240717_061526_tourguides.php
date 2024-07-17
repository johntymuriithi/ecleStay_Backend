<?php

use yii\db\Migration;

/**
 * Class m240717_061526_tourguides
 */
class m240717_061526_tourguides extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%guides}}', [
            'guide_id' => $this->primaryKey(),
            'about' => $this->string(100)->notNull(),
            'guide_name' => $this->string(200)->notNull(),
            'language' => $this->string(500)->notNull(),
            'email' => $this->string(100)->notNull(),
            'number' => $this->integer()->notNull(),
            'picture' => $this->string(200)->notNull(),
            'county_id' => $this->integer()->notNull(),
            'business_doc' => $this->string(100)->notNull(),
            'approved' => $this->boolean()->defaultValue(false),

        ]);

        $this->addForeignKey(
            'fk-county',
            'guides',
            'county_id',
            'counties',
            'county_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-county', 'guides');
        $this->dropTable('{{%guides}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240717_061526_tourguides cannot be reverted.\n";

        return false;
    }
    */
}
