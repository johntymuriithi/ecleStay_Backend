<?php
namespace app\models;

use yii\db\ActiveRecord;

class Categories extends ActiveRecord {
    public static function tableName()
    {
        return 'categories';
    }

    public function rules()
    {
        return [
            [['category_name'], 'required'],
            [['category_name'], 'string', 'max' => 30],
        ];
    }

    public function getTypes() {
        return $this->hasMany(Services::class, ['category_id' => 'category']);
    }
}

?>