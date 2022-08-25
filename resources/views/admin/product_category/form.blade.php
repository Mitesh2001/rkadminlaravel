    <div class="form-group row">
        <div class="col-lg-6">
            {!! Form::label('name', __('Category Name'), ['class' => '']) !!}
            <span class="text-danger">*</span>
            {!! 
                Form::text('name',  
                isset($productCategory['name']) ? $productCategory['name'] : null, 
                ['class' => 'form-control special-characters','required', 'pattern' => '^[a-zA-Z][a-zA-Z0-9-_.\/d$@$!<>()|%*?&. ]{0,50}$', 'title' => 'Category name must contain alphabetic value. e.g. Category 123']) 
            !!}
            <span class="form-text text-muted">Please enter category name. e.g. Category 123</span>
        </div>
        <div class="col-lg-6">
            {!! Form::label('name', __('Category Description'), ['class' => '']) !!}
            {!! 
                Form::textarea('category_description',  
                isset($productCategory['category_description']) ? $productCategory['category_description'] : null, 
                ['class' => 'form-control','rows'=>'5']) 
            !!}
            <span class="form-text text-muted">Please enter category description</span>
        </div>
        @php
            $cId = !empty($cId) ? $cId : (isset($productCategory->company_id) ? $productCategory->company_id : '');
            $submitButtonText = !empty($id) ? 'Update' : 'Create New';
        @endphp
        {{Form::hidden('id',!empty($id) ? $id : null)}}
        {{Form::hidden('company_id',!empty($cId) ? $cId : null)}}
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-lg-12">
                {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitproduct']) !!}
				@php
				if($cId)
					$url = route('admin.category.index',encrypt($cId));
				else
					$url = route('admin.master.category.index');
				@endphp
                <a href="{{$url}}" class="btn btn-md btn-primary ml-2">Cancel</a>
            </div>
        </div>
    </div>
@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery(".form").validate();
jQuery(".ui-form").validate();
});
</script>
@endsection
