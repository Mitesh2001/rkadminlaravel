
    <div class="form-group row">
		{{Form::hidden('company_id',$companyId)}}
        <div class="col-lg-4">
            {!! Form::label('plan', __('Plan'), ['class' => '']) !!}
			<span class="text-danger">*</span>
			{!!
				Form::select('plan',
				$plan,
				old('plan'), 
				['class' => 'form-control ui search selection top right pointing plan-select',
				'id' => 'plan-select'])
			!!}
			<span class="form-text text-muted">Please select plan</span>
        </div>
		<div class="col-lg-2 pt-7 text-center"><button type="button" class="btn btn-primary" onClick="addPlan()">Add Plan</button></div>
		<div class="col-lg-3">
            {!! Form::label('Subscription Date', __('Subscription Date'), ['class' => '']) !!}
			<span class="text-danger">*</span>
			{!!
				Form::date('subscription_date',
				isset($data['subscription_date']) ? $data['subscription_date'] : ((old('subscription_date'))?old('subscription_date'):date("Y-m-d")),
				['class' => 'form-control subscription_date','placeholder'=>'Select Subscription Date', 'min' => date("Y-m-d")])
			!!}
			<span class="form-text text-muted">Please select subscription date</span>
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
			</tbody>
			<tfoot>
			<tr>
			<td colspan="3"></td>
			<th colspan="2">Total Amount</th>
			<th class="text-right" id="final_amount">0</th>
			</tr>
			<tr class="sgst d-none">
			<td colspan="3"></td>
			<th class="align-middle">SGST %</th>
			<td class="align-middle"><input type="number" class="form-control text-right" name="sgst" max="100" min='0' id="sgst" step=".01" onChange="changeTax()" value="9" />
			<input type="hidden" class="form-control" name="sgst_amount" id="sgst_amount" value="9" /></td>
			<th class="text-right" id="sgst_amount_html">9</th>
			</tr>
			<tr class="cgst d-none">
			<td colspan="3"></td>
			<th class="align-middle">CGST %</th>
			<td class="align-middle"><input type="number" class="form-control text-right" name="cgst" max="100" min='0' id="cgst" step=".01" onChange="changeTax()" value="9" />
			<input type="hidden"  name="cgst_amount" id="cgst_amount" value="9" /></td>
			<th class="text-right" id="cgst_amount_html">9</th>
			</tr>
			<tr class="igst d-none">
			<td colspan="3"></td>
			<th class="align-middle">IGST %</th>
			<td class="align-middle"><input type="number" class="form-control text-right" name="igst" max="100" min='0' id="igst" step=".01" onChange="changeTax()" value="0" />
			<input type="hidden" class="form-control text-right" name="igst_amount" id="igst_amount" value="0" /></td>
			<th class="text-right" id="igst_amount_html">0</th>
			</tr>
			<tr>
			<td colspan="3"></td>
			<th colspan="2">Net Amount</th>
			<th class="text-right" id="net_amount">0</th>
			</tr>
			<tr class="d-none">
			<td colspan="3"></td>
			<th colspan="2">Round off Amount</th>
			<th class="text-right" id="round_off_amount">0</th>
			</tr>
			<tr>
			<td colspan="3"></td>
			<th colspan="2">Payment Pending</th>
			<th class="text-left">
				<div class="radio-inline">
					<label class="radio radio-outline">
					{{ Form::radio('payment_status', 'YES', (old('payment_status') == 'YES')?'checked':null, ['onClick'=>"paymentStatus(this.value)"]) }}
					<span></span>Yes</label>
					<label class="radio radio-outline">
					{{ Form::radio('payment_status', 'NO', (old('payment_status') == 'NO')?'checked':'checked', ['onClick'=>"paymentStatus(this.value)"]) }}
					<span></span>No</label>
				</div>
			</th>
			</tr>
			<tr id="payment_mode">
			<td colspan="3"></td>
			<th colspan="2" class="m-auto">Payment Mode <span class="text-danger">*</span></th>
			<td>{!!
				Form::select('payment_mode',
				$payment_modes,
				isset($data['payment_mode']) ? $data['payment_mode'] : old('payment_mode'), 
				['class' => 'form-control ui search selection top right pointing payment_mode-select',
				'id' => 'payment_mode-select','required','onChange'=>'changePaymentMode(this.value)'])
			!!}</td>
			</tr>
			<tr id="payment_date">
			<td colspan="3"></td>
			<th colspan="2" class="m-auto">Payment Date</th>
			<td>{!!
					Form::date('payment_date',
					isset($data['payment_date']) ? $data['payment_date'] : ((old('payment_date'))?old('payment_date'):date("Y-m-d")),
					['class' => 'form-control','placeholder'=>'Select Payment Date', 'min' => date("Y-m-d")])
				!!}</td>
			</tr>
			<tr class="payment d-none">
			<td colspan="3"></td>
			<th colspan="2" class="m-auto">Bank Name</th>
			<td>{!!
					Form::text('payment_bank_name',
					isset($data['payment_bank_name']) ? $data['payment_bank_name'] : old('payment_bank_name'),
					['class' => 'form-control','placeholder'=>'Enter Bank Name','pattern'=>'^[A-Za-z ]*$', 'maxlength'=>'40'])
				!!}</td>
			</tr>
			<tr class="payment d-none">
			<td colspan="3"></td>
			<th colspan="2" class="m-auto">Transaction Number</th>
			<td>{!!
					Form::text('payment_number',
					isset($data['payment_number']) ? $data['payment_number'] : old('payment_number'),
					['class' => 'form-control text-right','placeholder'=>'Enter Transaction Number', 'pattern'=>'^[0-9 ]*$','minlength'=>'16', 'maxlength'=>'16'])
				!!}</td>
			</tr>
			
			</tfoot>
			</table>
        </div>        
    </div>
	 <div class="form-group row">
        <div class="col-lg-12">
		</div>        
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col-lg-6">
			@if(!empty($subscription))
                {{Form::hidden('subscription_id',encrypt($subscription->id))}}
            @endif
                {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitpermission']) !!}
				<a href="{{url('rkadmin/subscriptions?company_id='.encrypt($companyId))}}" class="btn btn-md btn-primary ml-2">Cancel</a>
            </div>
        </div>
    </div>
{{-- page scripts --}}
@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
getCompanyDetails('<?php echo $companyId?>');
<?php if(old('payment_mode')){?>
changePaymentMode("<?php echo old('payment_mode')?>");
<?php }?>
});
</script>
<script type="text/javascript">
var state_id = country_id = 0;
function getCompanyDetails(company_id){
	if(!company_id){
		$("#subscriptionCreateForm").before('<div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Please select company!</strong></div>');
		return false;
	}
	jQuery.ajax({
		url: '{!! route('admin.subscriptions.company.detail') !!}', //this is your uri
		type: 'GET',
		data: { company_id:company_id },
		dataType: 'json',
		success: function(response){
			if(response.success){
				var company = response.company;
				state_id = company.state_id;
				country_id = company.country_id;
				
				if(state_id == 'Gujarat'){
					if(!jQuery('.igst').hasClass('d-none'))
						jQuery('.igst').addClass('d-none');
						jQuery('.sgst').removeClass('d-none');
						jQuery('.cgst').removeClass('d-none');
						jQuery('#igst_amount').val(0);
						jQuery('#igst').val(0);
						
				}else{
					if(!jQuery('.sgst').hasClass('d-none'))
						jQuery('.sgst').addClass('d-none');
					if(!jQuery('.cgst').hasClass('d-none'))
						jQuery('.cgst').addClass('d-none');
					jQuery('.igst').removeClass('d-none');
					
					jQuery('#sgst_amount').val(0);
					jQuery('#sgst').val(0);
					jQuery('#cgst_amount').val(0);
					jQuery('#cgst').val(0);
				}
				
			}
		}
	});
}
var Plans_final_amount = Plans_net_amount = 0;
function addPlan(){
	var plan_id = jQuery('#plan-select').val();
	var subscription_date = jQuery('.subscription_date').val();
	jQuery('.plan-select option[value="'+plan_id+'"]').prop('disabled',true)

	if(plan_id){
		jQuery.ajax({
			url: '{!! route('admin.subscriptions.plan.detail') !!}', //this is your uri
			type: 'GET',
			data: { plan_id:plan_id },
			dataType: 'json',
			success: function(response){
				if(response.success){

					var plan = response.plan;
					var plan_description = (plan.description) ? plan.description : '';
					var count = 18;
					var plan_desc = plan_description.slice(0, count) + (plan_description.length > count ? "..." : "");
					console.log(plan_description.length)

					jQuery('#plans_list').append('<tr id="plans_'+plan.id+'"><td><a class="btn btn-link p-1" onClick="deletePlan('+plan.id+')"><i class="flaticon2-trash text-danger"></i></a>'+plan.name+'<input type="hidden" name="plans['+plan.id+']" id="plans_id_'+plan.id+'" value="'+plan.id+'" /><div class="word-wrap" title="'+plan_description+'">'+plan_desc+' Email : '+plan.no_of_email+' SMS : '+plan.no_of_sms+'</div></td><td class="text-right">'+subscription_date+'<input type="hidden" name="subscription_date['+plan.id+']" id="subscription_date_'+plan.id+'" value="'+subscription_date+'" /></td><td class="text-right">'+plan.price.toFixed(2)+'<input type="hidden" name="plans_price['+plan.id+']" id="plans_price_'+plan.id+'" value="'+plan.price+'" /></td><td ><input type="number" class="form-control text-right" name="plans_discount['+plan.id+']" min="0" max="100" id="plans_discount_'+plan.id+'" step=".01" onChange="getPlanDiscount('+plan.id+',this.value)" value="0" /></td><td class="text-right"><span id="discount_amount_'+plan.id+'">0.00</span><input type="hidden" name="plans_discount_amount['+plan.id+']" id="plans_discount_amount_'+plan.id+'" value="0" /></td><td class="text-right"><span id="final_amount_'+plan.id+'">'+plan.price.toFixed(2)+'</span><input type="hidden" name="plans_final_amount['+plan.id+']" id="plans_final_amount_'+plan.id+'" value="'+plan.price+'" /></td></tr>');
					Plans_final_amount += parseFloat(plan.price);
					Plans_final_amount = parseFloat(Plans_final_amount)
					jQuery('#final_amount').html(Plans_final_amount.toFixed(2));
					changeTax();		
					jQuery('#plan-select').val('');
				}
			}
		});
	}else 
	{
		$("#subscriptionCreateForm").before('<div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Please select plan!</strong></div>');
		return false;
	}
}
function changeTax(){
	var sgst = jQuery('#sgst').val();
	var cgst = jQuery('#cgst').val();
	var igst = jQuery('#igst').val();
	
	if(sgst>=0 && sgst<=100 && cgst>=0 && cgst<=100 && igst>=0 && igst<=100){
		if(state_id == 'Gujarat'){
			var sgstamt = parseFloat(Plans_final_amount) * parseFloat(sgst) / 100;
			var cgstamt = parseFloat(Plans_final_amount) * parseFloat(cgst) / 100;
			sgstamt = parseFloat(sgstamt);
			jQuery('#sgst_amount').val(sgstamt);
			jQuery('#sgst_amount_html').html(sgstamt.toFixed(2));
			cgstamt = parseFloat(cgstamt);
			jQuery('#cgst_amount').val(cgstamt);
			jQuery('#cgst_amount_html').html(cgstamt.toFixed(2));
			Plans_net_amount = parseFloat(Plans_final_amount) + sgstamt + cgstamt;
		}else{
			var igstamt = parseFloat(Plans_final_amount) * parseFloat(igst) / 100;
			igstamt = parseFloat(igstamt);
			jQuery('#igst_amount').val(igstamt);
			jQuery('#igst_amount_html').html(igstamt.toFixed(2));
			Plans_net_amount = parseFloat(Plans_final_amount) + igstamt;
		}	
	Plans_net_amount = parseFloat(Plans_net_amount);
	jQuery('#net_amount').html(Plans_net_amount.toFixed(2));
	jQuery('#round_off_amount').html(Math.round(Plans_net_amount).toFixed(2));
	}
}
function changePaymentMode(mode){
	if(mode !='' && mode != 'CASH'){
		jQuery('.payment').removeClass('d-none');
	}else{
		if(!jQuery('.payment').hasClass('d-none'))
		jQuery('.payment').addClass('d-none');
	}
}
function getPlanDiscount(id,discount){
	if(discount<=100 && discount>=0){
		var old_plans_discount_amount = jQuery('#plans_discount_amount_'+id).val();
		var planprice = jQuery('#plans_price_'+id).val();
		var discount_amount = planprice * discount / 100;
		discount_amount = parseFloat(discount_amount);
		jQuery('#plans_discount_amount_'+id).val(discount_amount);
		jQuery('#discount_amount_'+id).html(discount_amount.toFixed(2));
		var final_amount = planprice - discount_amount;
		final_amount = parseFloat(final_amount);
		jQuery('#plans_final_amount_'+id).val(final_amount);
		jQuery('#final_amount_'+id).html(final_amount.toFixed(2));
		Plans_final_amount = parseFloat(Plans_final_amount) + parseFloat(old_plans_discount_amount) - parseFloat(discount_amount);
		Plans_final_amount = parseFloat(Plans_final_amount);
		jQuery('#final_amount').html(Plans_final_amount.toFixed(2));
		jQuery('#round_off_amount').html(Math.round(Plans_final_amount).toFixed(2));
		changeTax();
	}else{
		return false;
	}
}
function deletePlan(planId){

	var plans_final_amount = jQuery('#plans_final_amount_'+planId).val();	
	var final_amount = jQuery('#final_amount').html()
	var net_amount = jQuery('#net_amount').html()
	
	Plans_final_amount = parseFloat(final_amount) - parseFloat(plans_final_amount)
	Plans_net_amount = parseFloat(net_amount) - parseFloat(plans_final_amount)

	jQuery('#net_amount').html(Plans_net_amount.toFixed(2));
	jQuery('#final_amount').html(Plans_final_amount.toFixed(2));
	jQuery('#plans_'+planId).remove();
	jQuery('.plan-select option[value="'+planId+'"]').prop('disabled',false)
	changeTax();
}
function paymentStatus(value){
	if(value === 'YES'){
		jQuery('#payment_mode, #payment_date, .payment').addClass('d-none');
		jQuery('#payment_mode-select').prop('required',false);
	}else{
		jQuery('#payment_mode, #payment_date, .payment').removeClass('d-none');
		jQuery('#payment_mode-select').prop('required',true);
	}
}
</script>
@endsection