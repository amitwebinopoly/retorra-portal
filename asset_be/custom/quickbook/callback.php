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

$accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);
$dataService->updateOAuth2Token($accessTokenObj);

$accessToken = $accessTokenObj->getAccessToken();

$refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
$refreshedAccessToken = $refreshedAccessTokenObj->getRefreshToken();

echo "Access Token is:";
print_r($accessToken);
//eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0.._Gq34FrZzPCBdINSM28qwQ.auSMBw44wY-_wDqSqEJDbPtl2uODl4JwivlYIFklkNvP325qZL4p9KK2LYkhfpz2aYRXIP82gcJrdgLX-FJN2Om4pbkqmV6Y5jolMyLXdqeA43ExC2JOKDYmVQrxA_TZniZbbMwzLkaA0HG--FitkUmMHDKZIlTLHu4fKZf09YsYT0JntRS-MGKP45BIDrZmgfb_KNHqoyIPrJlBullNOCF6cCdLg4-eFAjfOHjMymzWk2KnV0KG4fD1F-qycer-ZarktWAmgFkH4pBvPumRqwvkwqb-54vCdHk9ajBAnQJR74Ged2E-HuVQaeitdmNCiPpTVaM2K93oambvjyI3ggPKvEHfJMcfMeHbER2CyxMNVsd_fHRuwjGxhZgfGM8xvRA0wj3Rdq6PQDXxXZrqyaUoAy86Jo7PY58IVPfWA_U-CvB4nbOMDpPR5197S5ODAePw2WspObreeOqbwp2c6EAOJ2P32RkgHnzaU5VODItsdU8sDwCUS_fJIYMTCJIlBzabxksp1SmSyRZzigVsJ940INTdbMaqQ2bZjaIEz8RDa4p60jffDFL79qBztlmb8DkTDnt9xHU9sm887dKidbLM3jwYgNP4jM33ne9JIDhWATaO1kULpB2yfL5gS8TeqpUEJjiqBhjQFB_JEWr7o2EwWa7vzchx5fkQDpYXPg5O8IyjKsfo9FpWFSXMAhEl74MvNYkxOm20gF1mgKUyKwVy5mB806id5gbcjpexA68arNC6IlXNpDOxzNAZKffLctwNfAi3YymYTg53ayXHI7zTjrm5anbywqHRzopdPVxM0Q0nCgUuxWzHoFfgHaDcOa3dGlpxM1c7f88Afy2OLFgBpNNDgG-njhoDNY8KWxTDqnug5VxzGDaFGie_cgCq.vNL2wAGUd3rIpRmAwhfRwA

echo "<hr>";
echo "RefreshToken Token is:";
print_r($refreshedAccessToken);
//AB117065351479ASe7Nv33I4MFxQ9TYNE7YzMLTgOv3oo389oF

echo "<hr>";
echo "realmId is:";
print_r($_GET['realmId']);
//4620816365351191740

?>