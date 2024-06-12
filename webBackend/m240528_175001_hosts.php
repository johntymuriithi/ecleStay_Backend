<?php
//
//use yii\db\Migration;
//
///**
// * Class m240528_175001_hosts
// */
//class m240528_175001_hosts extends Migration
//{
//    /**
//     * {@inheritdoc}
//     */
//    public function safeUp()
//    {
//        $this->createTable('{{%hosts}}', [
//            'host_id' => $this->primaryKey(),
//            'about' => $this->string(100)->notNull(),
//            'host_name' => $this->string(200)->notNull(),
//            'language' => $this->string(500)->notNull(),
//            'email' => $this->string(100)->notNull(),
//            'number' => $this->integer()->notNull(),
//            'picture' => $this->string(200)->notNull(),
//            'location' => $this->string(50)->notNull(),
//            'approved' => $this->boolean()->defaultValue(false),
//        ]);
//
//        $this->addForeignKey(
//            'fk-location',
//            'hosts',
//            'county_id',
//            'counties',
//            'county_id',
//            'CASCADE'
//        );
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function safeDown()
//    {
//        $this->dropForeignKey('fk-location', 'hosts');
//        $this->dropTable('{{%hosts}}');
//    }
//
//    /*
//    // Use up()/down() to run migration code without a transaction.
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m240528_175001_hosts cannot be reverted.\n";
//
//        return false;
//    }
//    */
//}
