<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="SSLCommerz">
    <title>Example - EasyCheckout (Popup) | SSLCommerz</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media(min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <div class="py-5 text-center">
            <h2>Ajax Easy Checkout - SSLCommerz</h2>
            <h5>You have to pay : tk
                @if (Session::has("coupon"))
                <strong>{{ Session::get("coupon")["balance"] }}</strong>
                @else
                <strong>{{ Cart::total() }}</strong>
                @endif
            </h5>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <h4 class="mb-3">Billing address</h4>
                <h6 class="text-info">You have to fill up all field Carefully.</h6>
                <form id="shipping_form" method="POST" class="needs-validation" ovalidate>
                    <div class="row">
                        <!-- Name ===========================================-->
                        <div class="col-md-12 mb-3">
                            <label>Full name *</label>
                            <input type="text" name="customer_name" class="form-control" id="customer_name" placeholder="Enter Name" value="{{  Auth::user()->name }}" required>
                        </div>

                        <!-- Mobile =========================================-->
                        <div class="col-md-12 mb-3">
                            <label>Mobile *</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">+88</span></div>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Enter Mobile" value="{{  Auth::user()->phone }}" equired>
                            </div>
                        </div>

                        <!-- Email ==============================================-->
                        <div class="col-md-12 mb-3">
                            <label>Email *</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" placeholder="Enter Email" value="{{ Auth::user()->email }}">
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
                        </div>
                        <!-- Upazila ============================================-->
                        <div class="col-md-6 mb-3">
                            <label>Upazila *</label>
                            <select name="upazila" id="upazila_dropdown" class="custom-select d-block w-100" equired>

                            </select>
                        </div>
                        <!-- Union ==============================================-->
                        <div class="col-md-6 mb-3">
                            <label>Union *</label>
                            <select name="union" id="union_dropdown" class="custom-select d-block w-100" equired>

                            </select>
                        </div>
                        <!-- Post Office ========================================-->
                        <div class="col-md-6 mb-3">
                            <label>Post Office *</label>
                            <input type="text" id="customer_postoffice" class="form-control" placeholder="Enter Post Office" value="{{ old('postoffice') }}">
                        </div>
                        <!-- Post Code ==========================================-->
                        <div class="col-md-6 mb-3">
                            <label>Post Code / Zip *</label>
                            <select name="postcode" id="postcode_dropdown" class="custom-select d-block w-100" equired>

                            </select>
                        </div>
                        <!-- Address ============================================-->
                        <div class="col-md-12 mb-3">
                            <label>Address / Village / Road / House / etc ... *</label>
                            <input type="text" id="customer_address" name="address" class="form-control" placeholder="Enter Other Address details" value="{{ old('address') }}">
                        </div>

                    </div>

                    <hr class="mb-4">

                    {{-- <input type="text" name="" value="45" id="total_amount"> --}}
                    <input type="hidden" id="total_amount" value="@if (Session::has("coupon")){{ Session::get("coupon")["balance"] }}@else{{ Cart::total() }}@endif">

                    <button class="btn btn-primary btn-lg btn-block" id="sslczPayBtn" token="if you have any token validation" postdata="" order="If you already have the transaction generated for current order" endpoint="{{ url('/pay-via-ajax') }}">
                        Pay Now
                    </button>
                </form>

                <a target="_blank" href="#" title="SSLCommerz" alt="SSLCommerz"><img style="width:100%;height:auto;" src="https://securepay.sslcommerz.com/public/image/SSLCommerz-Pay-With-logo-All-Size-04.png" /></a>

            </div>
        </div>

        <footer class="my-5 pt-5 text-muted text-center text-small">
            <p class="mb-1">&copy; 2019 Company Name</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#">Privacy</a></li>
                <li class="list-inline-item"><a href="#">Terms</a></li>
                <li class="list-inline-item"><a href="#">Support</a></li>
            </ul>
        </footer>
    </div>
    <!--=====================================================================-->
    {{-- <div class="col-md-12 mb-3">
        <label>Full name *</label>
        <input type="text" name="customer_name" class="form-control" id="customer_name" placeholder="Enter Name" value="{{  Auth::user()->name }}" required>
    <div class="error_mess" id="customer_name_error"></div>
    <button onclick="return validCheck()">Pay Now</button>
    </div>
    <script type="text/javascript">
        function validCheck() {
            var name = document.getElementById('customer_name');
            if (name.value == '') {
                document.getElementById('customer_name_error').innerHTML = 'Please enter your name!';
                name.focus();
                return false;
            } else {
                document.getElementById('customer_name_error').innerHTML = '';
            }
        }
    </script> --}}
    <!--=====================================================================-->

    <script src="{{ asset('backend_assets') }}/js/core/jquery.3.2.1.min.js"></script>
    <script src="{{ asset('backend_assets') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('backend_assets') }}/js/core/bootstrap.min.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> --}}

    <!-- If you want to use the popup integration, -->
    <script>
        var obj = {};
        obj.cus_name = $('#customer_name').val();
        obj.cus_phone = $('#customer_phone').val();
        obj.cus_email = $('#customer_email').val();
        obj.cus_district = $('#district_dropdown').val();
        obj.cus_upazila = $('#upazila_dropdown').val();
        obj.cus_union = $('#union_dropdown').val();
        obj.cus_postoffice = $('#customer_postoffice').val();
        obj.cus_postcode = $('#postcode_dropdown').val();
        obj.cus_address = $('#customer_address').val();
        obj.amount = $('#total_amount').val();

        $("#customer_name").change(function() {
            obj.cus_name = $('#customer_name').val();
        });
        $("#customer_phone").change(function() {
            obj.cus_phone = $('#customer_phone').val();
        });
        $("#customer_email").change(function() {
            obj.cus_email = $('#customer_email').val();
        });
        $("#district_dropdown").change(function() {
            obj.cus_district = $('#district_dropdown').val();
        });
        $("#upazila_dropdown").change(function() {
            obj.cus_upazila = $('#upazila_dropdown').val();
        });
        $("#union_dropdown").change(function() {
            obj.cus_union = $('#union_dropdown').val();
        });
        $("#customer_postoffice").change(function() {
            obj.cus_postoffice = $('#customer_postoffice').val();
        });
        $("#postcode_dropdown").change(function() {
            obj.cus_postcode = $('#postcode_dropdown').val();
        });
        $("#customer_address").change(function() {
            obj.cus_address = $('#customer_address').val();
        });


        $('#sslczPayBtn').prop('postdata', obj);

        (function(window, document) {
            var loader = function() {
                var script = document.createElement("script"),
                    tag = document.getElementsByTagName("script")[0];
                // script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR LIVE
                script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR SANDBOX
                tag.parentNode.insertBefore(script, tag);
            };

            window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
        })(window, document);
    </script>

    <!--=====================================================================-->
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

</html>
