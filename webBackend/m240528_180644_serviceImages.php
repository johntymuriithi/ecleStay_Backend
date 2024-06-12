<?php
//
//use yii\db\Migration;
//
///**
// * Class m240528_180644_serviceImages
// */
//class m240528_180644_serviceImages extends Migration
//{
//    /**
//     * {@inheritdoc}
//     */
//    public function safeUp()
//    {
//        $this->createTable('{{%serviceImages}}', [
//            'image_id' => $this->primaryKey(),
//            'service_id' => $this->integer(200)->notNull(),
//            'image_url' => $this->string(200)->notNull(),
//        ]);
//
//        $this->addForeignKey(
//            'fk-serviceImages',
//            'serviceImages',
//            'service_id',
//            'services',
//            'service_id',
//            'CASCADE'
//        );
//    }
//
//    public function safeDown()
//    {
//        $this->dropForeignKey('fk-serviceImages', 'serviceImages');
//        $this->dropTable('{{%serviceImages}}');
//    }
//    /*
//    // Use up()/down() to run migration code without a transaction.
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m240528_180644_serviceImages cannot be reverted.\n";
//
//        return false;
//    }
//    */
//}
