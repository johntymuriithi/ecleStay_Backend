<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;

class BaseController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Enable JSON output
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        //        // JWT Authentication (placed after access control) // incase it fails,,please login 401
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'signup', 'viewservice', 'showcategories', 'addcategory', 'servicereviews', 'showguides', 'activateuser',
                'getaccommodations', 'searchtype', 'showtypes', 'addhosts', 'hostreviews', 'addguides', 'guidebycounty', 'callbackurl',
                'showcounties', 'getservices', 'resetpasswordlink', 'resetpassword', 'hostreviews', 'addcounty', 'searchcounty'], // Actions that don't require authentication
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['login', 'signup', 'getservices', 'viewservice', 'callbackurl',
                        'showcategories', 'addcategory', 'getaccommodations', 'searchtype', 'activateuser',
                        'showtypes', 'addhosts', 'addguides', 'showcounties', 'resetpasswordlink', 'showguides',
                        'resetpassword', 'hostreviews', 'addcounty', 'servicereviews', 'searchcounty', 'guidebycounty'],
                    'roles' => ['?'], // Allow guests (unauthenticated users) // in short in mean users
                ],
                [
                    'allow' => true,
                    'actions' => ['showtypes', 'ordernow', 'addhosts', 'addguides', 'toa', 'showcategories', 'reviewhost', 'makepayment',
                        'addservice', 'hostrevews', 'reviewservice', 'showorders', 'servicereviews', 'hostreviews', 'guestservices', 'updateprofilepic'],
                    'roles' => ['@'], // authenticated users only // passed the bearer auth
                ],
                [
                    'allow' => true,
                    'actions' => ['showhosts', 'showcounties', 'addcounty', 'addtypes',
                        'addservice', 'getaccommodations', 'reviewhost', 'hostii', 'userguest',
                        'rolerevoker', 'assignadmin', 'approvedhosts', 'waitinghosts', "userstotal",
                        'showguides', 'assignhost', 'assignguide', 'approvedguides', 'waitingguides', 'hostiorders', 'serviceorders', 'paid'],
                    'roles' => ['admin'], // Require admin role
                ],
                [
                    'allow' => true,
                    'actions' => ['hostii', 'tester', 'hostiorders', 'serviceorders'],
                    'roles' => ['host'], // Require user role
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                throw new \yii\web\ForbiddenHttpException('Sorry,You are not Allowed to Access This ACTION.');
            },
        ];

        return $behaviors;
    }
}
?>