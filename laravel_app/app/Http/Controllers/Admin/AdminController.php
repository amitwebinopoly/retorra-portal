<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\InexController;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Facades\Config;


class AdminController extends Controller {

    public  $param=array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            parent::login_user_details();
            return $next($request);
        });
    }

    public function list_admin(){
        $this->param['page_title']='Admin List';
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.admin.list', $this->param);
    }

    public function list_admin_post(){
        $User=new User();
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


        if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }

        $User->set_role('Admin');
        $all_count=$User->count_all($keyword);
        $all_list=$User->select_all($start,$end,$keyword,$sort_field,$sort_type);

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
            if(isset($all_list) && !empty($all_list)){
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
            echo json_encode($this->param,1);
        }else{
            $this->param['sr_start']=$sr_start;
            $this->param['all_list']=$all_list;
            $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
            $html = view('backend.admin.list_post', $this->param)->render();

            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }

    public function add_admin(){
        $this->param['page_title']='Add Admin';
        return view('backend.admin.add', $this->param);
    }
    public function add_admin_post(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'unique:users,mobile',
            'password' => 'required'
        ]);
        $User = new User();

        $User->set_first_name(trim(ucfirst($_POST['first_name'])));
        $User->set_last_name(trim(ucfirst($_POST['last_name'])));
        $User->set_user_name('');
        $User->set_mobile(trim($_POST['mobile']));
        if(!empty($_POST['mobile'])){
            $User->set_mobile_is_verified('Approved');
        }else{
            $User->set_mobile_is_verified('');
        }
        $User->set_email(trim($_POST['email']));
        if(!empty($_POST['email'])){
            $User->set_email_is_verified('Approved');
        }else{
            $User->set_email_is_verified('');   
        }
        $User->set_password(Hash::make($_POST['password']));

        $User->set_website('');
        $User->set_address('');
        $User->set_city('');
        $User->set_state_id(null);
        $User->set_zipcode('');
        $User->set_profile_picture('');

        $User->set_docid1('');
        $User->set_docid1_is_verified('');
        $User->set_docid2('');
        $User->set_docid2_is_verified('');
        $User->set_docid3('');
        $User->set_docid3_is_verified('');

        $User->set_reg_as('');
        $User->set_role('Admin');
        $User->set_status('Active');
        $User->set_add_date(time());
        $user_id = $User->insert_user();

        if(!empty($user_id)){
            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'Admin added successfully.');
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Error while adding admin.');
        }
        return Redirect::route('list_admin');
    }

    public function edit_admin($id){
        $User = new User();

        $User->set_id($id);
        $User->set_fields('*');
        $user_data = $User->select_fields_by_id();
        if(isset($user_data) && !empty($user_data) && $user_data[0]->role=='Admin'){
            $this->param['page_title'] = 'Edit Admin';
            $this->param['user_data'] = $user_data;
            return view('backend.admin.edit',$this->param);
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
            return redirect()->back();
        }
    }
    public function edit_admin_post(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$_POST['id'].',id',
            'mobile' => 'unique:users,mobile,'.$_POST['id'].',id'
        ]);
        $User = new User();

        $User->set_id($_POST['id']);
        $User->set_first_name(ucfirst(trim($_POST['first_name'])));
        $User->set_last_name(ucfirst(trim($_POST['last_name'])));
        $User->set_mobile(trim($_POST['mobile']));
        $User->set_email(trim($_POST['email']));
        $User->set_modify_date(time());
        $update_admin = $User->update_admin();

        if(!empty($update_admin)){
            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'Admin updated successfully.');
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Error while updating admin.');
        }
        return Redirect::route('list_admin');
    }

}

