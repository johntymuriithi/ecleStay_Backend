<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Hosts extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%hosts}}';
    }

    public function rules()
    {
        return [
            [['language', 'email', 'about', 'number', 'picture', 'host_name', 'county_id'], 'required'],
            [['language'], 'string', 'max' => 500],
            [['email'], 'string', 'max' => 100],
            [['about'], 'string', 'max' => 100],
            [['picture'], 'string', 'max' => 200],
            [['number'], 'integer'],
            [['host_name'], 'string', 'max' => 200],
            [['approved'], 'boolean'],
            [['county_id'], 'integer'],
            [['email'], 'email'],
        ];
    }
//
//    public function attributeLabels()
//    {
//        return [
//            'host_id' => 'Host ID',
//            'about' => 'About',
//            'host_name' => 'Host Name',
//            'language' => 'Language',
//            'email' => 'Email',
//            'number' => 'Number',
//            'picture' => 'Picture',
//            'county_id' => 'County',
//            'approved' => 'Approved',
//        ];
//    }

    public function getServices()
    {
        return $this->hasMany(Services::class, ['host_id' => 'host_id']);
    }

//    public function getTypes()
//    {
//        return $this->hasMany(Types::class, ['host_id' => 'hosts_id']);
//    }
}