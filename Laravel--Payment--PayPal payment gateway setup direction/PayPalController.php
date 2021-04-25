<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\PaymentExecution;

// require __DIR__ . '/../bootstrap.php';
// use PayPal\Api\Amount;
// use PayPal\Api\Details;
// use PayPal\Api\Item;
// use PayPal\Api\ItemList;
// use PayPal\Api\Payer;
// use PayPal\Api\Payment;
// use PayPal\Api\RedirectUrls;
// use PayPal\Api\Transaction;

class PayPalController extends Controller
{
    //==========================================================================
    public function index(Request $request)
    {
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AdE70oJKqBFZBLhNl7CqQol8wuRCAwKIG7NDjlxYPoXb8qMgtkzFeolQtz_gB_4Unv20vWEMAnslz7AN',  // ClientID
                'EGT80vmkvPV1hf5WeoD7TAuPG4ZiAc51hDbAdBvXKLR5-OOBmv6j2ME9zNuz8mDGhNoA7knIlJrpYLbC'   // ClientSecret
            )
        );


        //my New added code
        $apiContext->setConfig(
            array(
                'log.LogEnabled' => true,
                'log.FileName' => 'PayPal.log',
                'log.LogLevel' => 'DEBUG',
                'mode' => 'live'
              )
        );


        // Step 2
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new \PayPal\Api\Amount();
        $amount->setTotal('17.00')->setCurrency('USD');
        // $amount->setCurrency('USD');

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount);

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl(route('paypal_return'))->setCancelUrl(route('paypal_cancel'));

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale')->setPayer($payer)->setTransactions(array($transaction))->setRedirectUrls($redirectUrls);

        // Step 3
        try {
            $payment->create($apiContext);
            echo $payment;
            echo "\n\nRedirect user to approval_url: " . $payment->getApprovalLink() . "\n";
            return redirect($payment->getApprovalLink());
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            // This will print the detailed information on the exception.
            //REALLY HELPFUL FOR DEBUGGING
            echo $ex->getData();
            die();
        }
    }

    //==========================================================================
    public function paypalReturn()
    {
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AdE70oJKqBFZBLhNl7CqQol8wuRCAwKIG7NDjlxYPoXb8qMgtkzFeolQtz_gB_4Unv20vWEMAnslz7AN',  // ClientID
                'EGT80vmkvPV1hf5WeoD7TAuPG4ZiAc51hDbAdBvXKLR5-OOBmv6j2ME9zNuz8mDGhNoA7knIlJrpYLbC'   // ClientSecret
            )
        );
        // dd(\request()->all());

        // Get payment object by passing paymentId
        $paymentId = $_GET['paymentId'];
        $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
        $payerId = $_GET['PayerID'];

        // Execute payment with payer ID
        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {
            // Execute payment
            $result = $payment->execute($execution, $apiContext);
            // dd($result);
            echo "<h3>Payment Success !</h3>";
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }
    }
    //==========================================================================
    public function paypalCancel()
    {
        return "order canceled";
    }
    //==========================================================================
}
