<?php

namespace app\controllers;

use app\models\County;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class PaymentController extends  BaseController {
    public $modelClass = 'app/model/Orders';


    public function actionMakepayment() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $params = \Yii::$app->request->bodyParams;
        $amount = $params['amount'];
        $number = $params['number'];

            date_default_timezone_set('Africa/Nairobi');

            # access token
            $consumerKey = 'zHTpme4GzVMm1fGkG43Htyk3ALfuo6LY4E4ckKz4F21UVWAC'; //Fill with your app Consumer Key
            $consumerSecret = 'PynblMosRMQa7c6Tf6SekFCVVIsWEueslUqqROGtcoT5GXx5WZGUehiMXutl3VKN'; // Fill with your app Secret

            # define the variales
            # provide the following details, this part is found on your test credentials on the developer account
            $BusinessShortCode = '174379';
            $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

            /*
              This are your info, for
              $PartyA should be the ACTUAL clients phone number or your phone number, format 2547********
              $AccountRefference, it maybe invoice number, account number etc on production systems, but for test just put anything
              TransactionDesc can be anything, probably a better description of or the transaction
              $Amount this is the total invoiced amount, Any amount here will be
              actually deducted from a clients side/your test phone number once the PIN has been entered to authorize the transaction.
              for developer/test accounts, this money will be reversed automatically by midnight.
            */

            $PartyA = intval($number); // This is your phone number,
            $AccountReference = 'EcliStay';
            $TransactionDesc = 'Test Payment';
            $Amount = $amount;

            # Get the timestamp, format YYYYmmddhms -> 20181004151020
            $Timestamp = date('YmdHis');

            # Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
            $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

            # header for access token
            $headers = ['Content-Type:application/json; charset=utf8'];

            # M-PESA endpoint urls
            $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
            $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

            # callback url
            $CallBackURL = 'https://52ca-41-80-113-0.ngrok-free.app/payment/callback';

            $curl = curl_init($access_token_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
            $result = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $result = json_decode($result);
            $access_token = $result->access_token;
            curl_close($curl);

            # header for stk push
            $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

            # initiating the transaction
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $initiate_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

            $curl_post_data = array(
                //Fill in the request parameters with valid values
                'BusinessShortCode' => $BusinessShortCode,
                'Password' => $Password,
                'Timestamp' => $Timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $Amount,
                'PartyA' => $PartyA,
                'PartyB' => $BusinessShortCode,
                'PhoneNumber' => $PartyA,
                'CallBackURL' => $CallBackURL,
                'AccountReference' => $AccountReference,
                'TransactionDesc' => $TransactionDesc
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $curl_response = curl_exec($curl);

        $responseArray = json_decode($curl_response, true);

        if ($responseArray['ResponseCode'] == 0) {
            return ['status' => 200, 'message' => 'Payment of amount' . " " . $amount . " ". 'By number' . " " . $number. " ". 'was successful'];
        } else {
            throw new BadRequestHttpException("Payment failed, please try again later");
        }

//        echo $responseArray['ResponseCode'];

    }

    public function actionCallbackurl() {

        header("Content-Type: application/json");

        $response = '{
         "ResultCode": 0, 
         "ResultDesc": "Confirmation Received Successfully"
     }';

        // DATA
        $mpesaResponse = file_get_contents('php://input');

        // log the response
        $logFile = "M_PESAConfirmationResponse.txt";

        // write to file
        $log = fopen($logFile, "a");

        fwrite($log, $mpesaResponse);
        fclose($log);

//        return $response;
    }

}

?>