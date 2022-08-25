{{-- Extends layout --}}
@extends('admin.layouts.default')
@section('content')
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user text-primary"></i>
                </span>
                <h3 class="form_title">Client : <span>{{ $companyData->client_data['name'].' | '. ' Company : '.$companyData['company_name']}}</span></h3>
            </div>
        </div>
        <div class="card-body">
            @include('admin.layouts.alert')
        <div class="form-group row">
            <div class="col-lg-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Client Name</th>
                        <td>{{$companyData->client_data['name']}}</td>
                    </tr>
                    <tr>
                        <th>Compay Name</th>
                        <td>{{$companyData['company_name']}}</td>
                    </tr>
                    <tr>
                        <th>Company Type</th>
                        <td>{{$companyData['company_type_id'] ? (isset($companyType[$companyData['company_type_id']])?$companyType[$companyData['company_type_id']]:null) : null}}</td>
                    </tr>
                    <tr>
                        <th>Industries Type</th>
                        <td>{{$companyData['industry_id'] ? (isset($industries[$companyData['industry_id']])?$industries[$companyData['industry_id']]: null) : null}}</td>
                    </tr>
                    <tr>
                        <th>Excise Number</th>
                        <td>{{$companyData['excise_no']}}</td>
                    </tr>
                    <tr>
                        <th>Vat Number</th>
                        <td>{{$companyData['vat_no']}}</td>
                    </tr>
                    <tr>
                        <th>Company License Type</th>
                        <td>{{$companyData['company_license_type'] ? (isset($licenseType[$companyData['company_license_type']])?$licenseType[$companyData['company_license_type']]:null) : null}}</td>
                    </tr>
                    <tr>
                        <th>Company License Number</th>
                        <td>{{$companyData['company_license_no']}}</td>
                    </tr>
                    <tr>
                        <th>GST Number</th>
                        <td>{{$companyData['gst_no']}}</td>
                    </tr>
                    <tr>
                        <th>PAN Number</th>
                        <td>{{$companyData['pan_no']}}</td>
                    </tr>
                    <tr>
                        <th>Website</th>
                        <td>{!! $companyData['website'] ? '<a href="'.$companyData['website'].'">'.$companyData['website'].'</a>' : null !!}</td>
                    </tr>
                    <tr>
                        <th>Established Year</th>
                        <td>{{$companyData['established_in']}}</td>
                    </tr>
                    <tr>
                        <th>Turnover</th>
                        <td>{{$companyData['turnover']}}</td>
                    </tr>
                    <tr>
                        <th>Company Logo</th>
                        <td>
                            @if($companyData['company_logo'])
                                <img src={{asset('storage/images/'.$companyData['company_logo'])}} alt="" height="50px" width="100px">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Address Line 1</th>
                        <td>{{$companyData['address_line_1']}}</td>
                    </tr>
                    <tr>
                        <th>Address Line 2</th>
                        <td>{{$companyData['address_line_2']}}</td>
                    </tr>
                    <tr>
                        <th>City</th>
                        <td>{{$companyData['city']}}</td>
                    </tr>
                    <tr>
                        <th>State</th>
                        <td>
                            @if($companyData->state_data || $companyData['state_name'])
                                {{$companyData->state_data ? $companyData->state_data['name'] : $companyData['state_name']}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td>{{$companyData->country_data['name']}}</td>
                    </tr>
                    <tr>
                        <th>Product / Service</th>
                        <td>
                            @if($companyData['product_service'])
                                {{$companyData['product_service'] == 1 ? 'Product' : 'Service'}}
                            @endif
                        </td>
                    </tr>
                </table>
            </div>    
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <a href="{{ url('rkadmin/clients')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        </div>
    </div>
<!--end::Card-->
@stop

