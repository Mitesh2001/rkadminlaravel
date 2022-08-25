<br>
@forelse($formData as $row)
<div class="row mt-5 mb-5">
    <div class="col-md-2"><b>*</b>&nbsp; <b> Section Name:</b> </div><div class="col-md-5"><b><h5>{{$row->name}}</b></h5></div>
</div>
    <table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
        <thead>
            <tr>
                <th>Input Type</th>
                <th>Label Name</th>
            </tr>
        </thead>
        @foreach ($row->getFieldData as $value)
            <tr>
                <td>{{$inputTypes[$value->input_type]}}</td>
                <td>{{$value->label_name}}</td>
            </tr>
        @endforeach
    </table>
@empty
<table class="table table-bordered table-hover table-checkable" id="kt_datatable" style="margin-top: 13px !important">
    <thead>
        <tr>
            <th>Input Type</th>
            <th>Label Name</th>
        </tr>
    </thead>
    <tr>
        <td colspan="2" class="text-center">No records available</td>
    </tr>
@endforelse