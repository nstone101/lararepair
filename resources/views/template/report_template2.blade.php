<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
      <title>{{__('repair.report')}}</title>
      <link href="{{ asset('css/templates/invoice2.css') }}" rel="stylesheet">
   </head>
   <body>
      <center>
         <div class="x_content">
         @if($is_a4)
         <div id="copy" class="row a4-full">
         @else
         <div id="copy" class="row a4-half">
            @endif
            <div class="col-md-12 col-sm-12 col-xs-12" >
               <div class="col-xs-5 no-padding text-left margin-10-top">
                  <div class="text-muted well well-sm no-shadow head_left">
                     <h4 class="text-head1 margin-0 text-white" >{{__('repair.invoice')}} {{($repair->code) }}</h4>
                     <h6 class="text-head1 margin-0 text-white" >{{__('repair.invoice_subheading')}}</h6>
                  </div>
                  <h5 class="color  margin-0">{{__('repair.code')}}: {{($repair->code) }}
                  </h5>
                  <h5 class="color  margin-0">{{__('customer.name')}}:  {{ $repair->customerData->name}}</h5>
                  <h5 class="color  margin-0">{{__('customer.phone')}}: {{ $repair->customerData->phone}} </h5>
               </div>
               <div class="col-xs-7 text-right" >
                  <h4 class="color" >
                     <img class="img" src="{{ asset(config('config.main_logo')) }}" >
                  </h4>
                  <h4 class="color text-right margin-0">{{config('config.company_name')}}</h4>
                  <h5 class="color text-right margin-0">{{config('config.address_line_1')}}</h5>
                  <h5 class="color text-right margin-0">{{__('customer.phone')}}: {{config('config.phone')}}</h5>
               </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12" >
               <div class="col-xs-4 bg-col report-boxes" >
                  <h5 class="color">{{__('repair.model')}}: {{$repair->model}}</h5>
                  <h5 class="color">{{__('status.label')}}:  {{$repair->status->label}}</h5>
                  <h5 class="color">{{__('repair.defect')}}:  {{$repair->defect}}</h5>
               </div>
               <div class="col-xs-4 bg-col report-boxes" >
                  <h5 class="color">{{__('repair.category')}}: {{$repair->category}}</h5>
                  <h5 class="color">{{__('repair.grand_total')}}:{{formatMoney($repair->grand_total, 2)}}</h5>
                  <h5 class="color">{{__('repair.paid')}}:  {{formatMoney($repair->paid, 2)}}</h5>
                  <h5 class="color">{{__('repair.balance')}}:  {{formatMoney( $repair->grand_total - $repair->paid, 2)}}</h5>
                  <h5 class="color">
                     {{__('repair.created_at')}} :  
                     {{date('m/d/Y', strtotime($repair->created_at))}}        
                  </h5>
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
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12" >
               <div class="col-xs-12 text-left" >
                  {{ ($disclaimer) }}
               </div>
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