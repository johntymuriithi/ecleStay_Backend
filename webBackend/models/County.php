<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class County extends ActiveRecord
{
    public $countyFile;

    public static function tableName()
    {
        return 'counties';
    }

    public function rules()
    {
        return [
            [['countyFile', 'county_code', 'county_name'], 'required'],
            // county image
            [['countyFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxFiles' => 1, 'message' => 'Please upload a picture.'],
//            [['countyName'], 'string', 'max' => 50],
            [['county_code'], 'integer'],
        ];
    }

    public static function countyImager($imageUrl, $Others)
    {
        return Yii::$app->db->createCommand()->insert(self::tableName(), [
            'county_code' => $Others['county_code'],
            'county_name' => $Others['county_name'],
            'county_url' => $imageUrl,
        ])->execute();
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