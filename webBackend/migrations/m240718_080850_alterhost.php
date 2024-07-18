<?php

use yii\db\Migration;

/**
 * Class m240718_080850_alterhost
 */
class m240718_080850_alterhost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('hosts', 'created_at', $this->timestamp()->defaultExpression('NULL'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('hosts', 'created_at');

    }
}
