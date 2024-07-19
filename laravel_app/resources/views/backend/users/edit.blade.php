@extends('backend.layout.basic')

@section('content')

    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Edit {{$user_data[0]->role}}</h1>
        <p class="mb-4"></p>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit {{$user_data[0]->role}}</h6>
            </div>
            <div class="card-body">
                <form class="form-horizontal" role="form" action="{{route('edit_user_post')}}" method="post" onsubmit="return check_valid();">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" value="{{$user_data[0]->id}}">
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label">First Name *</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{$user_data[0]->first_name}}">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="control-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{$user_data[0]->last_name}}">
                        </div>
                        <div class="col-lg-12 form-group">
                            <label class="control-label">Email *</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{$user_data[0]->email}}">
                        </div>
                    </div>
                    <div class="form-actions" align="center">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        @if($user_data[0]->id == $user_param['id'])
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
            </div>
            <div class="card-body">
                <form class="form-horizontal" role="form" action="{{route('edit_user_password_post')}}" method="post" onsubmit="return check_password_valid();">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="id" value="{{$user_data[0]->id}}">
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="control-label">Current password *</label>
                            <input type="password" class="form-control" name="old_password" id="old_password" autocomplete="off">
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="control-label">New Password *</label>
                            <input type="password" class="form-control" name="new_password" id="new_password" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-actions" align="center">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>

<script>
    function check_valid(){
        var first_name = $("#first_name").val().trim();
        var email = $("#email").val().trim();
        var flag = 0;

        if(first_name==''){
            flag++;
            toastr.error('First Name is required');
        }
        if(email==''){
            flag++;
            toastr.error('Email is required');
        }

        if(flag==0){
            return true;
        }else{
            return false;
        }
    }
    function check_password_valid(){
        var old_password = $("#old_password").val().trim();
        var new_password = $("#new_password").val().trim();
        var flag = 0;

        if(old_password==''){
            flag++;
            toastr.error('Old password is required');
        }
        if(new_password==''){
            flag++;
            toastr.error('New password is required');
        }

        if(flag==0){
            return true;
        }else{
            return false;
        }
    }

</script>
@endsection