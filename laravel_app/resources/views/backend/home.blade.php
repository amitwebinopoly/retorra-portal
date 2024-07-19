@extends('backend.layout.basic')

@section('content')
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
			{{--<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
				<i class="fas fa-download fa-sm text-white-50"></i> Generate Report
			</a>--}}
		</div>

		<!-- Content Row -->
		<div class="row">

			<!-- Earnings (Monthly) Card Example -->
			@if(isset($count_quote))
			<div class="col-xl-3 col-md-6 mb-4">
				<div class="card border-left-primary shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
									Quotes</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">{{$count_quote}}</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-chart-area fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

			<!-- Earnings (Monthly) Card Example -->
			@if(isset($count_order))
			<div class="col-xl-3 col-md-6 mb-4">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-success text-uppercase mb-1">
									Orders</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">{{$count_order}}</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-list fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

		</div>

		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0 text-gray-800">Users</h1>
			{{--<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
				<i class="fas fa-download fa-sm text-white-50"></i> Generate Report
			</a>--}}
		</div>

		<div class="row">

			<!-- Earnings (Monthly) Card Example -->
			@if(isset($count_admin))
			<div class="col-xl-3 col-md-6 mb-4">
				<div class="card border-left-info shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Admins</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">{{$count_admin}}</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-user-secret fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

			<!-- Pending Requests Card Example -->
			@if(isset($count_designer))
			<div class="col-xl-3 col-md-6 mb-4">
				<div class="card border-left-warning shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Designers</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">{{$count_designer}}</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-user-graduate fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

			@if(isset($count_showroom))
			<div class="col-xl-3 col-md-6 mb-4">
				<div class="card border-left-success shadow h-100 py-2">
					<div class="card-body">
						<div class="row no-gutters align-items-center">
							<div class="col mr-2">
								<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Showrooms</div>
								<div class="h5 mb-0 font-weight-bold text-gray-800">{{$count_showroom}}</div>
							</div>
							<div class="col-auto">
								<i class="fas fa-users fa-2x text-gray-300"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

		</div>

	</div>
@endsection