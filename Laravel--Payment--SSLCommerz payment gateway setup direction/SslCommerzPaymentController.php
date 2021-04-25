<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Library\SslCommerz\SslCommerzNotification;
use Auth;
use Session;
use Cart;
use Carbon\Carbon;
use App\Models\Upazila;
use App\Models\Union;
use App\Models\Postcode;
use App\Models\OrderProductDetails;
use App\Models\Product;

class SslCommerzPaymentController extends Controller
{
    //==========================================================================
    public function exampleEasyCheckout()
    {
        if (!Auth::user()) {
            return redirect("login_register");
        } else {
            if (Cart::total() == 0) {
                return back()->withUnsuccess("Please Product Add to your Cart for checkout !");
            } else {
                return view('exampleEasycheckout');
            };
        };
    }
    //==========================================================================
    public function exampleHostedCheckout()
    {
        if (!Auth::user()) {
            return redirect("login_register");
        } else {
            if (Cart::total() == 0) {
                return back()->withUnsuccess("Please Product Add to your Cart for checkout !");
            } else {
                return view('exampleHosted');
            };
        };
    }
    //==========================================================================
    public function index(Request $request)
    {
        $request->validate([
            "customer_name"=>"required",
            "customer_mobile"=>"required",
            "customer_email"=>"required",
            "district"=>"required",
            "upazila"=>"required",
            "union"=>"required",
            "postoffice"=>"required",
            "postcode"=>"required",
            "address"=>"required",
        ]);

        if (Session::has("coupon")) {
            $coupon = Session::get("coupon")["price"];
        } else {
            $coupon = "00";
        };
        $district_explode = explode("/", $request->district);
        $district = array_shift($district_explode);
        $upazila_explode = explode("/", $request->upazila);
        $upazila = array_shift($upazila_explode);

        $post_data = array();
        $post_data['total_amount'] = $request->amount;
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = "ssl_".uniqid();

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $request->customer_name;
        $post_data['cus_email'] = $request->customer_email;
        $post_data['cus_add1'] = $request->address;
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = $district;
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = $request->postcode;
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $request->customer_mobile;
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";

        $update_product = DB::table('orders')
            ->where('transaction_id', $post_data['tran_id'])
            ->updateOrInsert([
                'name' => $post_data['cus_name'],
                'email' => $post_data['cus_email'],
                'phone' => $post_data['cus_phone'],
                'amount' => $post_data['total_amount'],
                'status' => 'Pending',
                'address' => $post_data['cus_add1'],
                'transaction_id' => $post_data['tran_id'],
                'currency' => $post_data['currency'],
                # my input
                "user_id"=>Auth::user()->id,
                "country"=>$post_data['cus_country'],
                "city"=>$post_data['cus_city'],
                "upazila"=>$upazila,
                "union_bd"=>$request->union,
                "postoffice"=>$request->postoffice,
                "zip_code"=>$post_data['cus_postcode'],
                "coupon"=>$coupon,
                "tax"=>Cart::tax(),
                "day"=>date("d"),
                "month"=>date("m"),
                "year"=>date("Y"),
                "created_at"=>Carbon::now()->toDateTimeString(),
            ]);

        foreach (Cart::content() as $cart_product) {
            OrderProductDetails::insert([
                    "order_transaction_id"=>$post_data['tran_id'],
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
        }

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }
    //==========================================================================
    public function payViaAjax(Request $request)
    {
        $request_data = (array) json_decode($request->cart_json);

        $district_explode = explode("/", $request_data["cus_district"]);
        $district = array_shift($district_explode);
        $upazila_explode = explode("/", $request_data["cus_upazila"]);
        $upazila = array_shift($upazila_explode);

        $post_data = array();
        $post_data['total_amount'] = $request_data["amount"]; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $request_data["cus_name"];
        $post_data['cus_email'] = $request_data["cus_email"];
        $post_data['cus_add1'] = $request_data["cus_address"];
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = $district;
        // $post_data['cus_city'] = $request_data["cus_district"];
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $request_data["cus_phone"];
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";

        if (Session::has("coupon")) {
            $coupon = Session::get("coupon")["price"];
        } else {
            $coupon = "00";
        };

        #Before  going to initiate the payment order status need to update as Pending.
        $update_product = DB::table('orders')
            ->where('transaction_id', $post_data['tran_id'])
            ->updateOrInsert([
                'name' => $post_data['cus_name'],
                'email' => $post_data['cus_email'],
                'phone' => $post_data['cus_phone'],
                'amount' => $post_data['total_amount'],
                'status' => 'Pending',
                'address' => $post_data['cus_add1'],
                'transaction_id' => $post_data['tran_id'],
                'currency' => $post_data['currency'],
                # my input
                "user_id"=>Auth::user()->id,
                "country"=>"Bangladesh",
                "city"=>$post_data['cus_city'],
                "upazila"=>$upazila,
                "union_bd"=>$request_data["cus_union"],
                "postoffice"=>$request_data["cus_postoffice"],
                "zip_code"=>$request_data["cus_postcode"],
                "coupon"=>$coupon,
                "tax"=>Cart::tax(),
                "day"=>date("d"),
                "month"=>date("m"),
                "year"=>date("Y"),
                "created_at"=>Carbon::now(),
            ]);

        foreach (Cart::content() as $cart_product) {
            OrderProductDetails::insert([
                        "order_transaction_id"=>$post_data['tran_id'],
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


        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }
    //==========================================================================
    public function success(Request $request)
    {
        echo "Transaction is Successful";

        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $validation = $sslc->orderValidate($tran_id, $amount, $currency, $request->all());

            if ($validation == true) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Complete.
                Here you can also sent sms or email for successfull transaction to customer
                */
                $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Processing']);

                echo "<br >Transaction is successfully Completed";
            } else {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel and Transation validation failed.
                Here you need to update order status as Failed in order table.
                */
                $update_product = DB::table('orders')
                    ->where('transaction_id', $tran_id)
                    ->update(['status' => 'Failed']);
                echo "validation Fail";
            }
        } elseif ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
             */
            echo "Transaction is successfully Completed";
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            echo "Invalid Transaction";
        }

        return redirect("/")->with([
            "message"=>"Your Payment Success",
            "alert-type"=>"success",
        ]);
    }
    //==========================================================================
    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Failed']);
            echo "Transaction is Falied";
        } elseif ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }
    }
    //==========================================================================
    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('transaction_id', $tran_id)
            ->select('transaction_id', 'status', 'currency', 'amount')->first();

        if ($order_detials->status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->update(['status' => 'Canceled']);
            echo "Transaction is Cancel";
        } elseif ($order_detials->status == 'Processing' || $order_detials->status == 'Complete') {
            echo "Transaction is already Successful";
        } else {
            echo "Transaction is Invalid";
        }
    }
    //==========================================================================
    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) { #Check transation id is posted or not.
            $tran_id = $request->input('tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('transaction_id', $tran_id)
                ->select('transaction_id', 'status', 'currency', 'amount')->first();

            if ($order_details->status == 'Pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($tran_id, $order_details->amount, $order_details->currency, $request->all());
                if ($validation == true) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Processing']);

                    echo "Transaction is successfully Completed";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('transaction_id', $tran_id)
                        ->update(['status' => 'Failed']);

                    echo "validation Fail";
                }
            } elseif ($order_details->status == 'Processing' || $order_details->status == 'Complete') {

                #That means Order status already updated. No need to udate database.

                echo "Transaction is already successfully Completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.

                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }
    //==========================================================================
    //==========================================================================
    public function get_upazila_list(Request $request)
    {
        $explode = explode("/", $request->district_value);
        $district_id = end($explode);

        $dropdown = "<option value=''>Select Upazila ... </option>";
        $upazilas = Upazila::where("district_id", $district_id)->get();
        foreach ($upazilas as $upazila) {
            $dropdown .= "<option value='".$upazila->name."/".$upazila->id."'>".$upazila->name."</option>";
        }
        return response()->json($dropdown);
    }
    //==========================================================================
    public function get_postcode_list(Request $request)
    {
        $explode = explode("/", $request->district_value);
        $district_name = array_shift($explode);

        $dropdown2 = "<option value=''>Select Postcode ... </option>";
        $postcodes = Postcode::where("en_district", $district_name)->get();
        foreach ($postcodes as $postcode) {
            $dropdown2 .= "<option value='".$postcode->postcode_key."'>".$postcode->postcode_key." - ".$postcode->en_suboffice."</option>";
        }
        return response()->json($dropdown2);
    }
    //==========================================================================
    public function get_union_list(Request $request)
    {
        $explode = explode("/", $request->upazila_value);
        $upazila_id = end($explode);

        $dropdown3 = "<option value=''>Select Union ... </option>";
        $unions = Union::where("upazilla_id", $upazila_id)->get();
        foreach ($unions as $union) {
            $dropdown3 .= "<option value='".$union->name."'>".$union->name."</option>";
        }
        return response()->json($dropdown3);
    }
    //==========================================================================
    public function sslmessage()
    {
        echo "Yes ! You Are successfully Payment !!!!!!!!!!!!!!";
    }



    //==========================================================================
}
