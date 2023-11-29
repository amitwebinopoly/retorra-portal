@extends('backend.layout.basic')

@section('content')
    <link href="{{asset('asset_be/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">

    <div class="container-fluid">

        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">Quotes</h1>
            <p class="mb-4"></p>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quotes</h6>
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
                    '<?php echo route('list_quote_post'); ?>',  // ajax url
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


    </script>
@endsection