@extends('backend.layout.basic')

@section('content')
    <link href="{{asset('asset_be/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">

    <div class="container-fluid">

        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-2 text-gray-800">Samples</h1>
            <p class="mb-4"></p>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Samples</h6>
                </div>
                <div class="card-body">
                    <div id="master_div_pagination" class="dataTables_wrapper"></div>
                </div>
            </div>

        </div>


    </div>

    <div class="modal fade" id="modal_documents" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Documents</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal_document_body"></div>
                </div>
                {{--<div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>--}}
            </div>
        </div>
    </div>

    <script src="{{asset('asset_be/custom/js/inex_datatable.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $("#master_div_pagination").InexDataTable(
                    '<?php echo route('list_sample_post'); ?>',  // ajax url
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

        $(document).on('click','.show_tr_data',function(){
            let _this = $(this);
            let sr = _this.data('sr');

            $("#tr_data_"+sr).toggle();
            _this.find('i').toggleClass('fa-angle-down').toggleClass('fa-angle-up');
        });

        $(document).on('click','.modal_tr_data',function(){
            let _this = $(this);
            let sequence_num = _this.data('sequence_num');

            $("#modal_documents").modal('show');
            $("#modal_document_body").html('<div class="text-center">Processing...</div>');

            $.ajax({
                url: "<?php echo route('list_sample_docs_post'); ?>",
                type: "post",
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'sequence_num': sequence_num
                },
                success: function(obj){
                    let obj_arr = JSON.parse(obj);
                    if(obj_arr.SUCCESS == 'TRUE'){
                        let html = '';

                        html += '<div class="row">';
                        $(obj_arr.DATA).each(function(k,v){
                            html += '<div class="col-xl-4" style="height:150px;">';
                            html += '<a href="'+v+'" target="_blank"><img src="'+v+'" style="width:100%;height:100%;"></a>';
                            html += '</div>';
                        });
                        html += '</div>';

                        $("#modal_document_body").html(html);
                    }else{
                        $("#modal_document_body").html('<div class="text-center">'+obj_arr.MESSAGE+'</div>');
                    }
                }
            });

        });


    </script>
@endsection