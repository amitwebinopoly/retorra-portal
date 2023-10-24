<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\InexController;
use App\State;
use App\SubscribeEmail;
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


class UsersController extends Controller {

    public  $param=array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            parent::login_user_details();
            return $next($request);
        });
    }

    public function list_user(){
        $this->param['page_title']='User List';
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.user.list', $this->param);
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

        if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }
        if(isset($_POST['reg_as_filter']) && !empty($_POST['reg_as_filter'])){
            $User->set_reg_as($_POST['reg_as_filter']);
        }

        $User->set_role('User');
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
            if(isset($all_list) && !empty($all_list)){
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
            echo json_encode($this->param,1);
        }else{
            $this->param['sr_start']=$sr_start;
            $this->param['all_list']=$all_list;
            $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
            $html = view('backend.user.list_post', $this->param)->render();
            
            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }

    public function create_unique_username($id){
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
    }

    public function edit_user($id){
        $User = new User();
        $State = new State();

        $User->set_id($id);
        $User->set_fields('*');
        $user_data = $User->select_fields_by_id();
        if(isset($user_data) && !empty($user_data) && $user_data[0]->role=='User'){
            $state_list = $State->select_for_dropdown_by_user_status();
            $this->param['state_list']=$state_list;

            $this->param['page_title'] = 'Edit User';
            $this->param['user_data'] = $user_data;
            return view('backend.user.edit',$this->param);
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
            return redirect()->back();
        }
    }
    public function edit_user_post(Request $request){
        $this->validate($request, [
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$_POST['id'].',id',
            'mobile' => 'unique:users,mobile,'.$_POST['id'].',id'
        ]);
        $User = new User();

        $website = trim($_POST['website']);
        if(!empty($website)){
            if(strpos($website,'http') === false){
                $website = 'http://'.$website;
            }
        }
        $state_id = null;
        if(isset($_POST['state_id']) && !empty($_POST['state_id'])){
            $state_id = $_POST['state_id'];
        }

        $User->set_id($_POST['id']);
        $User->set_first_name(ucfirst(trim($_POST['first_name'])));
        $User->set_last_name(ucfirst(trim($_POST['last_name'])));
        $User->set_mobile(trim($_POST['mobile']));
        $User->set_email(trim($_POST['email']));

        $User->set_website($website);
        $User->set_address(trim($_POST['address']));
        $User->set_city(trim($_POST['city']));
        $User->set_state_id($state_id);
        $User->set_zipcode(trim($_POST['zipcode']));
        $User->set_modify_date(time());
        $update_user = $User->update_user();

        if(!empty($update_user)){
            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'User updated successfully.');
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Error while updating user.');
        }
        return Redirect::route('view_user',[$_POST['id']]);
    }

    public function view_user($id){
        $User = new User();

        $User->set_id($id);
        $user_data = $User->select_data_by_id();
        if(isset($user_data) && !empty($user_data) && $user_data[0]->role=='User'){
            if((isset($user_data[0]->profile_picture))&&(!empty($user_data[0]->profile_picture)) && file_exists(public_path().Config::get('constant.PROFILE_LOCATION').$user_data[0]->profile_picture)){
                $user_data[0]->profile_picture = asset(Config::get('constant.PROFILE_LOCATION').$user_data[0]->profile_picture);
            }else{
                $user_data[0]->profile_picture = asset(Config::get('constant.DEFAULT_LOCATION').'profile.jpg');
            }

            if((isset($user_data[0]->docid1))&&(!empty($user_data[0]->docid1)) && file_exists(public_path().Config::get('constant.DOCUMENT_ID_LOCATION').$user_data[0]->docid1)){
                $user_data[0]->docid1 = asset(Config::get('constant.DOCUMENT_ID_LOCATION').$user_data[0]->docid1);
            }else{
                $user_data[0]->docid1 = '';
            }
            if((isset($user_data[0]->docid2))&&(!empty($user_data[0]->docid2)) && file_exists(public_path().Config::get('constant.DOCUMENT_ID_LOCATION').$user_data[0]->docid2)){
                $user_data[0]->docid2 = asset(Config::get('constant.DOCUMENT_ID_LOCATION').$user_data[0]->docid2);
            }else{
                $user_data[0]->docid2 = '';
            }
            if((isset($user_data[0]->docid3))&&(!empty($user_data[0]->docid3)) && file_exists(public_path().Config::get('constant.DOCUMENT_ID_LOCATION').$user_data[0]->docid3)){
                $user_data[0]->docid3 = asset(Config::get('constant.DOCUMENT_ID_LOCATION').$user_data[0]->docid3);
            }else{
                $user_data[0]->docid3 = '';
            }
            $this->param['page_title'] = 'User Details';
            $this->param['user_data'] = $user_data;
            return view('backend.user.view',$this->param);
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
            return redirect()->back();
        }
    }
    public function delete_user($id){
        $User = new User();
        $User->set_id($id);
        $User->delete_user();

        Session::put('SUCCESS','TRUE');
        Session::put('MESSAGE','Delete successfully.');
        return redirect()->back();
    }
    public function user_profile_picture_post(){
        if(isset($_POST['user_id']) && !empty($_POST['user_id']) && isset($_FILES['profile_picture']['name']) && !empty($_FILES['profile_picture']['name'])){
            $User = new User();
            $upload_dir = public_path().Config::get('constant.PROFILE_LOCATION');
            $image_arr = explode('.',$_FILES['profile_picture']['name']);
            $ext = array_pop($image_arr);
            $img_name = time().$_POST['user_id'].'.'.$ext;
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'],$upload_dir.$img_name)){
                $User->set_id($_POST['user_id']);
                $User->set_profile_picture($img_name);
                $User->update_profile_picture();

                Session::put('SUCCESS','TRUE');
                Session::put('MESSAGE', 'Successfully uplaoded.');
            }else{
                Session::put('SUCCESS','FALSE');
                Session::put('MESSAGE', 'Error while image upload.');
            }
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
        }
        return redirect()->back();
    }
    public function user_doc_post(){
        if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
            $User = new User();
            $upload_dir = public_path().Config::get('constant.DOCUMENT_ID_LOCATION');

            if(isset($_FILES['docid1']['name']) && !empty($_FILES['docid1']['name'])){
                sleep(1);
                $image_arr = explode('.',$_FILES['docid1']['name']);
                $ext = array_pop($image_arr);
                $img_name = time().$_POST['user_id'].'.'.$ext;
                if(move_uploaded_file($_FILES['docid1']['tmp_name'],$upload_dir.$img_name)){
                    $User->set_id($_POST['user_id']);
                    $User->set_docid1($img_name);
                    $User->set_docid1_is_verified('Pending');
                    $User->update_docid1();
                }
            }
            if(isset($_FILES['docid2']['name']) && !empty($_FILES['docid2']['name'])){
                sleep(1);
                $image_arr = explode('.',$_FILES['docid2']['name']);
                $ext = array_pop($image_arr);
                $img_name = time().$_POST['user_id'].'.'.$ext;
                if(move_uploaded_file($_FILES['docid2']['tmp_name'],$upload_dir.$img_name)){
                    $User->set_id($_POST['user_id']);
                    $User->set_docid2($img_name);
                    $User->set_docid2_is_verified('Pending');
                    $User->update_docid2();
                }
            }
            if(isset($_FILES['docid3']['name']) && !empty($_FILES['docid3']['name'])){
                sleep(1);
                $image_arr = explode('.',$_FILES['docid3']['name']);
                $ext = array_pop($image_arr);
                $img_name = time().$_POST['user_id'].'.'.$ext;
                if(move_uploaded_file($_FILES['docid3']['tmp_name'],$upload_dir.$img_name)){
                    $User->set_id($_POST['user_id']);
                    $User->set_docid3($img_name);
                    $User->set_docid3_is_verified('Pending');
                    $User->update_docid3();
                }
            }
            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'Successfully saved.');
            return Redirect::route('view_user',[$_POST['user_id'],'tab'=>'doc']);
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
            return redirect()->back();
        }
    }

    public function fetch_user_autocomplete(){
        $User=new User();
        if( (isset($_GET['term']))&&(!empty($_GET['term'])) ){
            $term=$_GET['term'];
            $User->set_term($term);
            $select_title_by_term=$User->select_title_by_term("'User'");
            if(isset($select_title_by_term) && !empty($select_title_by_term)){
                $result_json = json_encode($select_title_by_term);
            }else{
                $result_json = '[]';
            }
            echo $result_json;
        }else{
            echo '[]';
        }
    }

    public function user_email_verify($id,$email_verified){
        $User = new User();
        $User->set_id($id);
        $User->set_email_is_verified($email_verified);
        $User->change_email_is_verified();

        Session::put('SUCCESS','TRUE');
        Session::put('MESSAGE', 'Successfully '.$email_verified);
        return redirect()->back();
    }
    public function user_mobile_verify($id,$mobile_verified){
        $User = new User();
        $User->set_id($id);
        $User->set_mobile_is_verified($mobile_verified);
        $User->change_mobile_is_verified();

        Session::put('SUCCESS','TRUE');
        Session::put('MESSAGE', 'Successfully '.$mobile_verified);
        return redirect()->back();
    }
    public function user_docid1_verify($id,$docid1_verified){
        $User = new User();
        $User->set_id($id);
        $User->set_docid1_is_verified($docid1_verified);
        $User->change_docid1_is_verified();

        Session::put('SUCCESS','TRUE');
        Session::put('MESSAGE', 'Successfully '.$docid1_verified.'.');
        return Redirect::route('view_user',[$id,'tab'=>'doc']);
    }
    public function user_docid2_verify($id,$docid2_verified){
        $User = new User();
        $User->set_id($id);
        $User->set_docid2_is_verified($docid2_verified);
        $User->change_docid2_is_verified();

        Session::put('SUCCESS','TRUE');
        Session::put('SUCCESS','TRUE');
        Session::put('MESSAGE', 'Successfully '.$docid2_verified.'.');
        return Redirect::route('view_user',[$id,'tab'=>'doc']);
    }
    public function user_docid3_verify($id,$docid3_verified){
        $User = new User();
        $User->set_id($id);
        $User->set_docid3_is_verified($docid3_verified);
        $User->change_docid3_is_verified();

        Session::put('SUCCESS','TRUE');
        Session::put('MESSAGE', 'Successfully '.$docid3_verified.'.');
        return Redirect::route('view_user',[$id,'tab'=>'doc']);
    }

    public function account_setting_post(){
        $User = new User();
        if(isset($_POST['user_id']) && !empty($_POST['user_id'])){
            $id = $_POST['user_id'];

            if(isset($_POST['seller_is_paid']) && !empty($_POST['seller_is_paid'])){
                $User->set_id($id);
                $User->set_seller_is_paid($_POST['seller_is_paid']);
                $User->update_seller_is_paid();
            }
            if(isset($_POST['buyer_is_paid']) && !empty($_POST['buyer_is_paid'])){
                $User->set_id($id);
                $User->set_buyer_is_paid($_POST['buyer_is_paid']);
                $User->update_buyer_is_paid();
            }

            Session::put('SUCCESS','TRUE');
            Session::put('MESSAGE', 'Successfully updated.');
            return Redirect::route('view_user',[$id,'tab'=>'setting']);
        }else{
            Session::put('SUCCESS','FALSE');
            Session::put('MESSAGE', 'Invalid request.');
            return redirect()->back();
        }
    }

}