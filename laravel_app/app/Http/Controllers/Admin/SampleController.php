<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\InexController;
use App\Http\Controllers\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Facades\Config;


class SampleController extends Controller {

    public  $param=array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            parent::login_user_details();
            return $next($request);
        });
    }

    public function list_sample(){
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.samples.list', $this->param);
    }
    public function list_sample_post(){
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $adjacents=5;
        $rows='25';
        $keyword='';

        /*if( (isset($_REQUEST['rows']))&&(!empty($_REQUEST['rows'])) ){
            $rows=$_REQUEST['rows'];
        }*/
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
                $UserTransaction->set_start_date($start_ts);
                $UserTransaction->set_end_date($end_ts);
            }
        }*/

        /*$all_count=$UserTransaction->count_all($keyword);
        $all_list=$UserTransaction->select_all($start,$end,$keyword,$sort_field,$sort_type);

        if( (isset($all_count[0]->count))&&(!empty($all_count[0]->count)) ){
            $record_count=$all_count[0]->count;
            $page=$record_count/$rows;
            $page=ceil($page);
        }
        $sr_start=0;
        if($record_count>=1){
            $sr_start=(($current_page-1)*$rows)+1;
        }
        $sr_end=($current_page)*$rows;
        if($record_count<=$sr_end){
            $sr_end=$record_count;
        }*/


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://retorra.ezofficeinventory.com/assets/filter.api?status=checked_out&page='.$current_page,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'token: '.Config::get('constant.EZ_OFFICE_INV_TOKEN')
            ),
        ));

        $response = curl_exec($curl);
        $response_arr = json_decode($response,1);
        curl_close($curl);

        $all_list = $response_arr['assets'];
        $page = $response_arr['total_pages'];
        $record_count = $page * $rows; //here from API we get 25 records per page

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
                fputcsv($file_for_download_data,array('#','User','Amount ($)','Stripe ID','Txn ID','Status','Add Date'));
                $i=$sr_start;
                foreach ($all_list as $single){
                    if($single->utran_add_date!=''){
                        $add_date = date($date_formate, $single->utran_add_date);
                    }else{
                        $add_date = '';
                    }
                    fputcsv($file_for_download_data,array(
                        $i,
                        $single->first_name.' '.$single->last_name,
                        $single->utran_amount,
                        $single->utran_stripe_id,
                        $single->utran_txn_id,
                        $single->utran_status,
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
            $html = view('backend.samples.list_post', $this->param)->render();

            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }
    public function list_sample_docs_post(){
        if( (isset($_REQUEST['sequence_num']))&&(!empty($_REQUEST['sequence_num'])) ){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://retorra.ezofficeinventory.com/assets/'.$_REQUEST['sequence_num'].'.api?show_document_urls=true',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'token: '.Config::get('constant.EZ_OFFICE_INV_TOKEN')
                ),
            ));

            $response = curl_exec($curl);
            $response_arr = json_decode($response,1);
            curl_close($curl);

            if(isset($response_arr['asset']['document_urls']) && !empty($response_arr['asset']['document_urls'])){
                $res['SUCCESS'] = 'TRUE';
                $res['MESSAGE'] = '';
                $res['DATA'] = $response_arr['asset']['document_urls'];
            }else{
                $res['SUCCESS'] = 'FALSE';
                $res['MESSAGE'] = 'No documents found';
            }
        }else{
            $res['SUCCESS'] = 'FALSE';
            $res['MESSAGE'] = 'Invalid request';
        }
        return json_encode($res,1);
    }
    
}

