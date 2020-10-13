<?php
   $tax = $repair->tax;
   $price_without_tax = $repair->total; // PRICE WITHOUT TAX
   $total = $repair->grand_total; // PRICE WITH TAX
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>{{ __('repair.report')}}</title>
      <link href="{{ asset('css/invoice.css') }}" rel="stylesheet">
   </head>
   <body>
      <div id="editable_invoice">{{ __('repair.editable_invoice')}}</div>
      <div class="halfinvoice">
         <header class="clearfix">
            <div id="logo">
               <img src="{{ asset(config('config.main_logo')) }}">
            </div>
            <div id="company" contentEditable="true">
               <h2 class="name">{{ config('config.company_name') }}</h2>
               <div>{{ config('config.person_name') }}</div>
               <div>{{ config('config.address_line_1') }}</div>
               <div>{{ config('config.phone') }}</div>
               <div><a href="mailto:{{ config('config.email') }}">{{ config('config.email') }}</a></div>
            </div>
         </header>
         <main>
            <div id="details" class="clearfix">
               <div id="client" contentEditable="true">
                  <div class="to">{{ __('customer.title')}}:</div>
                  <h2 class="name">{{$repair->name }}</h2>
                  <div class="company">{{($repair->customerData ? $repair->customerData->company : '') }}</div>
                  <div class="address">{{($repair->customerData ? $repair->customerData->address : '') }}</div>
                  <div class="postal_code">{{($repair->customerData ? $repair->customerData->city : '') }} {{($repair->customerData ? $repair->customerData->postal_code : '') }}</div>
                  <div class="email"><a {{ $repair->customerData ? 'href="mailto:'.$repair->customerData->email.'"' : '' }} >{{$repair->customerData ? $repair->customerData->email : '' }}</a></div>
                  <div class="telephone">{{$repair->customerData ? $repair->customerData->telephone : '' }}</div>
               </div>
               <div id="invoice" contentEditable="true">
                  <h1>{{ __('repair.invoice_n')}} <i>{{str_pad($repair->id, 4, '0', STR_PAD_LEFT) }}</i></h1>
                  <div class="date">{{ __('repair.date_opening')}}: {{ date_format(date_create($repair->created_at),"Y/m/d") }}</div>
               </div>
            </div>
            <div id="dati">
               <div class="col"><b>{{ __('repair.serial_number')}}:</b> {{$repair->serial_number}}</div>
               <div class="col"><b>{{ __('repair.model')}}:</b> {{$repair->model_name}}</div>
               <div class="col"><b>{{ __('repair.category')}}:</b> {{$repair->category}}</div>
               <div class="col"><b>{{ __('repair.defect')}}:</b> {{$repair->defect}}</div>
               <div class="col"><b>{{ __('repair.grand_total')}}:</b>{{formatMoney($repair->grand_total)}}</div>
               <div class="col"><b>{{ __('repair.paid')}}:</b>{{formatMoney($repair->paid)}}</div>
               <div class="col"><b>{{ __('repair.balance')}}:</b>{{formatMoney($repair->grand_total - $repair->paid)}}</div>
               <div class="col"><b>{{ __('repair.code')}}:</b> {{$repair->code}}</div>
               <div class="col txt"><textarea id="comment" onkeyup="auto_grow(this)" contentEditable="true">{{$repair->comment }}</textarea></div>
               <div class="clearfix"></div>
            </div>
            <div>
                  {{ ($disclaimer) }}
                  
                 
            </div>
         </main>
         <div id="print_button">{{ __('print')}}</div>
      </div>
   </body>
  
      <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
</html>