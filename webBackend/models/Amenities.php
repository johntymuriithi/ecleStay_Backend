<?php
namespace app\models;

use yii\db\ActiveRecord;

class Amenities extends ActiveRecord {
    public static function tableName()
    {
        return 'amenities';
    }

    public function rules()
    {
        return [
            [['amenity_name', 'service_id'], 'required'],
        ];
    }

//    public function getServices() {
//        return $this->hasMany(Services::class, ['type_id' => 'type_id']);
//    }
//
//    public function getCategories() {
//        return $this->hasOne(Categories::class, ['category_id' => 'category']);
//    }
}

?>