<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="SSLCommerz">
    <title>Example - Hosted Checkout | SSLCommerz</title>

    <link rel="stylesheet" href="{{ asset('backend_assets') }}/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container">
    <div class="py-5 text-center">
        <h2>Hosted Payment - SSLCommerz</h2>
        <h5>You have to pay : tk
            @if (Session::has("coupon"))
            <strong>{{ Session::get("coupon")["balance"] }}</strong>
            @else
            <strong>{{ Cart::total() }}</strong>
            @endif
        </h5>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 order-md-1">
            <h4 class="mb-3">Shipping address</h4>
            <!-- Form -->
            <form action="{{ url('/pay') }}" method="POST" class="needs-validation">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="row">
                    <!-- Name ===============================================-->
                    <div class="col-md-12 mb-3">
                        <label>Full name *</label>
                        <input type="text" name="customer_name" class="form-control" id="customer_name" placeholder="Enter Name" value="{{  Auth::user()->name }}" equired>
                        @if ($errors->has("customer_name"))
                            <small class="text-danger form-text">{{ $errors->first("customer_name") }}</small>
                        @endif
                    </div>
                    <!-- Mobile =============================================-->
                    <div class="col-md-12 mb-3">
                        <label>Mobile *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">+88</span></div>
                            <input type="text" name="customer_mobile" class="form-control" placeholder="Enter Mobile" value="{{  Auth::user()->phone }}" equired>
                        </div>
                        @if ($errors->has("customer_mobile"))
                            <small class="text-danger form-text">{{ $errors->first("customer_mobile") }}</small>
                        @endif
                    </div>
                    <!-- Email ==============================================-->
                    <div class="col-md-12 mb-3">
                        <label>Email *</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="Enter Email" value="{{ Auth::user()->email }}">
                        @if ($errors->has("customer_email"))
                            <small class="text-danger form-text">{{ $errors->first("customer_email") }}</small>
                        @endif
                    </div>
                    <!-- Country ============================================-->
                    <div class="col-md-6 mb-3">
                        <label>Country</label>
                        <select class="custom-select d-block w-100">
                            <option value="Bangladesh">Bangladesh</option>
                        </select>
                    </div>
                    <!-- District ===========================================-->
                    <div class="col-md-6 mb-3">
                        <label>District *</label>
                        <select name="district" id="district_dropdown" class="custom-select d-block w-100" equired>
                            <option value="">Select District ...</option>
                            @php
                                $districts = App\Models\District::all();
                            @endphp
                            @foreach ($districts as $district)
                                <option value="{{ $district->name }}/{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has("district"))
                            <small class="text-danger form-text">{{ $errors->first("district") }}</small>
                        @endif
                    </div>
                    <!-- Upazila ============================================-->
                    <div class="col-md-6 mb-3">
                        <label>Upazila *</label>
                        <select name="upazila" id="upazila_dropdown" class="custom-select d-block w-100" equired>

                        </select>
                        @if ($errors->has("upazila"))
                            <small class="text-danger form-text">{{ $errors->first("upazila") }}</small>
                        @endif
                    </div>
                    <!-- Union ==============================================-->
                    <div class="col-md-6 mb-3">
                        <label>Union *</label>
                        <select name="union" id="union_dropdown" class="custom-select d-block w-100" equired>

                        </select>
                        @if ($errors->has("union"))
                            <small class="text-danger form-text">{{ $errors->first("union") }}</small>
                        @endif
                    </div>
                    <!-- Post Office ========================================-->
                    <div class="col-md-6 mb-3">
                        <label>Post Office *</label>
                        <input type="text" name="postoffice" class="form-control" placeholder="Enter Post Office" value="{{ old('postoffice') }}">
                        @if ($errors->has("postoffice"))
                            <small class="text-danger form-text">{{ $errors->first("postoffice") }}</small>
                        @endif
                    </div>
                    <!-- Post Code ==========================================-->
                    <div class="col-md-6 mb-3">
                        <label>Post Code / Zip *</label>
                        <select name="postcode" id="postcode_dropdown" class="custom-select d-block w-100" equired>

                        </select>
                        @if ($errors->has("postcode"))
                            <small class="text-danger form-text">{{ $errors->first("postcode") }}</small>
                        @endif
                    </div>
                    <!-- Address ============================================-->
                    <div class="col-md-12 mb-3">
                        <label>Address / Village / Road / House / etc ... *</label>
                        <input type="text" name="address" class="form-control" placeholder="Enter Other Address details" value="{{ old('address') }}">
                        @if ($errors->has("address"))
                            <small class="text-danger form-text">{{ $errors->first("address") }}</small>
                        @endif
                    </div>
                </div>

                <hr class="mb-4">

                <input type="hidden" name="amount" value="@if (Session::has("coupon")){{ Session::get("coupon")["balance"] }}@else{{ Cart::total() }}@endif">

                <button class="btn btn-primary btn-lg btn-block" type="submit">Continue to checkout-( tk.  @if (Session::has("coupon")){{ Session::get("coupon")["balance"] }}@else{{ Cart::total() }}@endif )</button>
            </form>

            <a target="_blank" href="#" title="SSLCommerz" alt="SSLCommerz"><img style="width:100%;height:auto;" src="https://securepay.sslcommerz.com/public/image/SSLCommerz-Pay-With-logo-All-Size-04.png" /></a>

        </div>
    </div>

    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">&copy; 2020 Company Name</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
</div>

<script src="{{ asset('backend_assets') }}/js/core/jquery.3.2.1.min.js"></script>
<script src="{{ asset('backend_assets') }}/js/core/popper.min.js"></script>
<script src="{{ asset('backend_assets') }}/js/core/bootstrap.min.js"></script>
<script>
    (function(window, document) {
        var loader = function() {
            var script = document.createElement("script"),
                tag = document.getElementsByTagName("script")[0];
            script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
            tag.parentNode.insertBefore(script, tag);
        };

        window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
    })(window, document);
</script>

<script type="text/javascript">
    $(document).ready(function() {
        //======================================================================
        //get Upazila
        $("#district_dropdown").on("change", function() {
            var district_value = $(this).val();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                },
            });
            $.ajax({
                type: "POST",
                url: "/get_upazila_list",
                data: {
                    district_value: district_value,
                },
                success: function(data) {
                    $("#upazila_dropdown").html(data);
                },
            });
        });
        //======================================================================
        //get postcode
        $("#district_dropdown").on("change", function() {
            var district_value = $(this).val();
            // alert(upazila);
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                },
            });
            $.ajax({
                type: "POST",
                url: "/get_postcode_list",
                data: {
                    district_value: district_value,
                },
                success: function(data) {
                    $("#postcode_dropdown").html(data);
                },
            });
        });
        //======================================================================
        //get Union
        $("#upazila_dropdown").on("change", function() {
            var upazila_value = $(this).val();
            // alert(upazila);
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                },
            });
            $.ajax({
                type: "POST",
                url: "/get_union_list",
                data: {
                    upazila_value: upazila_value,
                },
                success: function(data) {
                    $("#union_dropdown").html(data);
                },
            });
        });

        //======================================================================
    });
</script>
</body>

</html>
