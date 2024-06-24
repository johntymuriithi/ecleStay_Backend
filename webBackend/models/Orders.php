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
            [['user_id', 'service_id', 'amount', 'guests_number', 'begin_date', 'end_date', 'billing_address', 'city', 'state', 'zip_code'], 'required'],
            // please valid rules later and don't fail to do so
        ];
    }
}