<?php namespace App\Http\Controllers;

/*use App\Http\Controllers\Admin\SubscribeController;
use App\Http\Controllers\Admin\UsersController;
use App\Property;
use App\PropertyRequest;
use App\State;
use App\SubscribeEmail;
use App\User;
use App\UsersOtp;*/
use App\Models\QBCustomerTypes;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use QuickBooksOnline\API\DataService\DataService;

class QBController extends Controller {

	public $param=array();
	public $response=array();

	public function __construct(){
		/*$this->middleware(function ($request, $next) {
			parent::login_user_details();
			return $next($request);
		});*/
	}

	public function qb_auth(){
		$dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => Config::get('constant.QB_CLIENT_ID'),
			'ClientSecret' =>  Config::get('constant.QB_CLIENT_SECRET'),
			'scope' => Config::get('constant.QB_SCOPE'),
			'baseUrl' => Config::get('constant.QB_BASE_ENV'),
			'RedirectURI' => route('qb_callback')
		));

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

		$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

		return redirect($authUrl);
	}
	public function qb_callback(){
		$Settings = new Settings();

		$dataService = DataService::Configure(array(
			'auth_mode' => 'oauth2',
			'ClientID' => Config::get('constant.QB_CLIENT_ID'),
			'ClientSecret' =>  Config::get('constant.QB_CLIENT_SECRET'),
			'scope' => Config::get('constant.QB_SCOPE'),
			'baseUrl' => Config::get('constant.QB_BASE_ENV'),
			'RedirectURI' => route('qb_callback')
		));

		$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

		$accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);
		$dataService->updateOAuth2Token($accessTokenObj);

		$accessToken = $accessTokenObj->getAccessToken();

		$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
		$refreshedAccessToken = $refreshedAccessTokenObj->getRefreshToken();

		echo "Access Token is:";
		print_r($accessToken);

		$exist = $Settings->select_field_by_key('QB_ACCESS_TOKEN');
		if(isset($exist[0]->id)){
			$Settings->update_setting($exist[0]->id,[
				'value' => $accessToken,
				'modify_date' => date('d-m-Y h:i:s A'),
			]);
		}else{
			$Settings->insert_setting([
				'key' => 'QB_ACCESS_TOKEN',
				'value' => $accessToken,
				'add_date' => date('d-m-Y h:i:s A'),
			]);
		}

		echo "<hr>";
		echo "RefreshToken Token is:";
		print_r($refreshedAccessToken);

		$exist = $Settings->select_field_by_key('QB_REFRESH_TOKEN');
		if(isset($exist[0]->id)){
			$Settings->update_setting($exist[0]->id,[
				'value' => $refreshedAccessToken,
				'modify_date' => date('d-m-Y h:i:s A'),
			]);
		}else{
			$Settings->insert_setting([
				'key' => 'QB_REFRESH_TOKEN',
				'value' => $refreshedAccessToken,
				'add_date' => date('d-m-Y h:i:s A'),
			]);
		}

		echo "<hr>";
		echo "realmId is:";
		print_r($_GET['realmId']);

		$exist = $Settings->select_field_by_key('QB_REALM_ID');
		if(isset($exist[0]->id)){
			$Settings->update_setting($exist[0]->id,[
				'value' => $_GET['realmId'],
				'modify_date' => date('d-m-Y h:i:s A'),
			]);
		}else{
			$Settings->insert_setting([
				'key' => 'QB_REALM_ID',
				'value' => $_GET['realmId'],
				'add_date' => date('d-m-Y h:i:s A'),
			]);
		}

	}
	public function cronjob_qb_refresh_token(){
		$Settings = new Settings();

		$exist = $Settings->select_field_by_key('QB_REFRESH_TOKEN');
		if(isset($exist[0]->value)){
			$dataService = DataService::Configure(array(
				'auth_mode' => 'oauth2',
				'ClientID' => Config::get('constant.QB_CLIENT_ID'),
				'ClientSecret' =>  Config::get('constant.QB_CLIENT_SECRET'),
				'scope' => Config::get('constant.QB_SCOPE'),
				'baseUrl' => Config::get('constant.QB_BASE_ENV'),
				'RedirectURI' => route('qb_callback')
			));

			$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

			$accessTokenObj = $OAuth2LoginHelper->refreshAccessTokenWithRefreshToken($exist[0]->value);
			$refreshTokenValue = $accessTokenObj->getRefreshToken();

			$Settings->update_setting($exist[0]->id,[
				'value' => $refreshTokenValue,
				'modify_date' => date('d-m-Y h:i:s A'),
			]);

			$accessTokenValue = $accessTokenObj->getAccessToken();
			$exist = $Settings->select_field_by_key('QB_ACCESS_TOKEN');
			if(isset($exist[0]->id)){
				$Settings->update_setting($exist[0]->id,[
					'value' => $accessTokenValue,
					'modify_date' => date('d-m-Y h:i:s A'),
				]);
			}else{
				$Settings->insert_setting([
					'key' => 'QB_ACCESS_TOKEN',
					'value' => $accessTokenValue,
					'add_date' => date('d-m-Y h:i:s A'),
				]);
			}
		}else{
			echo 'No refresh key existed in DB.';
		}
	}

	public function sync_qb_customer_types(){
		$Settings = new Settings();
		$QBCustomerTypes = new QBCustomerTypes();

		$qb_access_token_exist = $Settings->select_field_by_key('QB_ACCESS_TOKEN');
		$qb_real_id_exist = $Settings->select_field_by_key('QB_REALM_ID');
		if(isset($qb_access_token_exist[0]->value) && !empty($qb_access_token_exist[0]->value) && isset($qb_real_id_exist[0]->value) && !empty($qb_real_id_exist[0]->value) ){
			$qb_access_token = $qb_access_token_exist[0]->value;
			$qb_real_id = $qb_real_id_exist[0]->value;
			if(Config::get('constant.QB_BASE_ENV') == 'production'){
				$api_base_url = 'quickbooks.api.intuit.com';
			}else{
				$api_base_url = 'sandbox-quickbooks.api.intuit.com';
			}

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://'.$api_base_url.'/v3/company/'.$qb_real_id.'/query?minorversion=69',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'Select * from CustomerType',	// startposition 1 maxresults 50
				CURLOPT_HTTPHEADER => array(
					'Accept: application/json',
					'Content-Type: application/text',
					'Authorization: Bearer '.$qb_access_token
				),
			));

			$response = curl_exec($curl);
			$response_arr = json_decode($response,1);
			curl_close($curl);

			if(isset($response_arr['QueryResponse']['CustomerType']) && !empty($response_arr['QueryResponse']['CustomerType'])){
				foreach($response_arr['QueryResponse']['CustomerType'] as $ct){
					$ct_exist = $QBCustomerTypes->select_field_by_name($ct['Name']);
					if(isset($ct_exist[0]->id) && !empty($ct_exist[0]->id)){
						$arr = [
							'qb_customer_type_id' => $ct['Id'],
							'modify_date' => time(),
						];
						$QBCustomerTypes->update_qb_customer_type($ct_exist[0]->id, $arr);
					}else{
						$arr = [
							'qb_customer_type_id' => $ct['Id'],
							'name' => $ct['Name'],
							'add_date' => time(),
							'modify_date' => '',
						];
						$QBCustomerTypes->insert_qb_customer_type($arr);
					}
				}
				$res['SUCCESS'] = 'TRUE';
				$res['MESSAGE'] = 'Syncing process has been finished.';
				return json_encode($res,1);
			}else{
				$res['SUCCESS'] = 'FALSE';
				$res['MESSAGE'] = 'Customer Types are not found.';
				return json_encode($res,1);
			}
		}else{
			$res['SUCCESS'] = 'FALSE';
			$res['MESSAGE'] = 'QB_ACCESS_TOKEN or QB_REALM_ID is not found.';
			return json_encode($res,1);
		}
	}
	public function sync_qb_customers(){
		$Settings = new Settings();
		$QBCustomerTypes = new QBCustomerTypes();
		$User = new User();

		$qb_access_token_exist = $Settings->select_field_by_key('QB_ACCESS_TOKEN');
		$qb_real_id_exist = $Settings->select_field_by_key('QB_REALM_ID');
		if(isset($qb_access_token_exist[0]->value) && !empty($qb_access_token_exist[0]->value) && isset($qb_real_id_exist[0]->value) && !empty($qb_real_id_exist[0]->value) ){
			$qb_access_token = $qb_access_token_exist[0]->value;
			$qb_real_id = $qb_real_id_exist[0]->value;
			if(Config::get('constant.QB_BASE_ENV') == 'production'){
				$api_base_url = 'quickbooks.api.intuit.com';
			}else{
				$api_base_url = 'sandbox-quickbooks.api.intuit.com';
			}

			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://'.$api_base_url.'/v3/company/'.$qb_real_id.'/query?minorversion=69',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>'Select * from Customer',	// startposition 1 maxresults 50
				CURLOPT_HTTPHEADER => array(
					'Accept: application/json',
					'Content-Type: application/text',
					'Authorization: Bearer '.$qb_access_token
				),
			));

			$response = curl_exec($curl);
			$response_arr = json_decode($response,1);
			curl_close($curl);

			if(isset($response_arr['QueryResponse']['Customer']) && !empty($response_arr['QueryResponse']['Customer'])){
				foreach($response_arr['QueryResponse']['Customer'] as $cus){
					if(isset($cus['PrimaryEmailAddr']['Address']) && !empty($cus['PrimaryEmailAddr']['Address'])){
						$email = $cus['PrimaryEmailAddr']['Address'];
						$role = 'Showroom';
						$customer_type_ref = '';
						if(isset($cus['CustomerTypeRef']['value']) && !empty($cus['CustomerTypeRef']['value'])){
							$role = 'Designer';
							$customer_type_ref = $cus['CustomerTypeRef']['value'];
						}

						$arr = [
							'name' => isset($cus['GivenName'])?($cus['GivenName'].' '.@$cus['FamilyName']):$cus['DisplayName'],
							'first_name' => isset($cus['GivenName'])?$cus['GivenName']:$cus['DisplayName'],
							'last_name' => isset($cus['FamilyName'])?$cus['FamilyName']:"",
							'email' => $email,
							'role' => $role,
							'customer_type_ref' => $customer_type_ref
						];

						$cus_exist = $User->select_fields_by_email($email);
						if(isset($cus_exist[0]->id) && !empty($cus_exist[0]->id)){
							$arr['updated_at'] = date('Y-m-d h:i:s');
							$User->update_user($cus_exist[0]->id, $arr);
						}else{
							$arr['password'] = '';
							$arr['status'] = 'Active';
							$arr['created_at'] = date('Y-m-d h:i:s');
							$User->insert_user($arr);
						}
					}
				}
				$res['SUCCESS'] = 'TRUE';
				$res['MESSAGE'] = 'Syncing process has been finished.';
				return json_encode($res,1);
			}else{
				echo '';
				$res['SUCCESS'] = 'FALSE';
				$res['MESSAGE'] = 'Customers are not found.';
				return json_encode($res,1);
			}
		}else{
			$res['SUCCESS'] = 'FALSE';
			$res['MESSAGE'] = 'QB_ACCESS_TOKEN or QB_REALM_ID is not found.';
			return json_encode($res,1);
		}
	}
}