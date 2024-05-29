<?php
namespace app\models;

use yii\db\ActiveRecord;

class Types extends ActiveRecord {
    public static function tableName()
    {
        return 'types';
    }

    public function rules()
    {
        return [
            [['type_name'], 'required'],
        ];
    }

    public function getServices() {
        $this->hasMany(Services::class, ['type_id', 'type_id']);
    }
}
?>