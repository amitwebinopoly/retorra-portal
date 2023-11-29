<?php namespace App\Http\Controllers;

/*use App\Http\Controllers\Admin\SubscribeController;
use App\Http\Controllers\Admin\UsersController;
use App\Property;
use App\PropertyRequest;
use App\State;
use App\SubscribeEmail;
use App\User;
use App\UsersOtp;*/
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class FeHomeController extends Controller {

	public $param=array();
	public $response=array();

	public function __construct(){
		/*$this->middleware(function ($request, $next) {
			parent::login_user_details();
			return $next($request);
		});*/
	}

	public function fe_home(){
		//return view('frontend.home',$this->param);
		return Redirect::route('admin_login');
	}
	public function error_404(){
		return view('errors.404',$this->param);
	}
	public function error_500(){
		return view('errors.500',$this->param);
	}
	public function fe_logout(){
		Auth::logout();
		return Redirect::route('fe_home');
	}

	public function test_mail(){
		echo 'hi';
	}
}