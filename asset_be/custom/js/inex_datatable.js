function call_list() {
    var filter_obj = {};
    $($filter_ids_arr).each(function (k,v) {
        filter_obj[v] = $("#"+v).val();
    });

    var page = $("#pagination_page_no").val();
    var rows = $("#pagination_no_of_rows").val();
    var keyword = $("#pagination_search_keyword").val();
    var sort_field = $("#pagination_sort_field").val();
    var sort_type = $("#pagination_sort_type").val();
    var pagination_export = $("#pagination_export").val();

    var postdata = {
        'sort_field':sort_field,'sort_type':sort_type,'pagination_export':pagination_export,
        'current_page':page, 'rows':rows, 'keyword':keyword, '_token': $csrf_token
    };
    $.extend( postdata, filter_obj );   //combine default data and filter data

    if($function_before_ajax!=''){
        //window[$function_before_ajax]();
        $function_before_ajax();
    }

    $.ajax({
        type: 'post',
        data: postdata,
        url: $url,
        success: function (data) {
            if(pagination_export=='Y'){
                $("#pagination_export").val('N');
                $data_obj= $.parseJSON(data);
                if($data_obj['SUCCESS']=='TRUE'){
                    window.location.href = $data_obj['file_full_url'];
                }else{
                    alert('There is no record for download.');
                }
            }else{
                $data_obj= $.parseJSON(data);
                $("#pagination_main").html($data_obj['DATA']);
                set_sort_sign();
                set_pagenation_button($data_obj['page_count'],$data_obj['record_count'],$data_obj['sr_start'],$data_obj['sr_end']);
            }

            if($function_after_ajax!=''){
                //window[$function_after_ajax]();
                $function_after_ajax();
            }
        }
    });
}
function set_sort_sign(){
    // this function set sorting icon in table header fields
    var sort_field = $("#pagination_sort_field").val();
    var sort_type = $("#pagination_sort_type").val();
    var sort_asc_class = 'fa-sort-asc';
    var sort_desc_class = 'fa-sort-desc';
    var sort_unsorted_class = 'fa-unsorted';

    $(".sort_th").each(function () {
        var _this = $(this);
        if(_this.find('i').length==0){
            _this.append('<i class="fa '+sort_unsorted_class+'  pull-right"></i>');
        }
        if(_this.data('sort_field')==sort_field){
            if(sort_type=='ASC'){
                _this.find('i').addClass(sort_asc_class).removeClass(sort_unsorted_class).removeClass(sort_desc_class);
            }else{
                _this.find('i').addClass(sort_desc_class).removeClass(sort_asc_class).removeClass(sort_unsorted_class);
            }
        }else{
            _this.find('i').addClass(sort_unsorted_class).removeClass(sort_asc_class).removeClass(sort_desc_class);
        }
    });
}
function set_pagenation_button(total_page,record_count,sr_start,sr_end){
    var current_page = parseInt($("#pagination_page_no").val());
    var adjacents = 5;

    var out = '';

    out += '<div><span>Showing '+sr_start+' To '+sr_end+' of '+record_count+' Records</span></div>';
    out += '<ul class="pagination">';

    // previous
    if(current_page==1) {
        out+='<li class="paginate_button page-item previous disabled" aria-disabled="true"><a class="page-link" href="javascript:;">Prev</a></li>';
    } else if(current_page==2){
        out+='<li class="paginate_button page-item previous"><a class="page-link" data-page="1" href="javascript:;">Prev</a></li>';
    } else {
        out+='<li class="paginate_button page-item previous"><a class="page-link" data-page="'+(current_page-1)+'" href="javascript:;">Prev</a></li>';
    }

    // first
    /*if(current_page>(adjacents+1)) {
        out+='<li><a class="button" data-page="1" data-current_page="1" href="javascript:;">1</a></li>';
    }

    // interval
    if(current_page>(adjacents+2)) {
        out+='<li class="disabled"><a class="button" href="javascript:;">...</a></li>';
    }

    // pages
    $pmin = (current_page>adjacents) ? (current_page-adjacents) : 1;
    $pmax = (current_page<(total_page-adjacents)) ? (current_page+adjacents) : total_page;
    for($i=$pmin; $i<=$pmax; $i++) {
        if($i==current_page) {
            out+='<li class="active"><a class="button" data-current_page="'+$i+'" href="javascript:;">'+$i+'</a></li>';
        }else if($i==1){
            out+='<li><a class="button" data-page="'+$i+'" href="javascript:;">'+$i+'</a></li>';
        } else {
            out+='<li><a class="button" data-page="'+$i+'" href="javascript:;">'+$i+'</a></li>';
        }
    }

    // interval
    if(current_page<(total_page-adjacents-1)) {
        out+='<li class="disabled"><a class="button" href="javascript:;">...</a></li>';
    }

    // last
    if(current_page<(total_page-adjacents)) {
        out+='<li><a class="button" data-page="'+total_page+'" href="javascript:;">'+total_page+'</a></li>';
    }*/

    // next
    if(current_page<total_page) {
        out+='<li class="paginate_button page-item next"><a class="page-link" data-page="'+(current_page+1)+'" href="javascript:;">Next</a></li>';
    } else {
        out+='<li class="paginate_button page-item next disabled" aria-disabled="true"><a class="page-link" href="javascript:;">Next</a></li>';
    }

    out+= '</ul>';

    $("#pagination_footer_buttons").html(out);
}
$.fn.InexDataTable = function(url,csrf_token,filter_ids_arr,function_before_ajax,function_after_ajax) {
    var page_arr = ['10','50','100','200','500'];
    var _this = this;   //main div id
    _this.prepend('<div class="col-xs-12 dataTables_paginate" id="pagination_footer_buttons" align="center"></div>');        //this div is for table data
    _this.prepend('<div class="col-xs-12" id="pagination_main"></div>');        //this div is for table data
    _this.prepend('<div class="col-xs-12" id="pagination_data_div"></div>');    //this div is for hidden-element, search-box, select-row
    var html = '';

    html += '<input type="hidden" id="pagination_sort_field">';
    html += '<input type="hidden" id="pagination_sort_type" value="DESC">';
    html += '<input type="hidden" id="pagination_page_no" value="1">';
    html += '<input type="hidden" id="pagination_export" value="N">';
    html += '<div class="pull-left" style="display:none;">';
    html += '<select id="pagination_no_of_rows" class="form-control" style="width:80px;">';
    $(page_arr).each(function (k,v) {
        if(k==0){
            html += '<option value="'+v+'" selected>'+v+'</option>';
        }else{
            html += '<option value="'+v+'">'+v+'</option>';
        }
    });
    html += '</select>';
    html += '</div>';

    html += '<div class="pull-right pagination_section_search_keyword">';
    html += '<div class="form-group">';
    html += '<input class="form-control" type="text" placeholder="Search" id="pagination_search_keyword" style="width:200px;">';
    html += '</div>';
    html += '</div>';

    $("#pagination_data_div").html(html);

    $url = url;
    $csrf_token = csrf_token;
    $filter_ids_arr = filter_ids_arr;
    $function_before_ajax = function_before_ajax;
    $function_after_ajax = function_after_ajax;

    call_list();
    $("#pagination_no_of_rows").change(function(){
        $("#pagination_page_no").val('1');
        call_list();
    });
    $("#pagination_search_keyword").focusout(function(){
        $("#pagination_page_no").val('1');
        call_list();
    });
    $("#pagination_search_keyword").keyup(function(e){
        if(e.keyCode==13){
            $("#pagination_search_keyword").focusout().blur();
        }
    });
    $(document).on('click','#pagination_footer_buttons a', function () {
        var page_no=$(this).data('page');
        $("#pagination_page_no").val(page_no);
        if(page_no>=1){
            call_list();
        }
    });
    $(document).on('click','.sort_th', function () {
        var sort_field = $(this).data('sort_field');
        $("#pagination_sort_field").val(sort_field);

        var pagination_sort_type = $("#pagination_sort_type").val();
        if(pagination_sort_type=='' || pagination_sort_type=='ASC'){
            pagination_sort_type = 'DESC';
        }else{
            pagination_sort_type = 'ASC';
        }
        $("#pagination_sort_type").val(pagination_sort_type);
        call_list();
    });
    $("#download_csv_btn").click(function () {
        $("#pagination_export").val('Y');
        call_list();
    });
};