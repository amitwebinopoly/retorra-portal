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

		echo "<hr>";
		echo "RefreshToken Token is:";
		print_r($refreshedAccessToken);

		echo "<hr>";
		echo "realmId is:";
		print_r($_GET['realmId']);

	}
	public function cronjob_qb_refresh_token(){

	}
}