<?php
require 'vendor/autoload.php';

use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;

// The first parameter of OAuth2LoginHelper is the ClientID, second parameter is the client Secret
$oauth2LoginHelper = new OAuth2LoginHelper("ABh84ojNdw3FNHu5VEv1JZoIq14WdIF8QvARwYrQSlTuFbkANl","XuPdzhqKN7b5H1eqzgbvcQ1we1gCa4hpcfQJobuv");
$accessTokenObj = $oauth2LoginHelper->
refreshAccessTokenWithRefreshToken("AB117065351479ASe7Nv33I4MFxQ9TYNE7YzMLTgOv3oo389oF");
$accessTokenValue = $accessTokenObj->getAccessToken();
$refreshTokenValue = $accessTokenObj->getRefreshToken();
echo "Access Token is:";
print_r($accessTokenValue);
echo "<hr>";
echo "RefreshToken Token is:";
print_r($refreshTokenValue);
?>