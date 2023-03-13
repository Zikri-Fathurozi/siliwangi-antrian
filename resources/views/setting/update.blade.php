@extends('layouts.app')

@section('content')
<div class="page">
	<div class="page-main">
		<div class="my-3 my-md-12">
			<div class="container">
				<div class="card">
					<div class="card-body">
						<div class="text-wrap p-lg-6">
							<h2 class="mt-0 mb-4"><i class="fa fa-refresh"></i> Refresh Browser</h2>
							Terapkan perubahan pada semua browser aktif.
							<setting-update-component></setting-update-component>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
