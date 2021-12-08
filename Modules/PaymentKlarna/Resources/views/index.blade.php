@extends('paymentklarna::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('paymentklarna.name') !!}
    </p> <div id="klarna-checkout-container" style="overflow: hidden;">
        <div id="klarna-checkout-container" style="overflow: hidden;">
            <div id="klarna-unsupported-page">
            <style type="text/css">
            @-webkit-keyframes klarnaFadeIn{from{opacity:0}to{opacity:1}}@-moz-keyframes klarnaFadeIn{from{opacity:0}to{opacity:1}}@keyframes klarnaFadeIn{from{opacity:0}to{opacity:1}}#klarna-unsupported-page{opacity:0;opacity:1\9;-webkit-animation:klarnaFadeIn ease-in 1;-moz-animation:klarnaFadeIn ease-in 1;animation:klarnaFadeIn ease-in 1;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;animation-fill-mode:forwards;-webkit-animation-duration:.1s;-moz-animation-duration:.1s;animation-duration:.1s;-webkit-animation-delay:5s;-moz-animation-delay:5s;animation-delay:5s;text-align:center;padding-top:64px}#klarna-unsupported-page .heading{font-family: "Klarna Headline", Helvetica, Arial, sans-serif;color: rgb(23, 23, 23);font-size: 36px;letter-spacing: -0.2px;-webkit-font-smoothing: antialiased;}#klarna-unsupported-page .subheading{font-family: "Klarna Text", "Klarna Sans", Helvetica, Arial, sans-serif;color: rgb(23, 23, 23);-webkit-font-smoothing: antialiased;line-height: 28px;font-weight: 400;font-size: 19px;max-width: 640px;margin: 20px auto;}#klarna-unsupported-page .reload {cursor: pointer;outline: none;-webkit-tap-highlight-color: rgba(255, 255, 255, 0);border-width: 1px;background-color: rgb(38, 37, 37);border-color: rgb(38, 37, 37);padding: 15px 24px;margin-top: 15px;color: rgb(255, 255, 255);font-family: "Klarna Text", "Klarna Sans", Helvetica, Arial, sans-serif;font-weight: 500;text-rendering: geometricprecision;font-size: 100%;}
            </style>
            <h1 class="heading">Something went wrong</h1>
            <p class="subheading">Sorry for any inconvenience, please try reloading the checkout page or try again later.</p>
            <p class="subheading">If the problem persists it maybe be because you are using an old version of the web browser which is not safe nor compatible with modern web sites. For a smoother checkout experience, please install a newer browser.</p>
            <button class="reload" onclick="reloadCheckoutHandler && reloadCheckoutHandler()">Reload checkout</button>
            </div>
            <script id="klarna-checkout-context" type="text/javascript">
            /* <![CDATA[ */
            var reloadCheckoutHandler;
            (function(w,k,i,d,n,c,l){
              w[k]=w[k]||function(){(w[k].q=w[k].q||[]).push(arguments)};
              l=w[k].config={
                container:w.document.getElementById(i),
                ORDER_URL:'https://js.playground.klarna.com/eu/kco/checkout/orders/3a34ab2b-2752-64c0-8d59-de93a5375562',
                AUTH_HEADER:'KlarnaCheckout go7oh2426vrov9pa3wmn',
                LOCALE:'en-SE',
                ORDER_STATUS:'checkout_incomplete',
                MERCHANT_NAME:'Your business name',
                GUI_OPTIONS:[],
                ALLOW_SEPARATE_SHIPPING_ADDRESS:false,
                PURCHASE_COUNTRY:'swe',
                PURCHASE_CURRENCY:'SEK',
                TESTDRIVE:true,
                BOOTSTRAP_SRC:'https://js.playground.klarna.com/kcoc/211206-e15eb05/checkout.bootstrap.js',
                FE_EVENTS_DISABLED:'false',
                CLIENT_EVENT_HOST:'https://eu.playground.klarnaevt.com'
              };
              n=d.createElement('script');
              c=d.getElementById(i);
              n.async=!0;
              n.src=l.BOOTSTRAP_SRC;
              c.appendChild(n);
              try{
                ((w.Image && (new w.Image))||(d.createElement && d.createElement('img'))||{}).src =
                  l.CLIENT_EVENT_HOST + '/v1/checkout/snippet/load' +
                  '?sid=' + l.ORDER_URL.split('/').slice(-1) +
                  '&order_status=' + w.encodeURIComponent(l.ORDER_STATUS) +
                  '&timestamp=' + (new Date).getTime();
              }catch(e){}
              reloadCheckoutHandler = function () {
                  try{
                      ((w.Image && (new w.Image))||(d.createElement && d.createElement('img'))||{}).src =
                      l.CLIENT_EVENT_HOST+'/v1/checkout/snippet/reload?sid='+l.ORDER_URL.split('/').slice(-1)+
                      '&order_status='+w.encodeURIComponent(l.ORDER_STATUS)+'&timestamp='+(new Date()).getTime();
                      window.location.reload();
                  }catch(e){}
              }
            })(this,'_klarnaCheckout','klarna-checkout-container',document);
            /* ]]> */
            </script>
            <noscript>
          Please <a href="http://enable-javascript.com">enable JavaScript</a>.
            </noscript>
          </div>
@endsection
