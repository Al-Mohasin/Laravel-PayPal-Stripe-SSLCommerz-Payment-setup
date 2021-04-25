<?php
/*==============================================================================
            PayPal Payment Gateway setup direction.
================================================================================
1) At first you have to create a account in "paypal.com"
2) Login & enter "DEVELOPER" option, maybe that's link: "https://developer.paypal.com/docs/business/".
3) Now you mouse-hover your name and enter "Dashboard" option, maybe that's link: "https://developer.paypal.com/developer/applications/".
3) In DEVELOPER Dashboard you hanve to enter left sidebar "SANDBOX > Accounts" option, maybe that's link: "https://developer.paypal.com/developer/accounts/"
4) In Sandbox Account you can see by default created two account for testing. That's one is "Business" & another is "Personal" account. This Business account information you can use for take money from Buyer account for testing purpuse & Personal account information you can use for pay money as a Buyer for testing purpuse. Also you can make new Business & Personal account by clicking "Create Account" button for testing.
5) Now you have to enter "My apps & credentials" option. You have to create App & use that's information later.
6) When you use want real Transaction so you have to create App under Live option & use thats information.
*/

//==============================================================================
// Create Button where you want to click for pay and input money for here Example...
<form action="{{route('paypal_call')}}" method="post">
@csrf
    <input type="number" name="payable_amount" value="">
    <button type="submit">Pay with PayPal</button>
</form>

//==============================================================================
// Create Route in web.php
Route::post('/paypal', [\App\Http\Controllers\PayPalController::class,'index'])->name('paypal_call');
Route::get('/paypal/return', [\App\Http\Controllers\PayPalController::class,'paypalReturn'])->name('paypal_return');
Route::get('/paypal/cancel', [\App\Http\Controllers\PayPalController::class,'paypalCancel'])->name('paypal_cancel');

//==============================================================================
// Now have to create a Controller name "PayPalController" & use this code
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\PaymentExecution;
use Brian2694\Toastr\Facades\Toastr;

class PayPalController extends Controller
{
    //--------------------------------------------------
    public function index(Request $request)
    {
        $pay_now = $request->payable_amount;

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AWU7UzSVefmYmUyH89nn4WXUBUf7qLrAIYVg1m4Qtdo5fxC7LEZ1eac_njj9An4xM47gefCEPDbBP0ug',
                // ClientID from app
                'EOEWJPg1wXpr99HOc4wiyZoARmXoPUlGuKnrkGSpvqvMmmeqyREx3puJheiwe6SmcQ-bbMDR_CMBrNzI'
                // ClientSecret from app
            )
        );

        //my New added code
        $apiContext->setConfig(
            array(
                'log.LogEnabled' => true,
                'log.FileName' => 'PayPal.log',
                'log.LogLevel' => 'DEBUG',
                'mode' => 'live', //or
                // 'mode' => 'sandbox',
                // 'cache.enabled' => true,
              )
        );

        // Step 2
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new \PayPal\Api\Amount();
        $amount->setTotal($pay_now)->setCurrency('USD');
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

    //--------------------------------------------------
    public function paypalReturn()
    {
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AWU7UzSVefmYmUyH89nn4WXUBUf7qLrAIYVg1m4Qtdo5fxC7LEZ1eac_njj9An4xM47gefCEPDbBP0ug',
                // ClientID from app
                'EOEWJPg1wXpr99HOc4wiyZoARmXoPUlGuKnrkGSpvqvMmmeqyREx3puJheiwe6SmcQ-bbMDR_CMBrNzI'
                // ClientSecret from app
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
            Toastr::success('Payment Has been Successfull');
            return redirect()->route('index');
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }
    }
    //--------------------------------------------------
    public function paypalCancel()
    {
        return "order canceled";
    }
    //--------------------------------------------------
}

//==============================================================================
// Go .env & add this code
PAYPAL_CLIENT_ID=AYNtM6XHJu-bFJxzQJM1RvWVh0Tx7JRQz2YHbDhSNLCG8FN566SDUUVheLGFTQT8FOQQ643s2dZjcGVc
PAYPAL_SECRET=EC0H9PhZM-cARPeGRsAhvIdTeuilFRLV05j0Ptq6IaTP8HmCSonAtfAhRGfS34dmvqK18ROLBU528FhA
PAYPAL_MODE=live //or
// PAYPAL_MODE=sandbox



//==============================================================================
//=== END ===//
