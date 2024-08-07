@extends('backend.layout.basic')

@section('content')
<link href="{{asset('asset_be/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">

<div class="container-fluid">

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Users</h1>
            <div>
                @if($user_param['role']=='Admin')
                <a href="{{route('add_user')}}" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm">Add New Admin</a>
                <a href="javascript:;" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="sync_customer_qb_btn">Sync Customers from Quickbook</a>
                @endif
            </div>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Users</h6>
            </div>
            <div class="card-body">
                <div id="master_div_pagination" class="dataTables_wrapper"></div>
            </div>
        </div>

    </div>


</div>

<script src="{{asset('asset_be/custom/js/inex_datatable.js')}}" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $("#master_div_pagination").InexDataTable(
            '<?php echo route('list_user_post'); ?>',  // ajax url
            '<?php echo csrf_token(); ?>',              // csrf_token only in laravel. set blank in core
            [],
            function (){
                //function call before ajax call

            },
            function (){
                //function call after ajax responce

            }
        );
    });

    /*$("#filter_search_btn").click(function () {
     $("#pagination_page_no").val('1');
     call_list();
     });
     $("#filter_clear_btn").click(function () {
     $('#date_range_filter').val('');
     $('#is_read_filter').val('').trigger('change');
     $("#pagination_page_no").val('1');
     call_list();
     });*/

    $("#sync_customer_qb_btn").click(async function(){
        let original_text = $("#sync_customer_qb_btn").html();
        $("#sync_customer_qb_btn").html('Processing...').attr('disabled','true');

        const rawResponse = await fetch('{{route('sync_qb_customers')}}', {
            method: 'GET'
        });
        const obj = await rawResponse.json();

        $("#sync_customer_qb_btn").html(original_text).removeAttr('disabled');
        if(obj.SUCCESS == 'TRUE'){
            toastr.success(obj.MESSAGE);
        }else{
            toastr.error(obj.MESSAGE);
        }
    });


</script>
@endsection