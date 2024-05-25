<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class UserTokens extends ActiveRecord {

    public static function tableName()
    {
        return 'user_jwt_tokens';
    }

    public function rules()
    {
        return [
            [['user_id', 'jwt_token', 'created_at', 'expires_at'], 'required'],
            [['user_id'], 'integer'],
            [['created_at', 'expires_at'], 'safe'],
            [['jwt_token'], 'string', 'max' => 255],

        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'id']);
    }
}

?>