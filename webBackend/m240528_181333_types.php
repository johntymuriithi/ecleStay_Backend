<?php

//use yii\db\Migration;
//
///**
// * Class m240528_170027_services
// */
//class m240528_170027_services extends Migration
//{
//    /**
//     * {@inheritdoc}
//     */
//    public function safeUp()
//    {
//        $this->createTable('{{%services}}', [
//            'service_id' => $this->primaryKey(),
//            'host_id' => $this->integer()->notNull(),
//            'price' => $this->integer(200)->notNull(),
//            'pricing_criteria' => $this->string(50)->notNull(),
//            'type_id' => $this->integer()->notNull(),
//            'description' => $this->string(500)->notNull(),
//            'start_date' => $this->timestamp()->notNull(),
//            'end_date' => $this->timestamp()->notNull(),
//            'approved' => $this->boolean()->defaultValue(false),
//        ]);
//
//        $this->addForeignKey(
//            'fk-type',
//            'services',
//            'type_id',
//            'types',
//            'type_id',
//            'CASCADE'
//        );
//
//        $this->addForeignKey(
//            'fk-host',
//            'services',
//            'host_id',
//            'hosts',
//            'host_id',
//            'CASCADE'
//        );
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function safeDown()
//    {
//        $this->dropForeignKey('fk-type', 'services');
//
//        $this->dropForeignKey('fk-host', 'services');
//
//        $this->dropTable('{{%services}}');
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
//        echo "m240528_170027_services cannot be reverted.\n";
//
//        return false;
//    }
//    */
//}
