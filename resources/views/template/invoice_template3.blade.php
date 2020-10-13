<!DOCTYPE html>
<html>
   <head>
      <title>Repair Reciept</title>
      <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
      <link href="{{ asset('css/templates/invoice3.css') }}" rel="stylesheet">
   </head>
   <body>
      <div id="invoice-POS">
         <center id="top">
            <div class="logo">
               <img src="{{ asset(config('config.main_logo')) }}">
            </div>
            <div class="info">
               <h2>{{config('config.company_name')}}</h2>
               <p> 
                  {{__('configuration.address_line_1')}} : {{config('config.address_line_1')}}</br>
                  {{__('configuration.email')}}   : {{config('config.email')}}</br>
                  {{__('configuration.phone')}}   : {{config('config.phone')}}</br>
               </p>
            </div>
         </center>
         <div class="clearfix"></div>
         <div id="mid">
            <div class="info">
               <h2></h2>
               <center>
                     <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($repair->code, "C128") }}" alt="barcode"   />
               </center>
               <h2>{{__('customer.name')}}: {{$repair->customer}}</h2>
               <div class="clearfix"></div>
            </div>
         </div>
         <div id="bot">
            <div id="table">
               <table>
                  <tr class="tabletitle">
                     <td class="item">
                        <h2>{{__('repair_item.name')}}</h2>
                     </td>
                     <td class="Hours"></td>
                     <td class="Rate price"></h2></td>
                  </tr>
                  <tr class="service">
                     <td colspan="3" class="tableitem">
                        <p class="itemtext">
                           <strong>{{ $repair->model}}</strong>
                           <small>{{ $repair->serial_number ? '('.$repair->serial_number.')' : ''}}
                           <br>
                           {{$repair->defect}}</small>
                        </p>
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td></td>
                     <td class="Rate">
                        <h2>{{__('tax.name')}}</h2>
                     </td>
                     <td class="payment">
                        <h2>{{ formatMoney($repair->tax, 2) }}</h2>
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td></td>
                     <td class="Rate">
                        <h2>{{__('repair.grand_total')}}</h2>
                     </td>
                     <td class="payment">
                        <h2>{{ formatMoney($repair->grand_total, 2) }}</h2>
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td></td>
                     <td class="Rate">
                        <h2>{{__('repair.paid')}}</h2>
                     </td>
                     <td class="payment">
                        <h2>{{ formatMoney($repair->paid, 2) }}</h2>
                     </td>
                  </tr>
                  <tr class="tabletitle">
                     <td></td>
                     <td class="Rate">
                        <h2>{{__('repair.balance')}}</h2>
                     </td>
                     <td class="payment">
                        <h2>{{ formatMoney($repair->grand_total - $repair->paid, 2) }}</h2>
                     </td>
                  </tr>
               </table>
               <small class="text-right">
               @if($repair->payments)
               @foreach ($repair->payments as $payment)
               {{ __('payment.paid_line', 
	               [
	               'type' => __($payment->paid_by),
	               'amount' => $payment->amount,
	               'date' => $payment->date
	               ]
               ) }}<br>
               @endforeach
               @endif
               </small>
            </div>
            <div id="legalcopy">
               <p class="legal">{{ ($disclaimer) }}</p>
            </div>
            <center>
               <img src="{{ asset('uploads/temp/qrcode.png') }}" height="60" alt="qrcode"/>
            </center>
            <div class="clearfix"></div>
         </div>
      </div>
      
   </body>
   <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
</html>