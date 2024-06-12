<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class County extends ActiveRecord
{
    public static function tableName()
    {
        return 'counties';
    }

    public function rules()
    {
        return [
            [['county_name', 'county_code', 'county_url'], 'required'],
            [['county_name'], 'string', 'max' => 50],
            [['county_code'], 'integer'],
            [['county_url'], 'string', 'max' => 255]
        ];
    }

    public function getServices()
    {
        return $this->hasMany(Services::class, ['county_id' => 'county_id']);
    }

    public function getHosts()
    {
        return $this->hasMany(Hosts::class, ['county_id' => 'county_id']);
    }
}

?>