<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Guides extends ActiveRecord
{
    public $imageFile;
    public $businessFile;

    public static function tableName()
    {
        return '{{%guides}}';
    }

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 1, 'message' => 'Please upload a picture.'],
            // bst docs
            [['businessFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf, docx', 'maxFiles' => 1, 'message' => 'Please upload a file.'],
            [['language', 'email', 'about', 'number', 'guide_name', 'county_id', 'imageFile'], 'required'],
            [['language'], 'string', 'max' => 500],
            [['email'], 'string', 'max' => 100],
            [['about'], 'string', 'max' => 100],
            [['picture'], 'string', 'max' => 200],
            [['number'], 'integer'],
            [['guide_name'], 'string', 'max' => 200],
            [['approved'], 'boolean'],
            [['county_id'], 'integer'],
            [['email'], 'email'],
        ];
    }

    public static function hostImager($imageUrl, $Others, $biz)
    {
        return Yii::$app->db->createCommand()->insert(self::tableName(), [
            'guide_name' => $Others['guide_name'],
            'language' => $Others['language'],
            'email' => $Others['email'],
            'about' => $Others['about'],
            'number' => intval($Others['number']),
            'county_id' => intval($Others['location']),
            'business_doc' => $biz,
            'picture' => $imageUrl,
        ])->execute();
    }
//    public function extraFields()
//    {
//        return [
//            'hostReviews' => function () {
//                // Fetch host reviews and return them
//                return Yii::$app->runAction('hoster/hostreviews', ['id' => $this->host_id]);
//            },
//        ];
//    }
}