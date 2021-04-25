<?php
//===========     SSLCommerz Payment Gateway     ============//

#===============================================================================
# Add STORE_ID and STORE_PASSWORD values on your project's ".env" file.
# You can register for a store at -->
https:developer.sslcommerz.com/registration/

#===============================================================================
# Core Library Directory Structure
|-- config/
    |-- sslcommerz.php
 |-- app/Library/SslCommerz
    |-- AbstractSslCommerz.php (core file)
    |-- SslCommerzInterface.php (core file)
    |-- SslCommerzNotification.php (core file)
 |-- README.md
 |-- orders.sql (sample)

#===============================================================================
## Step 1:
# Download and extract the library files.
 https:github.com/sslcommerz/SSLCommerz-Laravel

#===============================================================================
## Step 2:
# Copy the "Library" folder and put it in the laravel project's "app/" directory.
# If needed, then run composer dump -o

#===============================================================================
## Step 3:
#Copy the "config/sslcommerz.php" file into your project's "config/" folder.

#===============================================================================
## Step 5:
# Copy the "SslCommerzPaymentController" into your project's Controllers folder.

#===============================================================================
## Step 6:
# Copy the defined routes from "routes/web.php" into your project's route file.
#Example...

use App\Http\Controllers\SslCommerzPaymentController;
# SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END

#===============================================================================
## Step 7:
# Add the below routes into the $excepts array of VerifyCsrfToken middleware.
protected $except = [
    '/pay-via-ajax', '/success','/cancel','/fail','/ipn'
];

#===============================================================================
## Step 8:
# Copy the "resources/views/*.blade.php" files into your project's "resources/views/" folder.

## Step 9:
# To integrate popup checkout, use the below script before the end of body tag.
#(this file may has integrated in that file- needd to check)

# For sandbox --->>>
<script>
    (function (window, document) {
        var loader = function () {
            var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
            script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
            tag.parentNode.insertBefore(script, tag);
        };

        window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
    })(window, document);
</script>

# For Live --->>>
<script>
    (function (window, document) {
        var loader = function () {
            var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
            script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
            tag.parentNode.insertBefore(script, tag);
        };

        window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
    })(window, document);
</script>

## Step 10:
# Use the below button where you want to show the "Pay Now" button:
#(this part may has integrated in that file- needd to check)
<button class="your-button-class" id="sslczPayBtn"
        token="if you have any token validation"
        postdata="your javascript arrays or objects which requires in backend"
        order="If you already have the transaction generated for current order"
        endpoint="/pay-via-ajax"> Pay Now
</button>

#===============================================================================

#===============================================================================



//===END ===//
