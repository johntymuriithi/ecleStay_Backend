<?php

namespace app\models;

use yii\db\ActiveRecord;

class Images extends ActiveRecord {
    public static function tableName()
    {
        return 'serviceImages';
    }

    public function rules()
    {
        return [
            [['service_id', 'image_url'], 'required'],
            [['service_id'], 'integer'],
            [['image_url'], 'string', 'max' => 200],
        ];
    }

    public function getService()
    {
        return $this->hasOne(Services::class, ['service_id' => 'service_id']);
    }
}

?>
