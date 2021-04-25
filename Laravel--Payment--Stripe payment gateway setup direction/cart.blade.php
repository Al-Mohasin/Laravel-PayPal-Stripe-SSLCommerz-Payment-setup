@extends("layouts.website_app")

@section("content")
<style media="screen">
    .d-none {
        display: none;
    }
    .coupon_remove_button {
        background-color:orange;
        display:inline-block;
        text-align:center; width:15px;
        height:15px; color:#fff;
        border-radius:3px;
        line-height:12px
    }
</style>

<div class="breadcrumb">
    <div class="container">
        <div class="breadcrumb-inner">
            <ul class="list-inline list-unstyled">
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class='active'>Shopping Cart</li>
            </ul>
        </div>
    </div>
</div>

@if(session("success"))
    <div class="container">
        <div class="row">
            <div class="alert alert-success alert-dismissible session_success" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <strong>{{ session("success") }}</strong>
            </div>
        </div>
    </div>
@endif
@if(session("unsuccess"))
    <div class="container">
        <div class="row">
            <div class="alert alert-danger alert-dismissible session_unsuccess" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <strong>{{ session("unsuccess") }}</strong>
            </div>
        </div>
    </div>
@endif

{{-- <div class="body-content outer-top-xs"> --}}
    <div class="container">
        <div class="row">
            <div class="shopping-cart" style="background:#eee !important;">

                <!-- product table -->
                <div class="shopping-cart-table ">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="cart-romove item">Remove</th>
                                    <th class="cart-description item">Image</th>
                                    <th class="cart-product-name item">Product Name</th>
                                    <th class="cart-qty item">Quantity</th>
                                    <th class="cart-sub-total item">Unit Price</th>
                                    <th class="cart-total last-item">Grandtotal</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <div class="shopping-cart-btn">
                                            <span class="">
                                                <a href="{{ url('/') }}" class="btn btn-upper btn-primary outer-left-xs">Continue Shopping</a>
                                                {{-- <a href="#" class="btn btn-upper btn-primary pull-right outer-right-xs">Update shopping cart</a> --}}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                            <tbody class="cark_table_check">
                                @foreach ($cart_info as $item)
                                <tr>
                                    <!-- delete -->
                                    <td class="romove-item">
                                        <a href="{{ url('cart_product_remove') }}/{{ $item->rowId }}" title="cancel" class="icon"><i class="fa fa-trash-o"></i></a>
                                    </td>
                                    <!-- image -->
                                    <td class="cart-image">
                                        <a class="#" href="detail.html">
                                            <img src="{{ asset('storage/product') }}/{{ $item->options->image }}" alt="cart image" width="70px">
                                        </a>
                                    </td>
                                    <!-- product info -->
                                    <td class="cart-product-name-info">
                                        <h4 class='cart-product-description'>
                                            <a href="#">{{ $item->name }}</a>
                                        </h4>
                                        <div class="row">
                                            <div class="col-sm-4"><div class="rating rateit-small"></div></div>
                                        </div>
                                        <div class="cart-product-info">
                                            <span class="product-color">COLOR:<span>{{ $item->options->color }}</span></span><br>
                                            <span class="product-color">SIZE:<span>{{ $item->options->size }}</span></span>
                                        </div>
                                    </td>

                                    <td class="cart-product-quantity">
                                        <form action="{{ url('cart_product_update') }}" method="POST">
                                            @csrf
                                            <div class="quant-input">
                                                <input type="text" value="{{ $item->rowId }}" name="id">
                                                <input type="number" value="{{ $item->qty }}" name="qty" max="{{ App\Models\Product::find($item->id)->quantity }}" min="1">
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm" style="margin-bottom: 25px"><i class="fa fa-refresh"></i> Edit Submit</button>
                                        </form>
                                    </td>
                                    <!-- price -->
                                    <td class="cart-product-sub-total">
                                        <span class="cart-sub-total-price">tk {{ $item->price }}</span>
                                    </td>
                                    <!-- quantity -->
                                    <td class="cart-product-grand-total">
                                        <span class="cart-grand-total-price">tk {{ $item->price * $item->qty }}</span>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- / product table -->

                <div class="" style="border-bottom:2px solid #aaa;"></div>

                <!-- coupon input -->
                <div class="col-md-6 col-sm-12 estimate-ship-tax">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <span class="estimate-title">Discount Code</span>
                                    <p>Enter your coupon code if you have one..</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <!-- apply coupon -->
                                    <form action="{{ url('apply_coupon') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" name="apply_coupon" class="form-control unicase-form-control text-input" placeholder="You Coupon..">
                                        </div>
                                        <div class="clearfix pull-right">
                                            <button type="submit" class="btn-upper btn btn-primary">APPLY COUPON</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- / coupon input -->

                <!-- total count -->
                <div class="col-md-6 col-sm-12 cart-shopping-total" style="background:#eee !important">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <!-- sub toral -->
                                    <div class="cart-sub-total">
                                        @if (Session::has("coupon"))
                                            Subtotal<span class="inner-left-md">{{ Cart::subtotal() }}</span> <br>

                                            Coupon: {{ Session::get("coupon")["name"] }}
                                            <a href="{{ url('remove_coupon') }}" class="coupon_remove_button">x</a>
                                            <span class="inner-left-md">
                                                {{ Session::get("coupon")["price"] }}
                                            </span>
                                        @else
                                            Subtotal<span class="inner-left-md">{{ Cart::subtotal() }}</span>
                                        @endif
                                    </div>
                                    <!-- tax -->
                                    <div class="cart-sub-total">
                                        Tax<span class="inner-left-md">{{ Cart::tax() }}</span>
                                    </div>
                                    <!-- grand toral -->
                                    <div class="cart-grand-total">
                                        @if (Session::has("coupon"))
                                            Grand Total<span class="inner-left-md">{{ Session::get("coupon")["balance"] }}</span>
                                        @else
                                            Grand Total<span class="inner-left-md">{{ Cart::total() }}</span>
                                        @endif
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="cart-checkout-btn pull-right" style="text-align:right">
                                        <p>
                                            <a href="{{ url('stripe_checkout') }}" class="btn btn-primary checkout-btn"> <b>Stripe Checkout</b> </a>
                                        </p>
                                        <p>
                                            <a href="{{ url('example1') }}" class="btn btn-primary checkout-btn"> <b>SSLCommerz Ajax Checkout</b> </a>
                                        </p>
                                        <p>
                                            <a href="{{ url('example2') }}" class="btn btn-primary checkout-btn"> <b>SSLCommerz Hosted Checkout</b> </a>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- / total count -->

            </div><!-- /.shopping-cart -->
        </div> <!-- /.row -->
    </div><!-- /.container -->
{{-- </div> --}}
@endsection

@section("footer_script")
<script>
    $(document).ready(function() {
        //======================================================================
        //shipping
        // $("#shipping_check").on('change', function() {
        //     var check = $(this).prop('checked');
        //     if (check == true) {
        //         $('.shipping').removeClass('d-none');
        //         $('.billing').addClass('d-none');
        //     }
        //     if (check == false) {
        //         $('.shipping').addClass('d-none');
        //         $('.billing').removeClass('d-none');
        //     }
        // });
        //======================================================================
        //country and city select
        // $("#country_dropdown").on("change", function() {
        //
        //     var country_id = $(this).val();
        //
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         type: "POST",
        //         url: "/get_city_list",
        //         data: {
        //             my_country_id: country_id
        //         },
        //         success: function(data) {
        //             $("#city_dropdown").html(data);
        //         }
        //     });
        // });


        //======================================================================
    });
</script>
@endsection
