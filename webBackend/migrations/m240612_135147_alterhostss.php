<?php

use yii\db\Migration;

/**
 * Class m240612_135147_alterhostss
 */
class m240612_135147_alterhostss extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('hosts', 'host_image');
    }

    /**
     * {@inheritdoc}
     */
    // Use up()/down() to run migration code without a transaction.
}
