<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PasswordResetToken extends ActiveRecord
{
    /**
     * @var mixed|null
     */
    public $user_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_password_tokens_table';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'token', 'token_expiry'], 'required'],
            [['user_id'], 'string', 'max' => 36],
            [['token'], 'string', 'max' => 128],
            [['token_expiry'], 'integer'],
        ];
    }

    /**
     * Creates a new password reset token for a user.
     *
     * @param string $userId
     * @param string $token
     * @param int $tokenExpiry
     * @return bool
     */
    public static function createToken($userId, $token, $tokenExpiry)
    {
        $hashedToken = hash('sha256', $token);

        return Yii::$app->db->createCommand()->insert(self::tableName(), [
            'user_id' => $userId,
            'token' => $hashedToken,
            'token_expiry' => $tokenExpiry,
        ])->execute();
    }
}

?>
