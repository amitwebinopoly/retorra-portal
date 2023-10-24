<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller {

	public $param=array();
	public $response=array();

	public function __construct(){
		$this->middleware(function ($request, $next) {
			parent::login_user_details();
			return $next($request);
		});
	}

	public function admin_login(){
		return view('backend.login',$this->param);
	}

	public function admin_login_post(){
		if(isset($_POST['login_email_mob']) && !empty($_POST['login_email_mob']) &&
			isset($_POST['login_password']) && !empty($_POST['login_password'])
		){

			$User = new User();
			$email_mob = trim($_POST['login_email_mob']);
			$password = trim($_POST['login_password']);

			$user_data = $User->check_admin_for_login($email_mob);
			if(!empty($user_data)){
				if (Hash::check(trim($password), $user_data[0]->password)) {
					Auth::loginUsingId($user_data[0]->id);
					return Redirect::route('admin_home');
				}else{
					Session::put('SUCCESS','FALSE');
					Session::put('MESSAGE','Wrong Credentials.');
					return redirect()->back();
				}
			}else{
				Session::put('SUCCESS','FALSE');
				Session::put('MESSAGE','Wrong Credentials.');
				return redirect()->back();
			}

		}else{
			Session::put('SUCCESS','FALSE');
			Session::put('MESSAGE','Invalid request.');
			return redirect()->back();
		}
	}

	public function admin_home(){
		$User = new User();

		/*$User->set_role('Admin');
		$count_all = $User->count_all('');
		$this->param['count_admin'] = $count_all[0]->count;

		$User->set_role('User');
		$count_all = $User->count_all('');
		$this->param['count_user'] = $count_all[0]->count;*/

		$this->param['date_formate'] = Config::get('constant.DATE_FORMATE');
		return view('backend.home',$this->param);
	}

	public function admin_logout(){
		Auth::logout();
		return Redirect::route('admin_login');
    }
}