<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Servicer extends ActiveRecord
{

    public static function tableName()
    {
        return 'servicer';
    }

    public function rules()
    {
        return [
            [['service_id', 'description', 'rating', 'cleanliness', 'location', 'communication', 'days_stayed', 'user_id'], 'required'],
        ];
    }

    public function getServices()
    {
        return $this->hasOne(Services::class, ['service_id' => 'service_id']);
    }
}

?>