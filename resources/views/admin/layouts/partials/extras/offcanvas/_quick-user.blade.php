@php
	$direction = config('layout.extras.user.offcanvas.direction', 'right');
@endphp
 {{-- User Panel --}}
<div id="kt_quick_user" class="offcanvas offcanvas-{{ $direction }} p-10">
	{{-- Header --}}
	<div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
		<h3 class="font-weight-bold m-0">
			User Profile
		</h3>
		<a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
			<i class="ki ki-close icon-xs text-muted"></i>
		</a>
	</div>

	{{-- Content --}}
    <div class="offcanvas-content pr-5 mr-n5">
		{{-- Header --}}
        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-100 mr-5">
                <div class="symbol-label" style="background-image:url('{{ asset('storage/images/'.auth()->guard('admin')->user()->picture) }}')"></div>
				<i class="symbol-badge bg-success"></i>
            </div>
            <div class="d-flex flex-column">
                <a href="{{ route('admin.profile')}}" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">
					{{ auth()->guard('admin')->user()->name }}
				</a>
                <div class="text-muted mt-1 d-none1">
                    {{ auth()->guard('admin')->user()->designation }}
                </div>
                <div class="navi mt-2">
                    <a href="{{ route('admin.profile')}}" class="navi-item">
                        <span class="navi-link p-0 pb-2">
                            <span class="navi-icon">
                                <i class="flaticon-multimedia-2 text-primary ml-1"></i>
							</span>
                            <span class="navi-text text-muted text-hover-primary">{{ auth()->guard('admin')->user()->email }}</span>
                        </span>
                    </a>
					<a href="{{ route('admin.logout')}}" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">Sign Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
