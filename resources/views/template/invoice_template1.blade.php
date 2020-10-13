<?php
   $tax = $repair->tax;
   $price_without_tax = $repair->total - $repair->service_charges; // PRICE WITHOUT TAX
   $total = $repair->grand_total; // PRICE WITH TAX
   $paid = $repair->paid; // PRICE WITH TAX
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>{{ __('repair.invoice')}}</title>
      <link href="{{ asset('css/invoice.css')}}" rel="stylesheet">
      <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
      <link href="{{ asset('css/templates/invoice1.css') }}" rel="stylesheet">
   </head>
   <body>
      <div id="editable_invoice">{{ __('repair.editable_invoice')}}</div>
      <header class="clearfix">
         <div id="logo">
            <img src="{{ asset(config('config.main_logo')) }}">
         </div>
         <div id="company" contentEditable="true">
            <h2 class="name">{{ config('config.company_name') }}</h2>
            <div>{{ config('config.person_name') }}</div>
            <div>{{ config('config.address') }}</div>
            <div>{{ config('config.phone') }}</div>
            <div><a href="mailto:{{ config('config.email') }}">{{ config('config.email') }}</a></div>
         </div>
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
         <h3>{{ __('repair.reparation_title').': '.$repair->defect.' '.$repair->model_name }}</h3>
         <pre>
        </pre>
         <table border="0" cellspacing="0" cellpadding="0">
            <thead>
               <tr>
                  <th class="no">#</th>
                  <th class="desc">{{ __('repair_item.name')}}</th>
                  <th class="unit">{{ __('repair_item.price')}}</th>
                  <th class="qty">{{ __('repair_item.qty')}}</th>
                  <th class="total">{{ __('repair_item.subtotal')}}</th>
               </tr>
            </thead>
            <tbody contentEditable="true">
               @if($repair->items && sizeof($repair->items) > 0) 
               <?php $a = 1 ;?>
               @foreach ($repair->items as $item)
               <tr>
                  <td class="no">{{str_pad($a, 2, '0', STR_PAD_LEFT) }}</td>
                  <td class="desc">
                     <h3>
                     {{ $item->product_name }}
                  </td>
                  <td class="unit">{{ formatMoney($item->unit_price, 2)}}</td>
                  <td class="qty">{{ number_format($item->quantity, 1)}}</td>
                  <td class="total">{{ formatMoney($item->subtotal, 2)}}</td>
               </tr>
               <?php $a++ ;?>
               @endforeach
               @else
               <tr>
                  <td class="no">{{str_pad(1, 2, '0', STR_PAD_LEFT) }}</td>
                  <td class="desc">
                     <h3>
                     {{__('repair.no_items_used')}}
                  </td>
                  <td class="unit">{{formatMoney(0.00)}}</td>
                  <td class="qty">0</td>
                  <td class="total">{{formatMoney(0.00)}}</td>
               </tr>
               @endif
            </tbody>
            <tfoot>
               <tr>
                  <td colspan="2" rowspan="6" id="">
                     <textarea id="comment" onkeyup="auto_grow(this)" contentEditable="true">{{$repair->comment }}</textarea>
                  </td>
                  <td colspan="2">{{ __('repair.subtotal')}}</td>
                  <td contentEditable="true">{{ formatMoney($price_without_tax, 2)}}</td>
               </tr>
               <tr>
                  <td colspan="2">{{ __('repair.service_charges')}}</td>
                  <td contentEditable="true">{{ formatMoney($repair->service_charges, 2) }}</td>
               </tr>
               <tr>
                  <td colspan="2">{{ __('tax.name')}} {{ $tax }}</td>
                  <td contentEditable="true">{{ formatMoney($tax, 2)}}</td>
               </tr>
               <tr>
                  <td colspan="2">{{ __('repair.total')}}</td>
                  <td contentEditable="true">{{ formatMoney($total, 2)}}</td>
               </tr>
               <tr>
                  <td colspan="2">{{ __('repair.paid')}}</td>
                  <td contentEditable="true">{{ formatMoney($paid, 2)}}</td>
               </tr>
               <tr>
                  <td colspan="2"> {{ __('repair.payable') }} </td>
                  <td contentEditable="true">{{ formatMoney($total - $paid, 2) }} </td>
               </tr>
            </tfoot>
         </table>
         <hr>
         {{ clean(config('config.disclaimer')) }}
      </main>
      <br><br>
      <br><br>
      <br><br>
      <div id="print_button">{{ __('print')}}</div>
   </body>
   <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
   <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
</html>