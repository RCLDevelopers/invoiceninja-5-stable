@extends('portal.ninja2020.layout.payments', ['gateway_title' => ctrans('texts.paypal'), 'card_title' => ''])

@section('gateway_head')

@endsection

@section('gateway_content')
    <form action="{{ route('client.payments.response') }}" method="post" id="server_response">
        @csrf
        <input type="hidden" name="payment_hash" value="{{ $payment_hash }}">
        <input type="hidden" name="company_gateway_id" value="{{ $gateway->company_gateway->id }}">
        <input type="hidden" name="gateway_response" id="gateway_response">
        <input type="hidden" name="gateway_type_id" id="gateway_type_id" value="{{ $gateway_type_id }}">
        <input type="hidden" name="amount_with_fee" id="amount_with_fee" value="{{ $total['amount_with_fee'] }}"/>
    </form>

    <div class="alert alert-failure mb-4" hidden id="errors"></div>

    <div id="paypal-button-container" class="paypal-button-container">
    </div>

    <div id="is_working" class="flex mt-4 place-items-center hidden">
       <span class="loader m-auto"></span>
    </div>
   
@endsection

@section('gateway_footer')
@endsection

@push('footer')

<script type="application/json" fncls="fnparams-dede7cc5-15fd-4c75-a9f4-36c430ee3a99">
    {
        "f":"{{ $guid }}",
        "s":"paypal.pay"        // unique ID for each web page
    }
</script>

<script type="text/javascript" src="https://c.paypal.com/da/r/fb.js"></script>


<style type="text/css">
.loader {
width: 48px;
height: 48px;
border-radius: 50%;
position: relative;
animation: rotate 1s linear infinite
}
.loader::before , .loader::after {
content: "";
box-sizing: border-box;
position: absolute;
inset: 0px;
border-radius: 50%;
border: 5px solid #454545;
animation: prixClipFix 2s linear infinite ;
}
.loader::after{
border-color: #FF3D00;
animation: prixClipFix 2s linear infinite , rotate 0.5s linear infinite reverse;
inset: 6px;
}
@keyframes rotate {
0%   {transform: rotate(0deg)}
100%   {transform: rotate(360deg)}
}
@keyframes prixClipFix {
    0%   {clip-path:polygon(50% 50%,0 0,0 0,0 0,0 0,0 0)}
    25%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 0,100% 0,100% 0)}
    50%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,100% 100%,100% 100%)}
    75%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 100%)}
    100% {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 0)}
}
</style>
<script src="https://www.paypal.com/sdk/js?client-id={!! $client_id !!}&currency={!! $currency !!}&components=buttons,funding-eligibility&intent=capture&enable-funding={!! $funding_source !!}"  data-partner-attribution-id="invoiceninja_SP_PPCP"></script>

<script>
//&buyer-country=US&currency=USD&enable-funding=venmo
    const fundingSource = "{!! $funding_source !!}";
    const clientId = "{{ $client_id }}";
    const orderId = "{!! $order_id !!}";
    const environment = "{{ $gateway->company_gateway->getConfigField('testMode') ? 'sandbox' : 'production' }}";

    paypal.Buttons({
        env: environment,
        fundingSource: fundingSource,
        client: clientId,
        createOrder: function(data, actions) {
            return orderId;  
        },
        onApprove: function(data, actions) {

            console.log(data);
            
            document.getElementById("gateway_response").value =JSON.stringify( data );
            
            formData = JSON.stringify(Object.fromEntries(new FormData(document.getElementById("server_response")))),

            fetch('{{ route('client.payments.response') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json(); // or response.json() if the response is JSON
            })
            .then(data => {

                var errorDetail = Array.isArray(data.details) && data.details[0];

                if (errorDetail && ['INSTRUMENT_DECLINED', 'PAYER_ACTION_REQUIRED'].includes(errorDetail.issue)) {
                    return actions.restart();
                }

                if(data.redirect){
                    window.location.href = data.redirect;
                    return;
                }

                document.getElementById("gateway_response").value =JSON.stringify( data );
                document.getElementById("server_response").submit();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('errors').textContent = `Sorry, your transaction could not be processed...\n\n${error.message}`;
                document.getElementById('errors').hidden = false;
            });



        },
        onCancel: function() {
            window.location.href = "/client/invoices/";
        },
        onError: function(error) {

            console.log("on error");
            console.log(error);

            document.getElementById("gateway_response").value = error;
            document.getElementById("server_response").submit();
        },
        onClick: function (){

            if(fundingSource != 'card')
              document.getElementById('paypal-button-container').hidden = true;

            document.getElementById('is_working').classList.remove('hidden');

            document.querySelector('div[data-ref="required-fields-container').classList.add('hidden');
            
        },
        onInit: function (){
            console.log("init");
        }
    
    }).render('#paypal-button-container').catch(function(err) {
        
      document.getElementById('errors').textContent = err;
      document.getElementById('errors').hidden = false;
        
    });
    
    document.getElementById("server_response").addEventListener('submit', (e) => {
		if (document.getElementById("server_response").classList.contains('is-submitting')) {
			e.preventDefault();
		}
		
		document.getElementById("server_response").classList.add('is-submitting');
	});

</script>
@endpush