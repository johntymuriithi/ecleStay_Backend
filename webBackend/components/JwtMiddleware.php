<?php
// your-project-directory/components/JwtAuth.php

namespace app\components;

use yii\filters\auth\AuthMethod;
use Yii;
use yii\web\UnauthorizedHttpException;

class JwtAuth extends AuthMethod
{
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            $identity = $user->loginByAccessToken($matches[1], get_class($this));
            if ($identity === null) {
                $this->handleFailure($response);
            }
            return $identity;
        }
        return null;
    }

    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Your request was made with invalid credentials');
    }
}


?>