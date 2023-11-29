<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\InexController;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Facades\Config;


class UsersController extends Controller {

    public  $param=array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            parent::login_user_details();
            return $next($request);
        });
    }

    public function list_user(){
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.users.list', $this->param);
    }
    public function list_user_post(){
        $User=new User();
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $rows='12';
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

        /*if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }*/


        if( (isset($_REQUEST['role']))&&(!empty($_REQUEST['role'])) ){
            $User->set_role($_REQUEST['role']);
        }

        $all_count=$User->count_all($keyword);
        $all_list=$User->select_all($start,$end,$keyword);

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
        }

        if(isset($_POST['pagination_export']) && $_POST['pagination_export']=='Y'){
            /*if(isset($all_list) && !empty($all_list)){
                $date_formate=Config::get('constant.DATE_FORMATE');
                $file_full_path = public_path().Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv";
                $file_full_url = asset(Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv");
                $file_for_download_data = fopen($file_full_path,"w");
                fputcsv($file_for_download_data,array('#','Name','Email','Mobile','Reg.As','Add Date'));
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
                        $single->reg_as,
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
            $html = view('backend.users.list_post', $this->param)->render();
            
            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }

    /*public function create_unique_username($id){
        $User = new User();
        create_again:
        $user_name = time();
        if(!empty($id)){
            $User->set_id($id);
            $User->set_fields(['first_name']);
            $user_data = $User->select_fields_by_id();
            if(!empty($user_data)){
                $user_name = str_replace(' ','',strtolower($user_data[0]->first_name)).rand(1000,9999);
            }
        }

        $User->set_user_name($user_name);
        $User->set_fields('id');
        $username_exist = $User->select_fields_by_username();
        if(!empty($username_exist)){
            goto create_again;
        }

        return $user_name;
    }
    public function add_user(){
        $State = new State();
        $state_list = $State->select_for_dropdown_by_user_status();
        $this->param['state_list']=$state_list;

        $this->param['page_title']='Add User';
        return view('backend.user.add', $this->param);
    }
    public function add_user_post(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'unique:users,mobile',
            'password' => 'required'
        ]);
        $User = new User();
        $SubscribeController = new SubscribeController();

        $website = trim($_POST['website']);
        if(!empty($website)){
            if(strpos($website,'http') === false){
                $website = 'http://'.$website;
            }
        }
        $reg_as = '';
        if(isset($_POST['reg_as']) && !empty($_POST['reg_as'])){
            $reg_as = $_POST['reg_as'];
        }
        $state_id = null;
        if(isset($_POST['state_id']) && !empty($_POST['state_id'])){
            $state_id = $_POST['state_id'];
        }

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

        $User->set_website($website);
        $User->set_address(trim($_POST['address']));
        $User->set_city(trim($_POST['city']));
        $User->set_state_id($state_id);
        $User->set_zipcode(trim($_POST['zipcode']));
        $User->set_profile_picture('');

        $User->set_docid1('');
        $User->set_docid1_is_verified('');
        $User->set_docid2('');
        $User->set_docid2_is_verified('');
        $User->set_docid3('');
        $User->set_docid3_is_verified('');

        $User->set_reg_as($reg_as);
        $User->set_role('User');
        $User->set_status('Active');
        $User->set_add_date(time());
        $user_id = $User->insert_user();

        if(!empty($user_id)){
            $user_name = $this->create_unique_username($user_id);
            $User->set_id($user_id);
            $User->set_user_name($user_name);
            $User->change_user_name();

            if(!empty($_POST['email'])){
                $SubscribeController->subscribe_email($_POST['email']);
            }
            if(!empty($_POST['mobile'])){
                $SubscribeController->subscribe_mobile($_POST['mobile']);
            }

            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'User added successfully.');
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Error while adding user.');
        }
        return Redirect::route('list_user');
    }*/

    public function add_user(){
        return view('backend.users.add',$this->param);
    }
    public function add_user_post(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'password' => 'required',
            'email' => 'required|email|unique:users,email'
        ]);
        $User = new User();

        $arr = [
            'name' => ucfirst(trim($_POST['first_name'])).' '.ucfirst(trim($_POST['last_name'])),
            'first_name' => ucfirst(trim($_POST['first_name'])),
            'last_name' => ucfirst(trim($_POST['last_name'])),
            'email' => trim($_POST['email']),
            'password' => trim(Hash::make($_POST['password'])),
            'role' => trim($_POST['role']),
            'status' => 'Active',
            'created_at' => date('Y-m-d h:i:s')
        ];

        $update_user = $User->insert_user($arr);

        if(!empty($update_user)){
            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'Added successfully.');

            return Redirect::route('list_user');
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Error while creating user.');
            return redirect()->back();
        }

    }
    public function edit_user($id){
        $User = new User();

        $user_data = $User->select_fields_by_id($id);
        if(isset($user_data) && !empty($user_data)){
            $this->param['user_data'] = $user_data;
            return view('backend.users.edit',$this->param);
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
            return redirect()->back();
        }
    }
    public function edit_user_post(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$_POST['id'].',id'
        ]);
        $User = new User();

        $arr = [
            'first_name' => ucfirst(trim($_POST['first_name'])),
            'last_name' => ucfirst(trim($_POST['last_name'])),
            'email' => trim($_POST['email']),
            'updated_at' => date('Y-m-d h:i:s')
        ];

        $update_user = $User->update_user($_POST['id'],$arr);

        if(!empty($update_user)){
            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'Updated successfully.');
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Error while updating user.');
        }
        //return Redirect::route('view_user',[$_POST['id']]);
        return redirect()->back();
    }

}