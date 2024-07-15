<?php namespace App\Http\Controllers;
use App\Http\Controllers\QBController;

use App\Models\User;
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

	public function shopify_webhook(){
		$QBController = new QBController();
		$User = new User();
		$data = file_get_contents('php://input');
		$topic = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];

		//$data = '{"id":7514510033202,"email":"nik2@webinopoly.com","created_at":"2023-10-04T12:28:02-05:00","updated_at":"2024-07-11T07:34:50-05:00","first_name":"nik","last_name":"suthar","orders_count":4,"state":"enabled","total_spent":"94.97","last_order_id":5977031278898,"note":"","verified_email":true,"multipass_identifier":null,"tax_exempt":false,"tags":"custom, price","last_order_name":"#1004","currency":"USD","phone":null,"addresses":[{"id":9710039925042,"customer_id":7514510033202,"first_name":"nik","last_name":"suthar","company":"webinopoly","address1":"6464 Savoy dr., Suite 720","address2":null,"city":"Houston","province":"Texas","country":"United States","zip":"78701","phone":null,"name":"nik suthar","province_code":"TX","country_code":"US","country_name":"United States","default":true}],"accepts_marketing":false,"accepts_marketing_updated_at":null,"marketing_opt_in_level":"single_opt_in","tax_exemptions":[],"email_marketing_consent":{"state":"not_subscribed","opt_in_level":"single_opt_in","consent_updated_at":null},"sms_marketing_consent":null,"admin_graphql_api_id":"gid:\/\/shopify\/Customer\/7514510033202","default_address":{"id":9710039925042,"customer_id":7514510033202,"first_name":"nik","last_name":"suthar","company":"webinopoly","address1":"6464 Savoy dr., Suite 720","address2":null,"city":"Houston","province":"Texas","country":"United States","zip":"78701","phone":null,"name":"nik suthar","province_code":"TX","country_code":"US","country_name":"United States","default":true}}';
		//$topic = 'customers/create';

		if($topic == 'customers/create'){
			$customer = json_decode($data,1);
			if(isset($customer['email']) && !empty($customer['email']) ){
				//first check email is existed or not in quickbook
				$get_cust_res = $QBController->get_customer_by_email($customer['email']);
				$get_cust_res_arr = json_decode($get_cust_res,1);
				if($get_cust_res_arr['SUCCESS']=='FALSE'){
					//customer is not exist, then let's create new customer
					$create_cust_data = [
						"FullyQualifiedName" => $customer['first_name']." ".$customer['last_name'],
						"PrimaryEmailAddr" => [
							"Address" => $customer['email']
						],
						"DisplayName" => $customer['first_name']." ".$customer['last_name'],
						"Suffix" => "",
						"Title" => "",
						"MiddleName" => "",
						"Notes" => "shopify_customer_id:".$customer['id'],
						"FamilyName" => $customer['first_name'],
						"GivenName" => $customer['last_name']
					];
					$create_customer_res = $QBController->create_customer($create_cust_data);
					$create_customer_res_arr = json_decode($create_customer_res,1);
					if(isset($create_customer_res_arr['DATA']['Customer']['Id']) ){
						$user_insert_arr = [
							'name' => $customer['first_name']." ".$customer['last_name'],
							'first_name' => $customer['first_name'],
							'last_name' => $customer['last_name'],
							'email' => $customer['email'],
							'role' => 'Designer',
							'customer_ref' => $create_customer_res_arr['DATA']['Customer']['Id'],
							'customer_type_ref' => "",
							'password' => "",
							'status' => "Active",
							'created_at' => date('Y-m-d h:i:s'),
						];
						$User->insert_user($user_insert_arr);
					}
				}
			}
		}

		echo "ok";
		http_response_code(200);
		return;
	}
}