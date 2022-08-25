<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Subscription Invoice</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-supermarket text-primary"></i>
                </span>
                <h4 class="text-center">Invoice</h4>
                <h5 class="card-label">{{$subscription->company->company_name.' ( '.$subscription->client->name.' )'  }}</h5>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">
                <table class="table table-bordered">
                <thead>
                <tr>
                <th>Plan</th>
                <th width="15%">Subscription Date</th>
                <th width="5%">Amount</th>
                <th width="10%">Discount %</th>
                <th width="15%">Discount Amount</th>
                <th width="20%">Final Amount</th>
                </tr>
                </thead>
                <tbody id="plans_list">
                
                @foreach ($clientplans as $cplan)
                <tr>
                <th>{{ $cplan->plan->name }}</th>
                <th width="15%" class="text-right">{{ $cplan->subscription_date }}</th>
                <th width="5%" class="text-right">{{ $cplan->plan_price }}</th>
                <th width="10%" class="text-right">{{ $cplan->discount }}</th>
                <th width="15%" class="text-right">{{ $cplan->discount_amount }}</th>
                <th width="20%" class="text-right">{{ $cplan->final_amount }}</th>
                </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                <td colspan="3"></td>
                <th colspan="2">Total Amount</th>
                <th class="text-right" id="final_amount">{{ $subscription->total_amount }}</th>
                </tr>
                @if($subscription->company->state_id == 'Gujarat')
                <tr class="sgst ">
                <td colspan="3"></td>
                <th colspan="2" class="align-middle">SGST @ {{ $subscription->sgst }}%</th>
                <td class="text-right">{{ $subscription->sgst_amount }}</th>
                </tr>
                <tr class="cgst ">
                <td colspan="3"></td>
                <th colspan="2" class="align-middle">CGST @ {{ $subscription->cgst }}%</th>
                <td class="text-right">{{ $subscription->cgst_amount }}</th>
                </tr>
                @else
                <tr class="igst ">
                <td colspan="3"></td>
                <th colspan="2" class="align-middle">IGST @ {{ $subscription->igst }}%</th>
                <th class="text-right">{{ $subscription->igst_amount }}</th>
                </tr>
                @endif
                <tr>
                <td colspan="3"></td>
                <th colspan="2">Net Amount</th>
                <th class="text-right" id="net_amount">{{ $subscription->final_amount }}</th>
                </tr>
                <tr>
                <td colspan="3"></td>
                <th colspan="2">Round off Amount</th>
                <th class="text-right">{{ $subscription->round_off_amount }}</th>
                </tr>
                <tr>
                <td colspan="3"></td>
                <th colspan="2">Payment Pending</th>
                <th class="text-right">{{ $subscription->is_payment_pending }}</th>
				</tr>
                @if(isset($subscription->payment_mode))
                    <tr>
                    <td colspan="3"></td>
                    <th colspan="2" class="m-auto">Payment Mode <span class="text-danger">*</span></th>
                    <th class="text-right">{{ $payment_modes[$subscription->payment_mode] }}</th>
                    </tr>
                @endif
                <tr>
                <td colspan="3"></td>
                <th colspan="2" class="m-auto">Payment Date</th>
                <th class="text-right">{{ date("d/m/Y",strtotime($subscription->payment_date)) }}</th>
                </tr>
                @if($subscription->payment_mode != 'CASH' && ($subscription->is_payment_pending == 'NO' || $subscription->is_payment_pending == ''))
                <tr class="payment ">
                <td colspan="3"></td>
                <th colspan="2" class="m-auto">Bank Name</th>
                <th class="text-right">{{ $subscription->payment_bank_name }}</th>
                </tr>
                <tr class="payment ">
                <td colspan="3"></td>
                <th colspan="2" class="m-auto">Transaction Number</th>
                <th class="text-right">{{ $subscription->payment_number }}</th>
                </tr>
                <!-- <tr class="payment ">
                <td colspan="3"></td>
                <th colspan="2" class="m-auto">Transaction Amount</th>
                <td class="text-right">{{ $subscription->payment_amount }}</td>
                </tr> -->
                @endif
                </tfoot>
                </table>
            </div>        
        </div>
    </div>
</body>
</html>