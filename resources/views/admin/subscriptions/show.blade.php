{{-- Extends layout --}}
@extends('admin.layouts.default')
@section('content')
    <div class="card card-custom">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="flaticon2-crisp-icons text-primary"></i>
				</span>
				<h3 class="form_title">View Subscription 
				@if(!empty($subscription))
					<span>| {{$subscription->subscriptions_uid}}</span>
					<span/>| Client Name : {{isset($subscription->client->name) ? $subscription->client->name : null}} | Company Name : {{$subscription->company->company_name}}</span>
				@endif</h3>
			</div>
		</div>
		<div class="card-body">
			@include('admin.layouts.alert')
			<div class="row">
				<div class="col-md-12 text-right mb-5">
					<a href="#"  class="btn btn-success" data-toggle="modal" data-target="#invoice_email" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon2-email"></i>Email</a>
					<a href="{{url('rkadmin/subscriptions/'.encrypt($subscription->id).'?is_pdf=1')}}" class="btn btn-success btn-shadow font-weight-bold mr-2"><i class="flaticon-download"></i>Download</a>
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
					<td>{{ $cplan->plan->name }}, Email : {{$cplan->no_of_email}} SMS : {{$cplan->no_of_sms}}</td>
					<td width="15%" class="text-right">{{ $cplan->subscription_date }}</td>
					<td width="5%" class="text-right">{{ Helper::decimalNumber($cplan->plan_price) }}</td>
					<td width="10%" class="text-right">{{ $cplan->discount }}</td>
					<td width="15%" class="text-right">{{ Helper::decimalNumber($cplan->discount_amount) }}</td>
					<td width="20%" class="text-right">{{ Helper::decimalNumber($cplan->final_amount) }}</td>
					</tr>
					@endforeach
					</tbody>
					<tfoot>
					<tr>
					<td colspan="3"></td>
					<th colspan="2">Total Amount</th>
					<th id="final_amount" class="text-right">{{ Helper::decimalNumber($subscription->total_amount) }}</th>
					</tr>
					@if($subscription->state_name == 'Gujarat')
					<tr class="sgst ">
						<td colspan="3"></td>
						<th colspan="2" class="align-middle">SGST @ {{ $subscription->sgst }}%</th>
						<th class="text-right">{{ Helper::decimalNumber($subscription->sgst_amount) }}</th>
					</tr>
					<tr class="cgst ">
						<td colspan="3"></td>
						<th colspan="2" class="align-middle">CGST @ {{ $subscription->cgst }}%</th>
						<th class="text-right">{{ Helper::decimalNumber($subscription->cgst_amount) }}</th>
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
					<th id="net_amount" class="text-right">{{ Helper::decimalNumber($subscription->final_amount) }}</th>
					</tr>
					<tr class="d-none">
					<td colspan="3"></td>
					<th colspan="2">Round off Amount</th>
					<th class="text-right">{{ Helper::decimalNumber($subscription->round_off_amount) }}</th>
					</tr>
					<tr>
					<td colspan="3"></td>
					<th colspan="2">Payment Pending</th>
					<th class="text-right">{{ $subscription->is_payment_pending }}</th>
					</tr>
					@if(isset($subscription->payment_mode))
					<tr>
					<td colspan="3"></td>
					<th colspan="2" class="m-auto">Payment Mode </th>
					<th class="text-right">{{ $payment_modes[$subscription->payment_mode] }}</th>
					</tr>
					@endif
					<tr>
					<td colspan="3"></td>
					<th colspan="2" class="m-auto">Payment Date</th>
					<th class="text-right">{{ date("d/m/Y",strtotime($subscription->payment_date)) }}</th>
					</tr>
					@if($subscription->payment_mode != 'CASH' && ($subscription->is_payment_pending == 'NO' || $subscription->is_payment_pending == '') )
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
					<th class="text-right">{{ $subscription->payment_amount }}</th>
					</tr> -->
					@endif
					</tfoot>
					</table>
				</div>        
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-lg-6">
						<a href="{{ url('rkadmin/subscriptions?company_id='.encrypt($subscription->company->id))}}" class="btn btn-primary btn-shadow font-weight-bold"><i class="flaticon2-back"></i> Back</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="invoice_email" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Invoice</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<i aria-hidden="true" class="ki ki-close"></i>
						</button>
					</div>
					{{Form::open(['url'=>'rkadmin/subscriptions/'.encrypt($subscription->id),'method'=>'GET'])}}
						<div class="modal-body">
							{{Form::hidden('is_email','1')}}
							<div class="row">
								<div class="col-lg-12">
									{!! Form::label('email', __('Email'), ['class' => '']) !!}
									<span class="text-danger">*</span>
									{{Form::text('email',!empty($subscription->client->email) ? $subscription->client->email : null,['class'=>'form-control','required'])}}
									<span class="form-text text-muted">Add multiple email id by comma separated. Maximum 3</span>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary font-weight-bold">Send Invoice</button>
						</div>
					{{Form::close()}}
				</div>
			</div>
		</div>
	</div>
	<!--end::Card-->
@stop

