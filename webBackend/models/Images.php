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
            [['image_url'], 'string', 'max' => 200],
            [['service_id'], 'integer']
        ];
    }

//    public function getServices() {
//        $this->hasMany(Services::class, ['type_id', 'type_id']);
//    }
}
?>