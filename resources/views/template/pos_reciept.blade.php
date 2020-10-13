<html>
    <head>
        <meta charset="utf-8">
        <title>Invoice</title>
        <base href="{{\URL::to('/')}}"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <style type="text/css" media="all">
            {!! file_get_contents(public_path('css/bootstrap.min.css')) !!}
        </style>
        <style type="text/css" media="all">
            body { color: #000; }
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .bootbox .modal-footer { border-top: 0; text-align: center; }
            h3 { margin: 5px 0; }
            .order_barcodes img { float: none !important; margin-top: 5px; }
            @media print {
                .no-print { display: none; }
                #wrapper { max-width: 480px; width: 100%; min-width: 250px; margin: 0 auto; }
                .no-border { border: none !important; }
                .border-bottom { border-bottom: 1px solid #ddd !important; }
                table tfoot { display: table-row-group; }
            }
        </style>
    </head>
    
    <body>
    <div id="wrapper">
        <div id="receiptData">
           
            <div id="receipt-data">
                <div class="text-center">
                    <?= !empty($biller->logo) ? '<img src="' . ('/uploads/logos/' . $biller->logo) . '" alt="">' : ''; ?>
                    <h3 style="text-transform:uppercase;"><?=$biller->company && $biller->company != '-' ? $biller->company : $biller->name;?></h3>
                    <?php
                    echo '<p>' . $biller->address . ' ' . $biller->city . ' ' . $biller->postal_code . ' ' . $biller->state . ' ' . $biller->country .
                    '<br>' . __('sale.tel') . ': ' . $biller->phone;
                    echo '</p>';
                    ?>
                </div>
                <?php
                echo '<p>' . __('sale.date') . ': ' . ($inv->date) . '<br>';
                echo __('sale.sale_no_ref') . ': ' . $inv->reference_no . '<br>';
                echo __('sale.sales_person') . ': ' . $created_by->first_name . ' ' . $created_by->last_name . '</p>';
                echo '<p>';

                if($customer) {
                    echo __('sale.customer') . ': ' . ($customer->company && $customer->company != '-' ? $customer->company : $customer->name) . '<br>';
                    echo __('sale.tel') . ': ' . $customer->phone . '<br>';
                    echo __('sale.address') . ': ' . $customer->address . '<br>';
                    echo $customer->city . ' ' . $customer->state . ' ' . $customer->country . '<br>';
                }else{
                    echo __('sale.customer') . ': ' . $inv->customer . '<br>';
                }
                echo '</p>';
                ?>

                <div style="clear:both;"></div>



                <table class="table table-condensed">
                    <tbody>
                        @foreach ($items as $row)
                            <tr>
                                <td colspan="2" class="no-border">
                                    #1: {{$row->product_name}} 
                                    {{$row->serial_no ? $row->serial_no : ''}}
                                    <span class="pull-right">{{$row->tax_code ? '*' . $row->tax_code : ''}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="no-border">
                                    {{$row->quantity}} x {{($row->item_discount != 0 ? '(' . formatMoney($row->unit_price + $row->item_discount) . ' - ' . formatMoney($row->item_discount) . ')' : formatMoney($row->unit_price))}}
                                </td>
                                <td class="no-border border-bottom text-right">{{formatMoney($row->subtotal)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th><?=__('sale.total');?></th>
                            <th class="text-right"><?=formatMoney(($inv->total + $inv->product_tax));?></th>
                        </tr>
                        <?php
                        if ($inv->order_tax != 0) {
                            echo '<tr><th>' . __('sale.tax') . '</th><th class="text-right">' . formatMoney( $inv->order_tax) . '</th></tr>';
                        }
                        if ($inv->order_discount != 0) {
                            echo '<tr><th>' . __('sale.order_discount') . '</th><th class="text-right">' . formatMoney($inv->order_discount) . '</th></tr>';
                        }

                        if ($inv->shipping != 0) {
                            echo '<tr><th>' . __('sale.shipping') . '</th><th class="text-right">' . formatMoney($inv->shipping) . '</th></tr>';
                        }

                        ?>


                        <tr>
                            <th><?=__('sale.grand_total'); ?></th>
                            <th class="text-right"><?=formatMoney($inv->grand_total); ?></th>
                        </tr>

                        <?php if ($inv->paid < ($inv->grand_total)) {
                            ?>
                            <tr>
                                <th><?=__('sale.paid_amount'); ?></th>
                                <th class="text-right"><?=formatMoney($inv->paid); ?></th>
                            </tr>
                            <tr>
                                <th><?=__('sale.due_amount'); ?></th>
                                <th class="text-right"><?=formatMoney(( ($inv->grand_total + $inv->rounding)) - ($inv->paid)); ?></th>
                            </tr>
                            <?php
                        } ?>
                    </tfoot>
                </table>


                @if($payments)
                    <table class="table table-striped table-condensed"><tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            @if($payment->paid_by == 'cash' && $payment->pos_paid)
                                <td> {{__('sale.paid_by')}}: {{__($payment->paid_by)}}</td>
                                <td colspan="2">{{__('sale.amount')}}: {{formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid)}}</td>
                                <td>{{__('sale.change')}}: {{($payment->pos_balance > 0 ? formatMoney($payment->pos_balance) : 0) }}</td>
                            @elseif(($payment->paid_by == 'card' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no)
                                <td>{{__('payment.paid_by')}}: {{__($payment->paid_by)}} </td>
                                <td>{{__('payment.amount')}}: {{formatMoney($payment->pos_paid)}} </td>
                                <td>{{__('payment.cc_no')}}: xxxx xxxx xxxx  {{substr($payment->cc_no, -4)}} </td>
                                <td>{{__('payment.cc_holder')}}:  {{$payment->cc_holder}}</td>
                            @elseif($payment->paid_by == 'cheque' && $payment->cheque_no)
                                <td> {{__('payment.paid_by')}}: {{__($payment->paid_by)}}</td>
                                <td colspan="2"> {{__('sale.amount')}}: {{formatMoney($payment->pos_paid)}}</td>
                                <td> {{__('payment.cheque_no')}}: {{$payment->cheque_no}}</td>
                            @elseif($payment->paid_by == 'voucher' && $payment->pos_paid)
                                <td> {{__('payment.paid_by')}}: {{($payment->paid_by)}}</td>
                                <td> {{__('payment.no')}}: xxxx xxxx xxxx {{substr($payment->cc_no, -4)}}</td>
                                <td> {{__('payment.amount')}}: {{formatMoney($payment->pos_paid)}}</td>
                                <td> {{__('payment.balance')}}: {{formatMoney(getCardBalance($payment->cc_no))}}</td>
                            @elseif($payment->paid_by == 'other' && $payment->amount)
                                <td colspan="2">{{__('payment.paid_by')}}: {{ __($payment->paid_by)}}</td>
                                <td colspan="2">{{__('payment.amount')}}: {{formatMoney($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid)}}</td>
                                </tr>
                                <td colspan="4">{{__('sale.payment_note')}}: {{$payment->note}}</td>
                            @endif

                        </tr>
                    @endforeach
                    </tbody></table>
                @endif

                @if($inv->note !== '')
                    <p class="text-center">{{ decode_html($inv->note) }}</p>
                @endif
                @if($inv->staff_note !== '')
                    <p class="no-print"><strong>{{ __('sale.staff_note') }}</strong> {{decode_html($inv->staff_note)}}</p>
                @endif
                @if($biller->invoice_footer !== '')
                    <p class="text-center">{{ decode_html($biller->invoice_footer) }}</p>
                @endif

            </div>

            <div class="order_barcodes text-center">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($inv->reference_no, "C128") }}" alt="barcode"   />
                <br>
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(100)->margin(0)->generate(\URL::to('/pos/view'.$inv->id))) !!} ">
            </div>
            <div style="clear:both;"></div>
        </div>

      
    </div>
    </body>
    </html>