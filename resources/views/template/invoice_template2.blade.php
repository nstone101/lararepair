<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
      <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <title>{{__('invoice')}}</title>
      <link href="{{ asset('css/templates/invoice2.css') }}" rel="stylesheet">
      @if(($pdf))
         <link href="{{ asset('css/templates/invoice2_pdf.css') }}" rel="stylesheet">
      @endif
   </head>
   <body>
      <center>
         <div class="x_content" contentEditable="true">
          @if($is_a4)
         <div id="copy" class="row a4-full">
         @else
         <div id="copy" class="row a4-half">
            @endif
            <div class="col-md-12 col-sm-12 col-xs-12 no-padding-left margin-0">
               <div class="col-xs-5 no-padding text-left no-padding-left margin-0">
                  <div class="text-muted well well-sm no-shadow head_left">
                     <h4 class="text-head1 margin-0 text-white" >{{__('repair.invoice')}} {{($repair->code) }}</h4>
                     <h6 class="text-head1 margin-0 text-white" >{{__('repair.invoice_subheading')}}</h6>
                  </div>
                  <h5 class="margin-0 color">{{__('repair.created_at')}}: {{ date('d/m/Y',strtotime($repair->created_at))}}</h5>
                  <h5 class="margin-0 color">{{__('repair.code')}}: {{($repair->id) }}</h5>
                  <h5 class="margin-0 color">{{__('customer.name')}}:  {{ $repair->customerData->name}}</h5>
                  <h5 class="margin-0 color">{{__('customer.phone')}}: {{ $repair->customerData->phone}} </h5>
               </div>
               <div class="col-xs-4 bg-col report-boxes">
                  <h5 class="color">{{__('repair.bc_management')}}</h5>
                  <h5 class="color">
                     <div>
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($repair->code, "C128") }}" alt="barcode"   />
                     </div>
                  </h5>
                  <h5  class="color">
                     <div class="pull-left">
                        <img src="{{ asset('uploads/temp/qrcode.png') }}" height="60" alt="qrcode"/>
                     </div>
                  </h5>
                  <h4 class="color">{{__('repair.check_online')}}</h4>
                  <h5 class="color">
                     {{\URL::to('/')}}
                  </h5>
               </div>
               <div class="col-xs-3" >
                  <h4 class="color" >
                     <img src="{{ asset(config('config.main_logo')) }}" class="img">
                  </h4>
                  <h4 class="color text-right margin-0">{{config('config.company_name')}}</h4>
                  <h5 class="color text-right margin-0">{{config('config.address')}}</h5>
                  <h5 class="color text-right margin-0">{{__('customer.phone')}}: {{config('config.phone')}}</h5>
               </div>
            </div>
            <table class="table table-bordered">
               <thead>
                  <tr>
                     <th class="col-md-1">
                        #
                     </th>
                     <th class="col-md-2">{{__('repair_item.name')}}</th>
                     <th class="col-md-2">{{__('repair.defect')}}</th>
                     <th class="col-md-3">{{__('repair.comments')}}</th>
                     <th class="col-md-2">{{__('repair.grand_total')}}</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <th class="col-md-1">1</td>
                     <th class="col-md-2">{{ $repair->model_name}} <small>{{ $repair->serial_number ? '('.$repair->serial_number.')' : ''}}</small></td>
                     <th class="col-md-2">{{ $repair->defect}}</td>
                     <th class="col-md-3">{{ $repair->comment}}</td>
                     <th class="col-md-2">{{ formatMoney($repair->grand_total, 2)}}</td>
                  </tr>
               </tbody>
               <tfoot>
                  <tr>
                     <th colspan="4">{{__('tax.name')}}</td>
                     <th class="col-md-2">{{ formatMoney($repair->tax, 2)}}</td>
                  </tr>
                  <tr>
                     <th colspan="4">{{__('repair.grand_total')}}</td>
                     <th class="col-md-2">{{ formatMoney($repair->grand_total, 2)}}</td>
                  </tr>
                  <tr>
                     <th colspan="4">{{__('repair.paid')}}</td>
                     <th class="col-md-2">{{ formatMoney($repair->paid, 2)}}</td>
                  </tr>
                  <tr>
                     <th colspan="4">{{__('repair.balance')}}</td>
                     <th class="col-md-2">{{ formatMoney($repair->grand_total - $repair->paid, 2)}}</td>
                  </tr>
                  <tr class="text-right">
                     <th colspan="5" class="text-right">
                        @if($repair->payments)
                           @foreach ($repair->payments as $payment)
                           {{ __('payment.paid_line', 
                              [
                              'type' => __($payment->paid_by),
                              'amount' => $payment->amount,
                              'date' => $payment->date
                              ]
                           )}}
                           <br>
                           @endforeach
                        @endif
                        </td>
                  </tr>
               </tfoot>
            </table>

            <div>
            {{ ($disclaimer) }}
                  
            </div>
            @if($is_a4)
               <div class="col-md-12 col-sm-12 col-xs-12" >
            @else
               <div class="col-md-12 col-sm-12 col-xs-12" >
            @endif
                  <div class="col-xs-6 no-padding text-left">
                     <h5 class="text-center color">{{__('Repairer')}}(.................................................)</h5>
                     <h5 class="text-center color">{{ config('config.company_name')}}</h5>
                  </div>
                  <div class="col-xs-6 no-padding text-left">
                     <h5 class="text-center color">{{__('sign_recipient')}} (.................................................)</h5>
                     <h5 class="text-center color">{{$repair->customer}}</h5>
                  </div>
               </div>
            </div>
          
         </div>
      </center>
      <div id="print_button">{{ __('general.print')}}</div>
   </body>
   <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
   <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
</html>