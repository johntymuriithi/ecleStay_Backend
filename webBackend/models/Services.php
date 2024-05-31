<?php
//namespace app\models;
//
//use Yii;
//use yii\db\ActiveRecord;
//
//class Services extends ActiveRecord
//{
//    public static function tableName()
//    {
//        return '{{%services}}';
//    }
//
//    public function rules()
//    {
//        return [
//            [['price', 'pricing_criteria', 'description', 'type_id', 'host_id', 'start_date','end_date', 'county_id'], 'required'],
//            [['price'], 'integer'],
//            [['pricing_criteria'], 'string', 'max' => 50],
//            [['description'], 'string', 'max' => 500],
//            [['host_id'], 'integer'],
//            [['type_id'], 'integer'],
////            [['start_date'], 'string', 'max' => 200],
////            [['end_date'], 'string', 'max' => 200],
//            [['approved'], 'boolean'],
//            [['county_id'], 'integer'],
//        ];
//    }
//
////    public function attributeLabels()
////    {
////        return [
////            'host_id' => 'Host ID',
////            'about' => 'About',
////            'host_name' => 'Host Name',
////            'language' => 'Language',
////            'email' => 'Email',
////            'number' => 'Number',
////            'picture' => 'Picture',
////            'county_id' => 'County',
////            'approved' => 'Approved',
////        ];
////    }
//
//    public function getImages()
//    {
//        return $this->hasMany(Images::class, ['service_id' => 'service_id']);
//    }
//
//    public function getHosts()
//    {
//        return $this->hasOne(Hosts::class, ['host_id' => 'host_id']);
//    }
//
//    public function getCounty()
//    {
//        return $this->hasOne(County::class, ['county_id' => 'county_id']);
//    }
//
//    public function getTypes()
//    {
//        return $this->hasOne(Types::class, ['type_id' => 'type_id']);
//    }
//}

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Services extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%services}}';
    }

    public function rules()
    {
        return [
            [['price', 'pricing_criteria', 'description', 'type_id', 'host_id', 'start_date', 'end_date', 'county_id'], 'required'],
            [['price', 'host_id', 'type_id', 'county_id'], 'integer'],
            [['pricing_criteria'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 500],
            [['approved'], 'boolean'],
        ];
    }

    public function getImages()
    {
        return $this->hasMany(Images::class, ['service_id' => 'service_id']);
    }

    public function getHosts()
    {
        return $this->hasOne(Hosts::class, ['host_id' => 'host_id']);
    }

    public function getCounty()
    {
        return $this->hasOne(County::class, ['county_id' => 'county_id']);
    }

    public function getTypes()
    {
        return $this->hasOne(Types::class, ['type_id' => 'type_id']);
    }


//    public function fields()
//    {
//        $fields = parent::fields();
//
//        // Explicitly include the related fields
//        $fields['images'] = function () {
//            return $this->images;
//        };
//        $fields['county'] = function () {
//            return $this->county;
//        };
//        $fields['hosts'] = function () {
//            return $this->hosts;
//        };
//        $fields['types'] = function () {
//            return $this->types;
//        };
//
//        return $fields;
//    }
}

