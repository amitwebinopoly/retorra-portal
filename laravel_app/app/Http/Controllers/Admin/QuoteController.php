<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\InexController;
use App\Models\Quotes;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;


class QuoteController extends Controller {

    public  $param=array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            parent::login_user_details();
            return $next($request);
        });
    }

    public function list_quote(){
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.quotes.list', $this->param);
    }
    public function list_quote_post(){
        $Quotes = new Quotes();
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $rows='10';
        $keyword='';

        if( (isset($_REQUEST['rows']))&&(!empty($_REQUEST['rows'])) ){
            $rows=$_REQUEST['rows'];
        }
        if( (isset($_REQUEST['keyword']))&&(!empty($_REQUEST['keyword'])) ){
            $keyword=$InexController->allow_special_character_in_keyword($_REQUEST['keyword']);
        }
        if( (isset($_REQUEST['current_page']))&&(!empty($_REQUEST['current_page'])) ){
            $current_page=$_REQUEST['current_page'];
        }
        $start=($current_page-1)*$rows;
        $end=$rows;
        $sort_field = '';
        if(isset($_POST['sort_field']) && !empty($_POST['sort_field'])){
            $sort_field = $_POST['sort_field'];
        }
        $this->param['sort_field']=$sort_field;
        $sort_type = '';
        if(isset($_POST['sort_type']) && !empty($_POST['sort_type'])){
            $sort_type = $_POST['sort_type'];
        }
        $this->param['sort_type']=$sort_type;


        /*if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }*/

        $all_count = $Quotes->count_all($keyword);
        $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);

        if( (isset($all_count[0]->count))&&(!empty($all_count[0]->count)) ){
            $record_count=$all_count[0]->count;
            $page=$record_count/$rows;
            $page=ceil($page);
        }
        $keyword=$InexController->remove_special_character_in_keyword($keyword);
        $sr_start=0;
        if($record_count>=1){
            $sr_start=(($current_page-1)*$rows)+1;
        }
        $sr_end=($current_page)*$rows;
        if($record_count<=$sr_end){
            $sr_end=$record_count;
        }

        if(isset($_POST['pagination_export']) && $_POST['pagination_export']=='Y'){
            /*if(isset($all_list) && !empty($all_list)){
                $date_formate=Config::get('constant.DATE_FORMATE');
                $file_full_path = public_path().Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv";
                $file_full_url = asset(Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv");
                $file_for_download_data = fopen($file_full_path,"w");
                fputcsv($file_for_download_data,array('#','Name','Email','Mobile','Add Date'));
                $i=$sr_start;
                foreach ($all_list as $single){
                    if($single->add_date!=''){
                        $add_date = date($date_formate, $single->add_date);
                    }else{
                        $add_date = '';
                    }
                    fputcsv($file_for_download_data,array(
                        $i,
                        $single->first_name.' '.$single->last_name,
                        $single->email,
                        $single->mobile,
                        $add_date
                    ));
                    $i++;
                }
                fclose($file_for_download_data);
                $this->param['SUCCESS']='TRUE';
                $this->param['file_full_url']=$file_full_url;
            }else{
                $this->param['SUCCESS']='FALSE';
            }
            echo json_encode($this->param,1);*/
        }else{
            $this->param['sr_start']=$sr_start;
            $this->param['all_list']=$all_list;
            $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
            $html = view('backend.quotes.list_post', $this->param)->render();

            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }

    public function api_add_quote(Request $request){
        $Quotes = new Quotes();
        $Settings = new Settings();

        $validator = Validator::make($request->all(), [
            'shape' => 'required',
            'shopify_customer_id' => 'required',
            'shopify_customer_email' => 'required',
            'shopify_customer_name' => 'required'
        ]);
        if ($validator->fails()) {
            $res['SUCCESS'] = 'FALSE';
            $res['MESSAGE'] = $validator->errors()->getMessages();
            echo json_encode($res,1);exit;
        }

        $qt_pre_exist = $Settings->select_field_by_key('QUOTE_NO_PREFIX');
        $qt_post_exist = $Settings->select_field_by_key('QUOTE_NO_POSTFIX');
        $max_quote_id = $Quotes->get_max_id();
        if(!empty($max_quote_id)){
            $quote_number = @$qt_pre_exist[0]->value.(($max_quote_id+1)+1000).@$qt_post_exist[0]->value;
        }else{
            $quote_number = @$qt_pre_exist[0]->value.(1+1000).@$qt_post_exist[0]->value;
        }

        $postData = $request->post();
        $insert_arr = [
            'quote_number' => $quote_number,
            'shape' => $postData['shape'],
            'ars_pom_color_1' => $postData['ars_pom_color_1'],
            'ars_pom_color_2' => $postData['ars_pom_color_2'],
            'ars_pom_color_3' => $postData['ars_pom_color_3'],
            'width_feet' => $postData['width_feet'],
            'width_inch' => $postData['width_inch'],
            'length_feet' => $postData['length_feet'],
            'length_inch' => $postData['length_inch'],
            'shopify_customer_id' => $postData['shopify_customer_id'],
            'shopify_customer_email' => $postData['shopify_customer_email'],
            'shopify_customer_name' => $postData['shopify_customer_name'],
            'status' => 'Draft',
            'add_date' => time()
        ];
        $insert_id = $Quotes->insert_quote($insert_arr);

        $res['SUCCESS'] = 'TRUE';
        $res['MESSAGE'] = "";
        $res['insert_id'] = $insert_id;
        echo json_encode($res,1);
    }
    public function api_get_quote_list($customer_id){
        $Quotes = new Quotes();
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $rows='10';
        $keyword='';

        if( (isset($_REQUEST['rows']))&&(!empty($_REQUEST['rows'])) ){
            $rows=$_REQUEST['rows'];
        }
        if( (isset($_REQUEST['keyword']))&&(!empty($_REQUEST['keyword'])) ){
            $keyword=$InexController->allow_special_character_in_keyword($_REQUEST['keyword']);
        }
        if( (isset($_REQUEST['page']))&&(!empty($_REQUEST['page'])) ){
            $current_page=$_REQUEST['page'];
        }
        $start=($current_page-1)*$rows;
        $end=$rows;
        $sort_field = '';
        if(isset($_POST['sort_field']) && !empty($_POST['sort_field'])){
            $sort_field = $_POST['sort_field'];
        }
        $this->param['sort_field']=$sort_field;
        $sort_type = '';
        if(isset($_POST['sort_type']) && !empty($_POST['sort_type'])){
            $sort_type = $_POST['sort_type'];
        }
        $this->param['sort_type']=$sort_type;


        /*if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }*/

        $Quotes->set_shopify_customer_id($customer_id);
        $all_count = $Quotes->count_all($keyword);
        $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);

        if( (isset($all_count[0]->count))&&(!empty($all_count[0]->count)) ){
            $record_count=$all_count[0]->count;
            $page=$record_count/$rows;
            $page=ceil($page);
        }
        $keyword=$InexController->remove_special_character_in_keyword($keyword);
        $sr_start=0;
        if($record_count>=1){
            $sr_start=(($current_page-1)*$rows)+1;
        }
        $sr_end=($current_page)*$rows;
        if($record_count<=$sr_end){
            $sr_end=$record_count;
        }

        $res['DATA'] = $all_list;
        $res['page_count'] = $page;
        $res['record_count']=$record_count;
        $res['sr_start']=$sr_start;
        $res['sr_end']=$sr_end;
        return json_encode($res,1);
    }

}

