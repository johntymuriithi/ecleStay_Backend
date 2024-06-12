<?php
namespace app\controllers;

use app\models\Hosts;
use app\models\Services;
use Cassandra\Value;
use Yii;
use yii\base\Security;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\ConflictHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\PasswordResetToken;
use yii\filters;
//use app\components\JwtMiddleware;
//use app\components\BaseController;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User';// specifies the model this controller will use
    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;

        $requiredFields = ['first_name', 'second_name', 'email', 'phone', 'password'];

        foreach ($requiredFields as $field) {
            if (!isset($params[$field])) {
                return ['status' => false, 'message' => "Field '$field' is required"];
            }
        }
        $user = new User();
        $user->first_name = $params['first_name'];
        $user->second_name = $params['second_name'];
        $user->email = $params['email'];
        $user->phone = $params['phone'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->generateActivationToken();

        $availableUser = User::findOne(['email' => $params['email']]);
        $phone = User::findOne(['phone' => $params['phone']]);
        if ($user->save()) {
            // here oooo
            $auth = Yii::$app->authManager;
            $userRole = $auth->getRole('user');
            $auth->assign($userRole, $user->id);

            $user->blocked = false;
            $user->login_trials = 0;
            $user->save();
            $tokenJWT = $user->generateJwt();
            Yii::$app->mailer->compose()
                ->setFrom('ecleStay-no-reply@gmail.com')
                ->setTo($user->email)
                ->setSubject('Welcome to ecleStay')
                ->setHtmlBody("<p>Welcome {$user->first_name} {$user->second_name}, to <h1>EcliStay</h1></p>
<p>Please click below Button to activate your account</p>
<a href='http://localhost:8080/user/activateuser?token={$user->activationToken}'>Activate Account</a>")
                ->send();
            return ['status' => 200, 'message' => 'User Sign up was Successful'];
        } elseif ($availableUser != null) {
            throw new ConflictHttpException("User with the same Email Exists");
        }elseif ($phone != null) {
            throw new ConflictHttpException("User with the same Number Exists");
        } else{
            return ['status' => false, 'errors' => $user->errors];
        }
    }
    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $params['email']]);
        if (!$user) {
            throw new NotFoundHttpException("User does not exists");
        } else {
            if (!$user->blocked){
                if ($user->userActive === true) {
                    if ($user && Yii::$app->security->validatePassword($params['password'], $user->password_hash)) {
                        $tokenJWTs = $user->generateJwt();
                        // give the user role here
                        $userRole =
                        $user->blocked = false;
                        $user->login_trials = 0;
                        $user->save();

                        return ['status' => 200, 'data' => ['token' => $tokenJWTs], 'Message' => 'Logged in Successfully'];
                    } else {
                        $trials = $user->login_trials;
                        $user->login_trials = $trials + 1;
                        $user->save();

                        if ($user->login_trials >= 3) {
                            $user->blocked = true;
                            $user->save();
                            throw new ForbiddenHttpException("User Blocked");
                        }
                        throw new BadRequestHttpException('Invalid password');
                    }
                } else {
                    throw new UnauthorizedHttpException("Please Active your account using the email sent to YOU!");
                }
            } else {
                Yii::$app->response->statusCode = 403;
                return [
                    'status' => 403,
                    'message' => 'User is temporarily blocked, please reset your password to continue',
                ];
                // frontend to redirect to reset password thingy
            }
        }
    }

    public function actionResetpasswordlink() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $args = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $args['email']]);

        if ($user) {
            $userId = $user->id;
            $token = Yii::$app->security->generateRandomString(64);
            $expireDate = time() + 120;
            if (PasswordResetToken::createToken($userId, $token, $expireDate)) {
                // Send the token to the user via email
                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['resetpasswordlink', 'token' => $token]);
                Yii::$app->mailer->compose()
                    ->setFrom('ecleStay-password-reset@gmail.com')
                    ->setTo($user->email)
                    ->setSubject('Password Reset')
                    ->setHtmlBody("<p>Hello {$user->first_name},</p><p>We have recieved a reqeust for a reset of password.
Click link below change your password <h1>Reset Link:</h1><i><a href='{$resetLink}'>Reset Password Here</a></i>
<p>Please do ignore this Link if you didn't request it, Thank You</p>")
                    ->send();

                return ["Status" => '200 OK', "resentLink" => $resetLink, "token" => $token];
            } else {
                throw new \RuntimeException('Failed to create password reset token.');
            }
        } else {
            throw new UnauthorizedHttpException("User with the email does not exists");
        }
    }

    public function actionResetpassword($token)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userParams = Yii::$app->request->bodyParams;
        $hashedTokenFromUser = hash('sha256', $token);
        $user = User::findOne(['email' => $userParams['email']]);
        if (!$user) {
            throw new NotFoundHttpException("Email user does not exist");
        }
        $userId = $user->id;
        $userRows = PasswordResetToken::findAll(['user_id' => $userId]);
        $validToken = null;
        foreach ($userRows as $idToken) {
            if ($idToken->token === $hashedTokenFromUser && $idToken->token_expiry > time()) {
                $validToken = $idToken;
                break;
            }
        }
        if ($validToken) {
            $newPassword = $userParams['password'];
            $newPasswordHashed = Yii::$app->security->generatePasswordHash($newPassword);
            $user->password_hash = $newPasswordHashed;

            if ($user->blocked === true) {
                $user->blocked = false;
                $user->login_trials = 0;
                $user->save();
            }
            if ($user->save()) {
                foreach ($userRows as $row) {
                    $row->delete();
                }
                return ["status" => 200, "message" => "Reset Successful, Continue with $newPassword as your password"];
            } else {
                throw new NotAcceptableHttpException("Not acceptable, Sorry");
            }
        } else {
            throw new NotFoundHttpException("Invalid or expired token");
        }
    }


    // don'$this->do thos in production please this is for testing only
    public function actionToa()
    {
        $users = Hosts::find()->all();
        foreach ($users as $user) {

            $user->delete();
        }
    }

    public function actionActivateuser($token)
    {
        $user = User::findOne(['activationToken' => $token]);

        if ($user && !$user->userActive) {
            $user->userActive = true;
            $user->activationToken = null; // Clear the token after activation
            if ($user->save()) {
                // Redirect to frontend login page
                return "Activated successfully, Waiting for frontend guys now";
//                return $this->redirect('https://fe59-41-90-101-26.ngrok-free.app');
            } else {
                throw new BadRequestHttpException("Activation Failed");
            }
        } else {
            throw new BadRequestHttpException("Invalid or Expired Token");
        }
    }


    // get the number of registered users in our website

    public function actionUserstotal() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $total = User::find()->count();

        return ["users" => $total];
    }

}
?>