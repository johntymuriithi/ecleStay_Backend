<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Hoster extends ActiveRecord
{

    public static function tableName()
    {
        return 'hoster';
    }

    public function rules()
    {
        return [
            [['host_id', 'description', 'rating'], 'required'],
        ];
    }

    public function getHosts()
    {
        return $this->hasOne(Hosts::class, ['host_id' => 'host_id']);
    }
}

?>