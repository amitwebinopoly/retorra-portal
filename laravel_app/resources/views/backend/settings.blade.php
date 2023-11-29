@extends('backend.layout.basic')

@section('content')
	<div class="container-fluid">

		<!-- Page Heading -->
		<div class="d-sm-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0 text-gray-800">Settings</h1>
			{{--<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
				<i class="fas fa-download fa-sm text-white-50"></i> Generate Report
			</a>--}}
		</div>

		<!-- Content Row -->
		<form class="form" method="post" action="{{route('settings_post')}}" onsubmit="">
			@csrf
			<div class="row">
				<div class="col-lg-12">
					<div class="card mb-4">
						<div class="card-header">
							Quote format<br>
							<small>Customize your quote number format by changing the prefix or adding a suffix. Changes are applied to future quotes.</small>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-lg-6">
									<label>Prefix</label>
									<input type="text" class="form-control" name="quote_no_prefix" id="quote_no_prefix" value="{{$QUOTE_NO_PREFIX}}">
								</div>
								<div class="col-lg-6">
									<label>Postfix</label>
									<input type="text" class="form-control" name="quote_no_postfix" id="quote_no_postfix" value="{{$QUOTE_NO_POSTFIX}}">
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									Your quote number will appear as <span class="show_prefix">{{$QUOTE_NO_PREFIX}}</span>1001<span class="show_postfix">{{$QUOTE_NO_POSTFIX}}</span>, <span class="show_prefix">{{$QUOTE_NO_PREFIX}}</span>1002<span class="show_postfix">{{$QUOTE_NO_POSTFIX}}</span>, <span class="show_prefix">{{$QUOTE_NO_PREFIX}}</span>1003<span class="show_postfix">{{$QUOTE_NO_POSTFIX}}</span> ...
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 text-center">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</div>
		</form>

	</div>

	<script>
		$("#quote_no_prefix").keyup(function(){
			$(".show_prefix").html($("#quote_no_prefix").val());
		});
		$("#quote_no_postfix").keyup(function(){
			$(".show_postfix").html($("#quote_no_postfix").val());
		});
	</script>
@endsection