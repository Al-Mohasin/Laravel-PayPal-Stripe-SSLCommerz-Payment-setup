@extends("layouts.website_app")

@section("content")
<div class="container ">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 estimate-ship-tax bg-info" style="padding-top:20px; padding-bottom:20px">
            <div class="contact_form_container">
                <div class="contact_form_title text-center">
                    <h4> <strong>Billing and Shipping Address</strong> </h4>
                    <h5> <b>( You have to Pay: $ @if(Session::has('coupon')){{ Session::get('coupon')['balance'] }}@else{{ Cart::total() }}@endif )</b> </h5>
                </div>

                <form action="{{ url('stripe_payment') }}" method="POST">
                    @csrf
                    <div class="billing">
                        <!-- Name ===========================================-->
                        <div class="form-group">
                            <label>Shipping name *</label>
                            <input type="text" name="customer_name" class="form-control" id="customer_name" placeholder="Enter Name" value="{{  Auth::user()->name }}" equired>
                            @if ($errors->has("customer_name"))
                                <small class="text-danger form-text"> <b>{{ $errors->first("customer_name") }}</b> </small>
                            @endif
                        </div>
                        <!-- Mobile =========================================-->
                        <div class="form-group">
                            <label>Shipping Phone </label>
                            <input type="text" class="form-control " name="customer_phone" aria-describedby="emailHelp" placeholder="Phone " value="@auth{{ Auth::user()->phone }}@endauth">
                            @if($errors->has("customer_phone"))
                                <small class="text-danger"><b>{{ $errors->first("customer_phone") }}</b></small>
                            @endif
                        </div>
                        <!-- Email ==========================================-->
                        <div class="form-group">
                            <label>Email </label>
                            <input type="email" class="form-control " name="customer_email" aria-describedby="emailHelp" placeholder="Phone " value="@auth{{ Auth::user()->email }}@endauth">
                            @if($errors->has("customer_email"))
                                <small class="text-danger"><b>{{ $errors->first("customer_email") }}</b></small>
                            @endif
                        </div>
                        <!-- Country ========================================-->
                        <div class="form-group">
                            <label>Shipping Country</label>
                            @php
                                $countries = App\Models\Country::all();
                            @endphp
                                <select class="form-control" name="country_name" id="country_dropdown">
                                    <option value="">Select your Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            @if($errors->has("country_name"))
                                <small class="text-danger"><b>{{ $errors->first("country_name") }}</b></small>
                            @endif
                        </div>
                        <!-- City ===========================================-->
                        <div class="form-group">
                            <label>Shipping City / District</label>
                            <select class="form-control" name="city_name" id="city_dropdown">

                            </select>
                            @if($errors->has("city_name"))
                                <small class="text-danger"><b>{{ $errors->first("city_name") }}</b></small>
                            @endif
                        </div>
                        <!-- Address ========================================-->
                        <div class="form-group">
                            <label>Shipping Address</label>
                            <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Address" name="customer_address">
                            @if($errors->has("customer_address"))
                                <small class="text-danger"><b>{{ $errors->first("customer_address") }}</b></small>
                            @endif
                        </div>
                        <!-- Zip code =======================================-->
                        <div class="form-group">
                            <label>Zip Code</label>
                            <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Zip code" name="zip_code">
                            @if($errors->has("zip_code"))
                                <small class="text-danger"><b>{{ $errors->first("zip_code") }}</b></small>
                            @endif
                        </div>
                    </div>

                    {{-- <div class=" text-left"> <b>Payment By</b> </div>
                    <div class="form-group">
                        <ul class="logos_list">
                            <li><input type="radio" name="payment" value="stripe">
                                <img src="{{ asset('frontend_assets/assets/images/payments/1.png') }}" style="width: 60px; margin:3px;">
                            </li>
                            <li><input type="radio" name="payment" value="paypal">
                                <img src="{{ asset('frontend_assets/assets/images/payments/3.png') }}" style="width: 60px; margin:3px;">
                            </li>
                            <li><input type="radio" name="payment" value="ideal">
                                <img src="{{ asset('frontend_assets/assets/images/payments/4.png') }}" style="width: 60px; margin:3px;">
                            </li>
                        </ul>
                    </div> --}}
                    <br>
                    <div class="contact_form_button">
                        <button type="submit" class="btn btn-info">Pay Now</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section("footer_script")
<script>
    $(document).ready(function() {
        //======================================================================
        //country and city select
        $("#country_dropdown").on("change", function() {
            var country_id = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "/get_city_list",
                data: {
                    my_country_id: country_id
                },
                success: function(data) {
                    $("#city_dropdown").html(data);
                }
            });
        });

        //======================================================================
    });
</script>
@endsection
