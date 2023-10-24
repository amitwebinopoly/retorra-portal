<?php
require 'vendor/autoload.php';

$dataService = \QuickBooksOnline\API\DataService\DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => 'ABh84ojNdw3FNHu5VEv1JZoIq14WdIF8QvARwYrQSlTuFbkANl',
    'ClientSecret' =>  'XuPdzhqKN7b5H1eqzgbvcQ1we1gCa4hpcfQJobuv',
    'RedirectURI' => 'http://localhost/pro_dev_8/retorra-portal/asset_be/custom/quickbook/callback.php',
    'scope' => 'com.intuit.quickbooks.accounting openid profile email phone address',
    'baseUrl' => "development"
));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

// Get the Authorization URL from the SDK
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

header('location:'.$authUrl);
?>