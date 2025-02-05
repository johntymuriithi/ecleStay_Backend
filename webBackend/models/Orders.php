<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%orders}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'service_id', 'amount', 'guests_number', 'host_id', 'begin_date', 'end_date', 'billing_address', 'city', 'state', 'zip_code'], 'required'],
            // please valid rules later and don't fail to do so
        ];
    }

    public function getUser()
    {
        return $this->hasOne(Services::class, ['user_id' => 'id']);
    }

    public function getServices()
    {
        return $this->hasOne(Services::class, ['service_id' => 'service_id']);
    }

}