<html xmlns="http://www.w3.org/TR/REC-html40">
   <head>
      <meta http-equiv=Content-Type content="text/html; charset=utf-8">
      <title>{{__('service_order')}} #{{$repair->id}}</title>
   </head>
   <link rel="stylesheet" type="text/css" href="{{ asset('css/templates/invoice4.css')}}">
   <body>
      <div id="header">
         <table width="100%" border="0" cellpadding="5" cellspacing="4" bgcolor="#EDEDEA">
            <tr>
               <td width="50%" valign="top" bgcolor="#EDEDEA"><span class="style15">
                  <img src="{{ asset(config('config.main_logo')) }}" alt="" width="auto" height="80" align="left"/>{{config('config.company_name')}}<br>
                  </span><span class="style16">{{config('config.address_line_1')}}<br>
                  {{__('phone')}}: {{config('config.phone')}}, </br> {{__('semail')}}: {{config('config.email')}}</span>
               </td>
               <td width="48" valign="top" bgcolor="#EDEDEA" class="no_print" onClick="window.print()">
                  <div align="center"></div>
               </td>
               <td width="50%" valign="top" bgcolor="#EDEDEA">
                  <div align="right" ><span>{{__('service_order')}}</span><span>          <br>
                     </span>
                  </div>
                  <div align="right" >#{{str_pad($repair->id, 4, '0', STR_PAD_LEFT)}}</div>
               </td>
            </tr>
         </table>
      </div>
      <table border=0 cellspacing=0 cellpadding=0 align=center
         width=100%>
         <tr>
            <td colspan=9 nowrap></td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=top nowrap>
               <div align="right">
                  <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($repair->code, "C128") }}" alt="barcode"   />
               </div>
            </td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=top nowrap>
               <table width="100%" border="0" cellspacing="10" cellpadding="10">
                  <tr>
                     <td width="15%" bgcolor="#EDEDEA">
                        <div align="center"><strong>{{__('repair.created_at')}}:</strong></div>
                     </td>
                     <td width="35%" class="td_bold">
                        <div align="center"><strong>{{$repair->created_at}}</strong></div>
                     </td>
                     <td width="15%" bgcolor="#EDEDEA">
                        <div align="center"><strong>{{__('repair.model')}}</strong></div>
                     </td>
                     <td width="35%" class="td_bold">
                        <div align="center"><strong>{{$repair->model}}</strong></div>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=top nowrap>
               <table width="100%" border="0" cellpadding="0" cellspacing="0" id="order_info">
                  <tr>
                     <td width="50%" height="35" class="border_table">
                           <strong>{{__('customer.customer')}}</strong>
                     </td>
                     <td width="50%" height="35" class="border_table">
                        <span class="style14">&nbsp;&nbsp;{{__('repair.category')}}</span>
                     </td>
                  </tr>
                  <tr>
                     <td width="50%" valign="top" class="border_table">
                        <table width="100%" border="0" cellspacing="10" cellpadding="10">
                           <tr>
                              <td class="td_bold"><strong>{{ $repair->customerData->name}}</strong></td>
                           </tr>
                           <tr>
                              <td class="td_bold"><strong>{{ $repair->customerData->phone}}</strong></td>
                           </tr>
                           <tr>
                              <td class="td_bold"><strong>{{ $repair->customerData->address}}</strong></td>
                           </tr>
                           <tr>
                              <td class="td_bold"><strong>{{ $repair->customerData->email}}</strong></td>
                           </tr>
                        </table>
                     </td>
                     <td width="50%" class="border_table">
                        <table width="100%" border="0" cellpadding="5" cellspacing="4" class="no_border_table">
                           <tr>
                              <td width="31%">
                                 <div align="right">{{__('repair.manufacturer')}}:</div>
                              </td>
                              <td width="69%" class="td_bold"><strong>{{$repair->manufacturer}}</strong></td>
                           </tr>
                           <tr>
                              <td>
                                 <div align="right">{{__('repair.category')}}</div>
                              </td>
                              <td class="td_bold"><strong>{{ $repair->category}}</strong></td>
                           </tr>
                           <tr>
                              <td>
                                 <div align="right">{{__('repair.model')}}</div>
                              </td>
                              <td class="td_bold"><strong>{{ $repair->model}}</strong></td>
                           </tr>
                           <tr>
                              <td>
                                 <div align="right">{{__('repair.serial_number')}}</div>
                              </td>
                              <td class="td_bold"><strong>{{ $repair->serial_number}}</strong></td>
                           </tr>
                           <tr>
                              <td>
                                 <div align="right">{{__('repair.created_at')}}</div>
                              </td>
                              <td class="td_bold"><strong>{{ $repair->created_at}}</strong></td>
                           </tr>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td height="35" class="border_table"><strong>&nbsp;&nbsp;{{__('repair.defect')}}</strong></td>
                     <td height="35" class="border_table"><strong>&nbsp;&nbsp;{{__('repair.diagnostics')}}</strong></td>
                  </tr>
                  <tr>
                     <td valign="top" class="border_table">
                        <table width="100%" border="0" cellspacing="4" cellpadding="5">
                           <tr>
                              <td class="td_bold"><strong>{{ $repair->defect}}</strong></td>
                           </tr>
                        </table>
                     </td>
                     <td valign="top" class="border_table">
                        <table width="100%" border="0" cellspacing="4" cellpadding="5">
                           <tr>
                              <td class="td_bold"><strong>{{ $repair->diagnostics}}</strong></td>
                           </tr>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td height="35" class="border_table"><strong>&nbsp;&nbsp;{{__('repair.status')}}</strong></td>
                     <td height="35" class="border_table"><strong>&nbsp;&nbsp;{{__('repair.items')}}</strong></td>
                  </tr>
                  <tr>
                     <td valign="top" class="border_table">
                        <table width="100%" border="0" cellspacing="4" cellpadding="5">
                           <tr>
                              <td class="td_bold"><strong>{{$repair->status->label }}</strong></td>
                           </tr>
                        </table>
                     </td>
                     <td height="35" class="border_table">
                        <table width="100%" border="0" cellspacing="4" cellpadding="5">
                           <tr>
                              <td class="td_bold">
                                 <div align="right">
                                    <strong>
                                       {{ $subtotal = 0}}
                                       @if($repair->items)
                                       @foreach($repair->items as $item)
                                       <p>{{$item->product_name }}({{$item->quantity }}) - {{$item->subtotal}}</p>
                                       {{ $subtotal += $item->subtotal}}
                                       @endforeach
                                       @endif
                                    </strong>
                                 </div>
                              </td>
                           </tr>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td height="35" class="border_table"><strong>&nbsp;&nbsp;{{__('repair.comments')}}:</strong></td>
                     <td height="35" class="border_table"><strong>&nbsp;&nbsp;{{__('repair.grand_total')}}:</strong></td>
                  </tr>
                  <tr>
                     <td valign="top" class="border_table">
                        <table width="100%" border="0" cellspacing="4" cellpadding="5">
                           <tr>
                              <td class="td_bold"><strong>{{$repair->comments}}</strong></td>
                           </tr>
                        </table>
                     </td>
                     <td height="35" class="border_table">
                        <table width="100%" border="0" cellspacing="4" cellpadding="5">
                           <tr>
                              <td class="td_bold">
                                 <div align="right"><strong>{{formatMoney($repair->grand_total)}}</strong></div>
                              </td>
                           </tr>
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td colspan="9" valign=top nowrap></td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=bottom>&nbsp;&nbsp;&nbsp;</td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=bottom bgcolor="#EDEDEA">
               <div align="center" class="td_bold"><strong><span>
                  {{ ($disclaimer) }}
                  </span></strong><br>
                  <br>
               </div>
            </td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=bottom nowrap></td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=bottom nowrap>
               <table width="100%" border="0" cellspacing="4" cellpadding="5">
                  <tr>
                     <td width="53%">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                     <td width="47%">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                  </tr>
                  <tr>
                     <td class="td_bold">{{__('configuration.customer_sign')}}: . . . . . . . . . . . . . . . . . .</td>
                     <td class="td_bold">{{__('configuration.company_sign')}}: . . . . . . . . . . . . . . . . . .</td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td height="25" colspan="9" valign=bottom nowrap>
               </v:textbox>
               <table cellpadding=0 cellspacing=0 width="100%">
                  <tr>
                     <td>            </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
      <div id="footer">
         <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
               <td bgcolor="#EDEDEA">
                  <table width="100%" border="0" cellspacing="4" cellpadding="5">
                     <tr>
                        <td class="td_bold">{{__('repair.check_status')}}:  
                           {{\URL::to('/')}}
                           <br>
                           {{__('repair.order_number')}}: <strong>{{str_pad($repair->id, 4, '0', STR_PAD_LEFT)}}</strong> |  {{__('repair.code')}}: <strong>{{$repair->code}}</strong>
                        </td>
                     </tr>
                  </table>
               </td>
               <td width="116">
                  <div align="center">
                     <img src="{{ asset('uploads/temp/qrcode.png') }}" height="60" alt="qrcode"/>
                  </div>
               </td>
            </tr>
         </table>
      </div>
   </body>
      <script type="text/javascript" src="{{ asset('js/bundle.js') }}"></script>
   <script type="text/javascript" src="{{ asset('js/templates/print.js') }}"></script>
   
</html>