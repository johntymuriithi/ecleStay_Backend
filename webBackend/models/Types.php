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
            [['type_name', 'category', 'hosts_id'], 'required'],
        ];
    }

    public function getServices() {
        return $this->hasMany(Services::class, ['type_id' => 'type_id']);
    }

    public function getCategories() {
        return $this->hasOne(Categories::class, ['category_id' => 'category']);
    }
}

?>