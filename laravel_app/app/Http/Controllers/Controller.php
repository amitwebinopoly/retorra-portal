<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function login_user_details(){
        $User=new User();

        $user_param=array();
        $user_param['first_name']='';
        $user_param['last_name']='';
        $user_param['id']='';
        $user_param['role']='';
        $user_param['user_image']=asset('asset_fe/images/profile-pic.png');
        if(isset(Auth::user()->id)){
            $user_id=Auth::user()->id;
            $select_by_user_id = $User->select_fields_by_id($user_id,['first_name','last_name','email','role']);

            if(!empty($select_by_user_id[0])){
                /*if((isset($select_by_user_id[0]->profile_picture))&&(!empty($select_by_user_id[0]->profile_picture))&&($select_by_user_id[0]->profile_picture!="NULL") && file_exists(public_path().Config::get('constant.PROFILE_LOCATION').$select_by_user_id[0]->profile_picture)){
                    $user_param['user_image']=asset(Config::get('constant.PROFILE_LOCATION').$select_by_user_id[0]->profile_picture);
                }*/

                $user_param['first_name']=ucfirst($select_by_user_id[0]->first_name);
                $user_param['last_name']=ucfirst($select_by_user_id[0]->last_name);
                $user_param['email']=$select_by_user_id[0]->email;
                $user_param['id']=$user_id;
                $user_param['role']=$select_by_user_id[0]->role;
            }
        }

        View::share("user_param",$user_param);
    }
}
