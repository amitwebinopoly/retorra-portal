<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Retorra - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('asset_be/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('asset_be/css/sb-admin-2.min.css')}}" rel="stylesheet">

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('asset_be/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('asset_be/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <link rel="stylesheet" type="text/css" href="{{asset('asset_be/custom/bootstrap-toastr/toastr.min.css')}}"/>
    <script src="{{asset('asset_be/custom/bootstrap-toastr/toastr.min.js')}}"></script>

</head>

<body class="bg-gradient-primary">

@include('backend.layout.error_msg_layout')

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-4 d-none d-lg-block bg-login-image" style="background:url({{asset('asset_be/custom/images/login-img.webp')}}); background-size: contain; background-repeat: no-repeat;"></div>
                        <div class="col-lg-8">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Retorra - Login</h1>
                                </div>
                                <form class="user" action="{{route('admin_login_post')}}" method="post" onsubmit="return check_valid();">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user"
                                               name="login_email_mob" id="login_email_mob" aria-describedby="emailHelp"
                                               placeholder="Enter Email Address...">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user"
                                               name="login_password" id="login_password" placeholder="Enter Password...">
                                    </div>
                                    {{--<div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember
                                                Me</label>
                                        </div>
                                    </div>--}}
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                    {{--<hr>
                                    <a href="index.html" class="btn btn-google btn-user btn-block">
                                        <i class="fab fa-google fa-fw"></i> Login with Google
                                    </a>
                                    <a href="index.html" class="btn btn-facebook btn-user btn-block">
                                        <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                                    </a>--}}
                                </form>
                                {{--<hr>
                                <div class="text-center">
                                    <a class="small" href="forgot-password.html">Forgot Password?</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="register.html">Create an Account!</a>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Core plugin JavaScript-->
<script src="{{asset('asset_be/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('asset_be/js/sb-admin-2.min.js')}}"></script>

<script>
    function check_valid(){
        var email = $("#login_email_mob").val().trim();
        var password = $("#login_password").val().trim();
        var flag = 0;

        if(password==''){
            flag++;
            toastr.error('Password is required');
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
</script>

</body>

</html>