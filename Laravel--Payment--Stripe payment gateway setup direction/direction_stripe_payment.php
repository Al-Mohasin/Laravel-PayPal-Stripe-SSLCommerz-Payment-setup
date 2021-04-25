<?php
//===========  Stripe Payment system  ============//

/*==============================================================================
At first need to Sign Up to "stripe.com" & go to Developer option.
select API key & take "Publishable key & Secret key"
set in ".env" file like this --->
*/
STRIPE_KEY=      //"Publishable key" here
STRIPE_SECRET=   //"Secret key" here

//==============================================================================
//GO-- Command this for Install stripe-php Package
composer require stripe/stripe-php

//==============================================================================
# view file (blade.php) have (put) root folder
stripe_checkout.blade.php
stripe_payment.blade.php

//==============================================================================
//GO-- web.php for Route
Route::get("/stripe_checkout", [App\Http\Controllers\StripePaymentController::class, "stripe_checkout"]);
Route::post('/stripe_payment', [App\Http\Controllers\StripePaymentController::class, 'stripe_payment']);
Route::post('/stripe', [App\Http\Controllers\StripePaymentController::class, 'stripePost'])->name('stripe.post');

//==============================================================================
//Controller Example...
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;
use Cart;
use Auth;
use App\Models\Order;
use App\Models\OrderProductDetails;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Country;
use App\Models\City;

class StripePaymentController extends Controller
{
    //-----------------------------
    public function stripe_checkout()
    {
        if (!Auth::user()) {
            return redirect("login_register");
        } else {
            if (Cart::total() == 0) {
                return back()->withUnsuccess("Please Product Add to your Cart for checkout !");
            } else {
                return view('stripe_checkout');
            };
        };
    }
    //-----------------------------
    public function stripe_payment(Request $request)
    {
        $request->validate([
            "customer_name"=>"required",
            "customer_phone"=>"required",
            "customer_email"=>"required",
            "country_name"=>"required",
            "city_name"=>"required",
            "customer_address"=>"required",
            "zip_code"=>"required",
        ]);
        $shipping_data = $request->all();
        return view('stripe_payment', compact("shipping_data"));
    }
    //------------------
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        if (Session::has('coupon')) {
            $grand_total = Session::get('coupon')['balance'];
        } else {
            $grand_total = Cart::total();
        };

        $charge = Stripe\Charge::create([
            "amount" => $grand_total * 100,
            // "amount" => $request->final_amoun * 100,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Test payment from dm.com."
        ]);

        $shipping_data = (array) json_decode($request->shipping_data, true);

        $country_name = Country::find($shipping_data["country_name"])->name;
        $city_name = City::find($shipping_data["city_name"])->name;

        $created_id = "stripe_".uniqId();
        if (Session::has("coupon")) {
            $coupon = Session::get("coupon")["price"];
        } else {
            $coupon = "00";
        };
        $order_details_id = Order::insert([
            "user_id"=>Auth::user()->id,
            "name"=>$shipping_data["customer_name"],
            "email"=>$shipping_data["customer_email"],
            "phone"=>$shipping_data["customer_phone"],
            "country"=>$country_name,
            "city"=>$city_name,
            "address"=>$shipping_data["customer_address"],
            "zip_code"=>$shipping_data["zip_code"],
            "transaction_id"=>$created_id,
            "currency"=>$charge->currency,
            "amount"=>$grand_total,
            "coupon"=>$coupon,
            "tax"=>Cart::tax(),
            "status"=>"Pending",
            "day"=>date("d"),
            "month"=>date("m"),
            "year"=>date("Y"),
            "created_at"=>Carbon::now(),
        ]);

        foreach (Cart::content() as $cart_product) {
            OrderProductDetails::insert([
                "order_transaction_id"=>$created_id,
                "product_id"=>$cart_product->id,
                "product_name"=>$cart_product->name,
                "color"=>$cart_product->options->color,
                "size"=>$cart_product->options->size,
                "single_price"=>$cart_product->price,
                "quantity"=>$cart_product->qty,
                "total_price"=>($cart_product->price) * ($cart_product->qty),
                "created_at"=>Carbon::now(),
            ]);
            $Product_quantity = Product::find($cart_product->id)->quantity;
            $new_quantity = $Product_quantity - $cart_product->qty;
            Product::find($cart_product->id)->update([
                "quantity"=>$new_quantity,
            ]);
        };

        Cart::destroy();
        if (Session::has('coupon')) {
            Session::forget('coupon');
        };
        return redirect("/")->with([
            'messege'=>'Successfully Payment',
            'alert-type'=>'success'
        ]);
    }

    //--------------
}

//==============================================================================
# Command * -->>> (do not forget it)
php artisan config:cache
php artisan config:clear

//==============================================================================

Now you can check with following card details:

Name: Test
Number: 4242 4242 4242 4242
CSV: 123
Expiration Month: 12
Expiration Year: 2024

//==============================================================================












//===END ===//
