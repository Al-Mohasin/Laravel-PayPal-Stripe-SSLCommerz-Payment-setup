@extends("layouts.website_app")

@section("content")
<style type="text/css">
    .panel-title {
        display: inline;
        font-weight: bold;
    }

    .display-table {
        display: table;
    }

    .display-tr {
        display: table-row;
    }

    .display-td {
        display: table-cell;
        vertical-align: middle;
        width: 61%;
    }
</style>

<div class="container" style="margin-top:50px; margin-bottom:30px">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default credit-card-box">

                <div class="panel-heading" style="display:block; background:#505050; color:#fff;">
                    <div class="row text-center" style="display:block;">
                        <h3 class="text-center" style="display:block;">Payment Details</h3>
                        <h4>You have to Pay :
                            <strong>$ @if(Session::has('coupon')){{ Session::get('coupon')['balance'] }}@else{{ Cart::total() }}@endif</strong>
                        </h4>
                    </div>
                </div>
                <div class="panel-body">

                    @if (Session::has('success'))
                    <div class="alert alert-success text-center">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <p>{{ Session::get('success') }}</p>
                    </div>
                    @endif

                    <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" id="payment-form">
                        @csrf
                        <div class='form-row row'>
                            <div class='col-xs-12 form-group required'>
                                <label class='control-label'>Name on Card</label>
                                <input name="payment_method" class='form-control' size='4' type='text' value="test">
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 form-group card required'>
                                <label class='control-label'>Card Number-(example:4242424242424242)</label>
                                <input autocomplete='off' class='form-control card-number' size='20' type='text' value="4242424242424242">
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 col-md-4 form-group cvc required'>
                                <label class='control-label'>CVC</label>
                                <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text' value="432">
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                <label class='control-label'>Expiration Month</label>
                                <input class='form-control card-expiry-month' placeholder='MM' size='2' type='text' value="06">
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration required'>
                                <label class='control-label'>Expiration Year</label>
                                <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text' value="2021">
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-md-12 error form-group hide'>
                                <div class='alert-danger alert'>Please correct the errors and try
                                    again.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <input type="hidden" name="shipping_data" value="{{ json_encode($shipping_data) }}">

                                <input type="hidden" name="final_amount" value="@if(Session::has('coupon')){{ Session::get('coupon')['balance'] }}@else{{ Cart::total() }}@endif">

                                <button class="btn btn-primary btn-lg btn-block" type="submit">
                                    <strong>Pay Now ( $ @if(Session::has('coupon')){{ Session::get('coupon')['balance'] }}@else{{ Cart::total() }}@endif )</strong>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

            </div> <!-- / credit-card-box -->
        </div>
    </div>
</div> <!-- / container -->
@endsection

@section("footer_script")
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
{{-- <script src="{{ asset('frontend_assets/assets') }}/js/stripe.min.js"></script> --}}

<script type="text/javascript">
    $(function() {
        var $form = $(".require-validation");
        $('form.require-validation').bind('submit', function(e) {
            var $form = $(".require-validation"),
                inputSelector = ['input[type=email]', 'input[type=password]',
                    'input[type=text]', 'input[type=file]',
                    'textarea'
                ].join(', '),
                $inputs = $form.find('.required').find(inputSelector),
                $errorMessage = $form.find('div.error'),
                valid = true;
            $errorMessage.addClass('hide');

            $('.has-error').removeClass('has-error');
            $inputs.each(function(i, el) {
                var $input = $(el);
                if ($input.val() === '') {
                    $input.parent().addClass('has-error');
                    $errorMessage.removeClass('hide');
                    e.preventDefault();
                }
            });

            if (!$form.data('cc-on-file')) {
                e.preventDefault();
                Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                Stripe.createToken({
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);
            }

        });

        function stripeResponseHandler(status, response) {
            if (response.error) {
                $('.error')
                    .removeClass('hide')
                    .find('.alert')
                    .text(response.error.message);
            } else {
                // token contains id, last4, and card type
                var token = response['id'];
                // insert the token into the form so it gets submitted to the server
                $form.find('input[type=text]').empty();
                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.get(0).submit();
            }
        }

    });
</script>
@endsection
