<?php
//API EXAMPLES
//https://github.com/IntuitDeveloper/SampleApp-CRUD-PHP/blob/master/CRUD_Examples/Customer/CustomerRead.php

require "vendor/autoload.php";


use QuickBooksOnline\API\DataService\DataService;

// Prep Data Services
$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => "ABh84ojNdw3FNHu5VEv1JZoIq14WdIF8QvARwYrQSlTuFbkANl",
    'ClientSecret' => "XuPdzhqKN7b5H1eqzgbvcQ1we1gCa4hpcfQJobuv",
    'accessTokenKey' => 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..zfVZJCvtHOlZnnoRGkr6Mw.gy30qvYY69R1joMxNKxCGBhmRz8_WV9gd54wJFXJGp9HoBqIURma4Nh-wedBbkCQhVfjbzKO2cbdlTT5YyBjymrWxkbsafnBo2XGnUnc6QZ8M7pN2Gc_bilkNV8MRkgbOqFRT0cShj3qBQ-lZ_FSrP2Ya8HzaLj9rVvgfiwlJIH8K8qnjbaM_KqENY1unTplzTcUEBhDji7CIr5AqPFswxDfKvvpEeTcf1GJHqGDZUd4A-NxtP4Jc2nfPf7JynZCGYKnHCB7H9I9DbcBYQrz2eKWh8FZhGd_Zz_X1vXshJNcH-JWpa5AYPtuN-AUfNE9fjQnfXIYialqfOEF1Vf4snGA2kF4nE_tQEvspx71-igp4TIfMTmZe_a6HpHdYwriFOaB1HSd9-ZTzdsThWkwJyf8-1ixe3pMleu6nhqEmRv3M1NoDpF56Bz93mrmu_ZElyBOp_XAX1_D2wp8DPY6geWhS5EaoXSFnPzUHCumkMh03cp-Fn9pRNaeLH-J7LANGW9NmRW4i4OY4Way3RbkGuduEfHYy5aMElsITH9inatC2kFMMpzg3cmzQfvI_fKbXk38XsUGCqjrIR_vVvnqqCvAzw_qBd1rfHedFgbNd6TZuYbpmUJE_bHGBxDImejQ0ootAS3DHUi7Tl4ggxGU8IzFxDKpBM0EPJb1pUZDmjh7t0_8HUD03DBY3aBXPJcaeyylnkbJY33JF7B_GXAK6bX_IwI0Y4ArgK9r9JNAA1aPuLvi6Q-4C8yKLDyD3rUJH83ue3ANOrSm_AsqKc8-B2QgulnhlJB50pIqLYLq94Fmw1qeeckBX-j6-LteOfOrZYw4gD-zZPspkK4E9BQbli9peUhsGLJglkTr-fM1YLOS75A5IHZW3ppd9eIleThj.bxU4qqUdhan9Edw6PVBmPg',
    'refreshTokenKey' => "AB117065351479ASe7Nv33I4MFxQ9TYNE7YzMLTgOv3oo389oF",
    'QBORealmID' => "4620816365351191740",
    'baseUrl' => "Development"
));

$customer = $dataService->FindbyId('customer',1);
$error = $dataService->getLastError();
if ($error) {
    echo "The Status code is: " . $error->getHttpStatusCode() . "\n";
    echo "The Helper message is: " . $error->getOAuthHelperError() . "\n";
    echo "The Response message is: " . $error->getResponseBody() . "\n";
}
else {
    echo '<pre>';
    print_r($customer);
    exit;
}