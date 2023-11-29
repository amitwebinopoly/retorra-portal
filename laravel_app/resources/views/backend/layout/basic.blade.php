<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Retorra Admin</title>

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

    <style>
        .pagination{ justify-content: center !important;}
    </style>

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    @include('backend.layout.error_msg_layout')

    <!-- Sidebar -->
    @include('backend.layout.sidebar')
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('backend.layout.header')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            @yield('content')
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        @include('backend.layout.footer')
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Core plugin JavaScript-->
<script src="{{asset('asset_be/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('asset_be/js/sb-admin-2.min.js')}}"></script>

</body>

</html>