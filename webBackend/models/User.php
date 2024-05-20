<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class User extends ActiveRecord implements IdentityInterface
{
    public $password; // For storing the plaintext password during signup

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'second_name', 'email', 'phone', 'password_hash', 'auth_key'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash'], 'string', 'min' => 6],
        ];
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param string $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     * @return string a key that is used to check the validity of a given identity ID.
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given authentication key.
     * @param string $authKey the given authentication key
     * @return bool whether the given authentication key is valid.
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}

?>
