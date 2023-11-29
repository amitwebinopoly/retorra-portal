@extends('backend.layout.basic')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Add Admin</h1>
        <p class="mb-4"></p>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Add Admin</h6>
            </div>
            <div class="card-body">
                <form class="form-horizontal" role="form" action="{{route('add_user_post')}}" method="post" onsubmit="return check_valid();">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="role" value="Admin">
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{old('first_name')}}">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="control-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{old('last_name')}}">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="control-label">Email *</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{old('email')}}">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="control-label">Password *</label>
                            <input type="text" class="form-control" name="password" id="password" value="{{old('password')}}">
                        </div>
                    </div>
                    <div class="form-actions" align="center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

<script>
    function check_valid(){
        var first_name = $("#first_name").val().trim();
        var email = $("#email").val().trim();
        var password = $("#password").val().trim();
        var flag = 0;

        if(first_name==''){
            flag++;
            toastr.error('First Name is required');
        }
        if(email==''){
            flag++;
            toastr.error('Email is required');
        }
        if(password==''){
            flag++;
            toastr.error('Password is required');
        }

        if(flag==0){
            return true;
        }else{
            return false;
        }
    }

</script>
@endsection