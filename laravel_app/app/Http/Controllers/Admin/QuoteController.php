<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\GraphqlController;
use App\Http\Controllers\InexController;
use App\Http\Controllers\QBController;
use App\Models\QBCustomerTypes;
use App\Models\Quotes;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
Use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;


class QuoteController extends Controller {

    public  $param=array();

    public function __construct(){
        $this->middleware(function ($request, $next) {
            parent::login_user_details();
            return $next($request);
        });
    }

    public function list_quote(){
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.quotes.list', $this->param);
    }
    public function list_quote_post(){
        $Quotes = new Quotes();
        $User = new User();
        $QBCustomerTypes = new QBCustomerTypes();
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $rows='10';
        $keyword='';

        if( (isset($_REQUEST['rows']))&&(!empty($_REQUEST['rows'])) ){
            $rows=$_REQUEST['rows'];
        }
        if( (isset($_REQUEST['keyword']))&&(!empty($_REQUEST['keyword'])) ){
            $keyword=$InexController->allow_special_character_in_keyword($_REQUEST['keyword']);
        }
        if( (isset($_REQUEST['current_page']))&&(!empty($_REQUEST['current_page'])) ){
            $current_page=$_REQUEST['current_page'];
        }
        $start=($current_page-1)*$rows;
        $end=$rows;
        $sort_field = '';
        if(isset($_POST['sort_field']) && !empty($_POST['sort_field'])){
            $sort_field = $_POST['sort_field'];
        }
        $this->param['sort_field']=$sort_field;
        $sort_type = '';
        if(isset($_POST['sort_type']) && !empty($_POST['sort_type'])){
            $sort_type = $_POST['sort_type'];
        }
        $this->param['sort_type']=$sort_type;


        /*if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }*/

        $all_count = [];
        $all_list = [];
        if(Auth::user()->role == 'Showroom'){
            $qb_ct_data = $QBCustomerTypes->select_field_by_name(Auth::user()->name);
            $select_all_designer = $User->get_all_designer_by_showroom(@$qb_ct_data[0]->qb_customer_type_id);
            if(isset($select_all_designer[0]->customer_refs) && !empty($select_all_designer[0]->customer_refs)){
                $Quotes->set_qb_customer_ref_ids($select_all_designer[0]->customer_refs);

                $Quotes->set_status('Draft');
                $all_count = $Quotes->count_all($keyword);
                $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);
            }
        }
        else{
            $Quotes->set_status('Draft');
            $all_count = $Quotes->count_all($keyword);
            $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);
        }

        if( (isset($all_count[0]->count))&&(!empty($all_count[0]->count)) ){
            $record_count=$all_count[0]->count;
            $page=$record_count/$rows;
            $page=ceil($page);
        }
        $keyword=$InexController->remove_special_character_in_keyword($keyword);
        $sr_start=0;
        if($record_count>=1){
            $sr_start=(($current_page-1)*$rows)+1;
        }
        $sr_end=($current_page)*$rows;
        if($record_count<=$sr_end){
            $sr_end=$record_count;
        }

        if(isset($_POST['pagination_export']) && $_POST['pagination_export']=='Y'){
            /*if(isset($all_list) && !empty($all_list)){
                $date_formate=Config::get('constant.DATE_FORMATE');
                $file_full_path = public_path().Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv";
                $file_full_url = asset(Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv");
                $file_for_download_data = fopen($file_full_path,"w");
                fputcsv($file_for_download_data,array('#','Name','Email','Mobile','Add Date'));
                $i=$sr_start;
                foreach ($all_list as $single){
                    if($single->add_date!=''){
                        $add_date = date($date_formate, $single->add_date);
                    }else{
                        $add_date = '';
                    }
                    fputcsv($file_for_download_data,array(
                        $i,
                        $single->first_name.' '.$single->last_name,
                        $single->email,
                        $single->mobile,
                        $add_date
                    ));
                    $i++;
                }
                fclose($file_for_download_data);
                $this->param['SUCCESS']='TRUE';
                $this->param['file_full_url']=$file_full_url;
            }else{
                $this->param['SUCCESS']='FALSE';
            }
            echo json_encode($this->param,1);*/
        }else{
            $this->param['sr_start']=$sr_start;
            $this->param['all_list']=$all_list;
            $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
            $html = view('backend.quotes.list_post', $this->param)->render();

            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }

    public function list_order(){
        $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
        return view('backend.orders.list', $this->param);
    }
    public function list_order_post(){
        $Quotes = new Quotes();
        $User = new User();
        $QBCustomerTypes = new QBCustomerTypes();
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $rows='10';
        $keyword='';

        if( (isset($_REQUEST['rows']))&&(!empty($_REQUEST['rows'])) ){
            $rows=$_REQUEST['rows'];
        }
        if( (isset($_REQUEST['keyword']))&&(!empty($_REQUEST['keyword'])) ){
            $keyword=$InexController->allow_special_character_in_keyword($_REQUEST['keyword']);
        }
        if( (isset($_REQUEST['current_page']))&&(!empty($_REQUEST['current_page'])) ){
            $current_page=$_REQUEST['current_page'];
        }
        $start=($current_page-1)*$rows;
        $end=$rows;
        $sort_field = '';
        if(isset($_POST['sort_field']) && !empty($_POST['sort_field'])){
            $sort_field = $_POST['sort_field'];
        }
        $this->param['sort_field']=$sort_field;
        $sort_type = '';
        if(isset($_POST['sort_type']) && !empty($_POST['sort_type'])){
            $sort_type = $_POST['sort_type'];
        }
        $this->param['sort_type']=$sort_type;


        /*if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }*/

        $all_count = [];
        $all_list = [];
        if(Auth::user()->role == 'Showroom'){
            $qb_ct_data = $QBCustomerTypes->select_field_by_name(Auth::user()->name);
            $select_all_designer = $User->get_all_designer_by_showroom(@$qb_ct_data[0]->qb_customer_type_id);
            if(isset($select_all_designer[0]->customer_refs) && !empty($select_all_designer[0]->customer_refs)){
                $Quotes->set_qb_customer_ref_ids($select_all_designer[0]->customer_refs);

                $Quotes->set_status('Order-Placed');
                $all_count = $Quotes->count_all($keyword);
                $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);
            }
        }
        else{
            $Quotes->set_status('Order-Placed');
            $all_count = $Quotes->count_all($keyword);
            $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);
        }

        if( (isset($all_count[0]->count))&&(!empty($all_count[0]->count)) ){
            $record_count=$all_count[0]->count;
            $page=$record_count/$rows;
            $page=ceil($page);
        }
        $keyword=$InexController->remove_special_character_in_keyword($keyword);
        $sr_start=0;
        if($record_count>=1){
            $sr_start=(($current_page-1)*$rows)+1;
        }
        $sr_end=($current_page)*$rows;
        if($record_count<=$sr_end){
            $sr_end=$record_count;
        }

        if(isset($_POST['pagination_export']) && $_POST['pagination_export']=='Y'){
            /*if(isset($all_list) && !empty($all_list)){
                $date_formate=Config::get('constant.DATE_FORMATE');
                $file_full_path = public_path().Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv";
                $file_full_url = asset(Config::get('constant.DOWNLOAD_TABLE_LOCATION')."downloaded_table_".time().".csv");
                $file_for_download_data = fopen($file_full_path,"w");
                fputcsv($file_for_download_data,array('#','Name','Email','Mobile','Add Date'));
                $i=$sr_start;
                foreach ($all_list as $single){
                    if($single->add_date!=''){
                        $add_date = date($date_formate, $single->add_date);
                    }else{
                        $add_date = '';
                    }
                    fputcsv($file_for_download_data,array(
                        $i,
                        $single->first_name.' '.$single->last_name,
                        $single->email,
                        $single->mobile,
                        $add_date
                    ));
                    $i++;
                }
                fclose($file_for_download_data);
                $this->param['SUCCESS']='TRUE';
                $this->param['file_full_url']=$file_full_url;
            }else{
                $this->param['SUCCESS']='FALSE';
            }
            echo json_encode($this->param,1);*/
        }else{
            $this->param['sr_start']=$sr_start;
            $this->param['all_list']=$all_list;
            $this->param['date_formate']=Config::get('constant.DATE_FORMATE');
            $html = view('backend.orders.list_post', $this->param)->render();

            $res['DATA'] = $html;
            $res['page_count'] = $page;
            $res['record_count']=$record_count;
            $res['sr_start']=$sr_start;
            $res['sr_end']=$sr_end;
            return json_encode($res,1);
        }
    }

    public function api_add_quote(Request $request){
        $User = new User();
        $Quotes = new Quotes();
        $QBCustomerTypes = new QBCustomerTypes();
        $Settings = new Settings();
        $QBController = new QBController();

        $validator = Validator::make($request->all(), [
            'shopify_customer_id' => 'required',
            'shopify_customer_email' => 'required',
            'shopify_customer_name' => 'required',
            'shopify_product_title' => 'required',
            'shopify_product_id' => 'required',
            'shopify_variant_id' => 'required'
        ]);
        if ($validator->fails()) {
            $msg = '';
            foreach($validator->errors()->getMessages() as $m){
                $msg = $m;
            }
            $res['SUCCESS'] = 'FALSE';
            $res['MESSAGE'] = $msg;
            echo json_encode($res,1);exit;
        }

        $qt_pre_exist = $Settings->select_field_by_key('QUOTE_NO_PREFIX');
        $qt_post_exist = $Settings->select_field_by_key('QUOTE_NO_POSTFIX');
        $max_quote_id = $Quotes->get_max_id();
        if(!empty($max_quote_id)){
            $quote_number = @$qt_pre_exist[0]->value.(($max_quote_id+1)+1000).@$qt_post_exist[0]->value;
        }else{
            $quote_number = @$qt_pre_exist[0]->value.(1+1000).@$qt_post_exist[0]->value;
        }

        $postData = $request->post();

        $user_data = $User->select_fields_by_email($postData['shopify_customer_email']);
        if(isset($user_data[0]->customer_ref) && !empty($user_data[0]->customer_ref)){
            $insert_arr = [
                'qb_customer_ref_id' => $user_data[0]->customer_ref,
                'quote_number' => $quote_number,
                'project_name' => @$postData['project_name'],
                'shape' => $postData['shape'],
                'material' => $postData['material'],
                'native_arm_pom_color' => $postData['native_arm_pom_color'],
                'own_arm_pom_color' => $postData['own_arm_pom_color'],
                'sku' => $postData['sku'],
                'estimated_price' => $postData['estimated_price'],
                'freight_price' => $postData['freight_price'],
                'pad_price' => $postData['pad_price'],
                'width_feet' => $postData['width_feet'],
                'width_inch' => $postData['width_inch'],
                'length_feet' => $postData['length_feet'],
                'length_inch' => $postData['length_inch'],
                'shopify_customer_id' => $postData['shopify_customer_id'],
                'shopify_customer_email' => $postData['shopify_customer_email'],
                'shopify_customer_name' => $postData['shopify_customer_name'],
                'shopify_product_title' => $postData['shopify_product_title'],
                'shopify_product_id' => $postData['shopify_product_id'],
                'shopify_variant_id' => $postData['shopify_variant_id'],
                'status' => 'Draft',
                'add_date' => time()
            ];
            $insert_id = $Quotes->insert_quote($insert_arr);

            //create estimate in QB
            $create_est_data = [
                "Line" => [
                    [
                        "Description" => "Quote number: ".$quote_number."\nPattern: ".$postData['shopify_product_title']."\nsku: ".$postData['sku']."\nshape: ".$postData['shape']."\nmaterial: ".$postData['material'],
                        "Amount" => floatval($postData['estimated_price']),
                        "DetailType" => "SalesItemLineDetail",
                        "SalesItemLineDetail" => [
                            "UnitPrice" => floatval($postData['estimated_price']),
                            "Qty" => 1,
                            "ItemRef" => [
                                "value" => "4"	//this is hardcoded productId of "Retorra custom rug" - https://app.qbo.intuit.com/app/item?itemId=4
                            ]
                        ]
                    ]
                ],
                "CustomerRef" => [
                    "value" => $user_data[0]->customer_ref
                ],
                "DocNumber" => $quote_number
            ];
            if(isset($postData['freight_price']) && !empty($postData['freight_price']) && floatval($postData['freight_price'])>0 ){
                $create_est_data['Line'][] = [
                    "Description" => "",
                    "Amount" => floatval($postData['freight_price']),
                    "DetailType" => "SalesItemLineDetail",
                    "SalesItemLineDetail" => [
                        "UnitPrice" => floatval($postData['freight_price']),
                        "Qty" => 1,
                        "ItemRef" => [
                            "value" => "5"	//this is hardcoded productId of "freight" - https://app.qbo.intuit.com/app/item?itemId=5
                        ]
                    ]
                ];
            }
            if(isset($postData['pad_price']) && !empty($postData['pad_price']) && floatval($postData['pad_price'])>0 ){
                $create_est_data['Line'][] = [
                    "Description" => "",
                    "Amount" => floatval($postData['pad_price']),
                    "DetailType" => "SalesItemLineDetail",
                    "SalesItemLineDetail" => [
                        "UnitPrice" => floatval($postData['pad_price']),
                        "Qty" => 1,
                        "ItemRef" => [
                            "value" => "15"	//this is hardcoded productId of "rug pad" - https://app.qbo.intuit.com/app/item?itemId=15
                        ]
                    ]
                ];
            }

            $create_estimate_res = $QBController->create_update_estimate($create_est_data);
            $create_estimate_res_arr = json_decode($create_estimate_res,1);
            if(isset($create_estimate_res_arr['SUCCESS']) && $create_estimate_res_arr['SUCCESS']=='TRUE'){
                $update_arr = [
                    'qb_estimate_id' => $create_estimate_res_arr['DATA']['Estimate']['Id'],
                    'qb_status' => $create_estimate_res_arr['DATA']['Estimate']['TxnStatus']
                ];
                $Quotes->update_quote($insert_id,$update_arr);

                //update custom fields in QB
                $CustomField_with_value = [];
                if(isset($create_estimate_res_arr['DATA']['Estimate']['CustomField'])){
                    foreach($create_estimate_res_arr['DATA']['Estimate']['CustomField'] as $cf){
                        if($cf['Name'] == 'Project Sidemark' && isset($postData['project_name']) && !empty($postData['project_name']) ){
                            $CustomField_with_value[] = [
                                "DefinitionId" => $cf['DefinitionId'],
                                "StringValue" => $postData['project_name'],
                                "Type" => "StringType"
                            ];
                        }else if($cf['Name'] == 'Customer Type'){
                            $CustomField_with_value[] = [
                                "DefinitionId" => $cf['DefinitionId'],
                                "StringValue" => $user_data[0]->role,
                                "Type" => "StringType"
                            ];
                        }else if($cf['Name'] == 'Assigned Showroom'){
                            if(!empty($user_data[0]->customer_type_ref)){
                                $user_customer_type_data = $QBCustomerTypes->select_field_by_customer_type_id($user_data[0]->customer_type_ref);
                                if(isset($user_customer_type_data[0]) && !empty($user_customer_type_data[0])){
                                    $CustomField_with_value[] = [
                                        "DefinitionId" => $cf['DefinitionId'],
                                        "StringValue" => $user_customer_type_data[0]->name,
                                        "Type" => "StringType"
                                    ];
                                }
                            }
                        }
                    }
                }
                if(!empty($CustomField_with_value)){
                    $update_est_data = [
                        "Id" => $create_estimate_res_arr['DATA']['Estimate']['Id'],
                        "SyncToken" => $create_estimate_res_arr['DATA']['Estimate']['SyncToken'],
                        "sparse" => true,
                        "CustomField" => $CustomField_with_value
                    ];
                    $QBController->create_update_estimate($update_est_data);
                }

                //send email to Admin
                $QBController->send_estimate($create_estimate_res_arr['DATA']['Estimate']['Id'],Config::get('constant.CLIENT_ADMIN_EMAIL'));

                $res['SUCCESS'] = 'TRUE';
                $res['MESSAGE'] = "Your quote has been submitted successfully. We will contact you soon.";
                $res['insert_id'] = $insert_id;
                $res['qb_estimate_id'] = $create_estimate_res_arr['DATA']['Estimate']['Id'];
            }else{
                $res['SUCCESS'] = 'FALSE';
                $res['MESSAGE'] = $create_estimate_res_arr['MESSAGE'];
            }
        }else{
            $res['SUCCESS'] = 'FALSE';
            $res['MESSAGE'] = "Your email is not existed in quickbook.";
        }
        echo json_encode($res,1);
    }
    public function api_get_quote_list($customer_id){
        $Quotes = new Quotes();
        $InexController = new InexController();

        $record_count=0;
        $page=0;
        $current_page=1;
        $rows='10';
        $keyword='';

        if( (isset($_REQUEST['rows']))&&(!empty($_REQUEST['rows'])) ){
            $rows=$_REQUEST['rows'];
        }
        if( (isset($_REQUEST['keyword']))&&(!empty($_REQUEST['keyword'])) ){
            $keyword=$InexController->allow_special_character_in_keyword($_REQUEST['keyword']);
        }
        if( (isset($_REQUEST['page']))&&(!empty($_REQUEST['page'])) ){
            $current_page=$_REQUEST['page'];
        }
        $start=($current_page-1)*$rows;
        $end=$rows;
        $sort_field = '';
        if(isset($_POST['sort_field']) && !empty($_POST['sort_field'])){
            $sort_field = $_POST['sort_field'];
        }
        $this->param['sort_field']=$sort_field;
        $sort_type = '';
        if(isset($_POST['sort_type']) && !empty($_POST['sort_type'])){
            $sort_type = $_POST['sort_type'];
        }
        $this->param['sort_type']=$sort_type;


        /*if(isset($_POST['date_range_filter']) && !empty($_POST['date_range_filter'])){
            $dr_arr = explode(' To ',$_POST['date_range_filter']);
            if(isset($dr_arr[0]) && !empty($dr_arr[0]) && isset($dr_arr[1]) && !empty($dr_arr[1]) ){
                $start_ts = strtotime($dr_arr[0].' 0:0');
                $end_ts = strtotime($dr_arr[1].' 23:59');
                $User->set_start_date($start_ts);
                $User->set_end_date($end_ts);
            }
        }*/

        $Quotes->set_shopify_customer_id($customer_id);
        $all_count = $Quotes->count_all($keyword);
        $all_list = $Quotes->select_all($start,$end,$keyword,$sort_field,$sort_type);

        if( (isset($all_count[0]->count))&&(!empty($all_count[0]->count)) ){
            $record_count=$all_count[0]->count;
            $page=$record_count/$rows;
            $page=ceil($page);
        }
        $keyword=$InexController->remove_special_character_in_keyword($keyword);
        $sr_start=0;
        if($record_count>=1){
            $sr_start=(($current_page-1)*$rows)+1;
        }
        $sr_end=($current_page)*$rows;
        if($record_count<=$sr_end){
            $sr_end=$record_count;
        }

        $res['DATA'] = $all_list;
        $res['page_count'] = $page;
        $res['record_count']=$record_count;
        $res['sr_start']=$sr_start;
        $res['sr_end']=$sr_end;
        return json_encode($res,1);
    }
    public function api_download_quote_pdf($customer_id,$qb_estimate_id){
        $Quotes = new Quotes();
        $QBController = new QBController();

        $db_quote_exist = $Quotes->select_data_by_shopify_customer_id_and_qb_estimate_id($customer_id,$qb_estimate_id);
        if(!empty($db_quote_exist)){
            $QBController->download_estimate($qb_estimate_id);
            exit;
        }else{
            $res['SUCCESS'] = 'FALSE';
            $res['MESSAGE'] = "Invalid quote/estimate Id.";
        }
        echo json_encode($res,1);
    }

    public function cronjob_check_estimate_status(){
        $Quotes = new Quotes();
        $QBController = new QBController();
        $data_for_status_cron = $Quotes->select_data_for_status_cron();
        if(!empty($data_for_status_cron)){
            $headers = array(
                'X-Shopify-Access-Token' => Config::get('constant.SHOPIFY_API_TOKEN')
                //'X-Shopify-Storefront-Access-Token' => $storefront_access_token
            );
            $GraphqlController = new GraphqlController(Config::get('constant.SHOPIFY_STORE'), $headers, false); //pass true for store front apis

            foreach($data_for_status_cron as $quote){
                $estimate_res = $QBController->get_estimate($quote->qb_estimate_id);
                $estimate_data = json_decode($estimate_res,1);
                if(isset($estimate_data['DATA']['Estimate']['TxnStatus'])){
                    $update_arr = [
                        'qb_status' => $estimate_data['DATA']['Estimate']['TxnStatus']
                    ];
                    $Quotes->update_quote($quote->id,$update_arr);

                    //if estimate is paid, then create order in shopify
                    if($estimate_data['DATA']['Estimate']['TxnStatus'] == 'Paid'){
                        //create shopify order
                        $mutation = 'mutation draftOrderCreate($input: DraftOrderInput!) {
                          draftOrderCreate(input: $input) {
                            draftOrder { id }
                          }
                        }';
                        $input_arr = [
                            "input" =>  [
                                "purchasingEntity" => [
                                    "customerId" => "gid://shopify/Customer/".$quote->shopify_customer_id
                                ],
                                "useCustomerDefaultAddress" => true,
                                "note" => "",
                                //"email" => $quote->shopify_customer_email,
                                "taxExempt" => true,
                                //"tags" => [ "foo", "bar" ],
                                "lineItems" => [
                                    [
                                        "title" => $quote->shopify_product_title,
                                        "originalUnitPrice" => $estimate_data['DATA']['Estimate']['Line'][0]['Amount'],
                                        "quantity" => 1,
                                        "customAttributes" => []
                                    ]/*,
                                    [
                                        "variantId" => "gid =>//shopify/ProductVariant/43729076",
                                        "quantity" => 2
                                    ]*/
                                ],
                                "customAttributes" => []
                            ]
                        ];
                        if(isset($quote->shopify_product_id) && !empty($quote->shopify_product_id)){
                            $input_arr['input']['lineItems'][0]['customAttributes'][] = [ "key" => "Shopify Product ID", "value" => $quote->shopify_product_id ];
                        }
                        if(isset($quote->shopify_variant_id) && !empty($quote->shopify_variant_id)){
                            $input_arr['input']['lineItems'][0]['customAttributes'][] = [ "key" => "Shopify Variant ID", "value" => $quote->shopify_variant_id ];
                        }
                        if(isset($quote->shape) && !empty($quote->shape)){
                            $input_arr['input']['lineItems'][0]['customAttributes'][] = [ "key" => "Shape", "value" => $quote->shape ];
                        }
                        if(isset($quote->material) && !empty($quote->material)){
                            $input_arr['input']['lineItems'][0]['customAttributes'][] = [ "key" => "Material", "value" => $quote->material ];
                        }
                        if(isset($quote->sku) && !empty($quote->sku)){
                            $input_arr['input']['lineItems'][0]['customAttributes'][] = [ "key" => "SKU", "value" => $quote->sku ];
                        }

                        if(isset($quote->quote_number) && !empty($quote->quote_number)){
                            $input_arr['input']['customAttributes'][] = [ "key" => "Quote number", "value" => $quote->quote_number ];
                        }
                        if(isset($quote->qb_estimate_id) && !empty($quote->qb_estimate_id)){
                            $input_arr['input']['customAttributes'][] = [ "key" => "QB Estimate ID", "value" => $quote->qb_estimate_id ];
                        }
                        $draftOrderCreateRes = $GraphqlController->runByMutation($mutation,json_encode($input_arr,1));

                        if(isset($draftOrderCreateRes['data']['draftOrderCreate']['draftOrder']['id'])){
                            //complete draftorder ro create order
                            $mutation = 'mutation draftOrderComplete($id: ID!) {
                              draftOrderComplete(id: $id) { draftOrder { order { id } } }
                            }';
                            $input_arr = [
                                "id" => $draftOrderCreateRes['data']['draftOrderCreate']['draftOrder']['id']
                            ];
                            $draftOrderCompleteRes = $GraphqlController->runByMutation($mutation,json_encode($input_arr,1));
                            if(isset($draftOrderCompleteRes['data']['draftOrderComplete']['draftOrder']['order']['id'])){
                                $update_arr = [
                                    'shopify_order_id' => str_replace('gid://shopify/Order/','',$draftOrderCompleteRes['data']['draftOrderComplete']['draftOrder']['order']['id']),
                                    'status' => 'Order-Placed',
                                    'modify_date' => time()
                                ];
                                $Quotes->update_quote($quote->id,$update_arr);
                            }else{
                                $update_arr = [
                                    'message' => json_encode($draftOrderCompleteRes,1)
                                ];
                                $Quotes->update_quote($quote->id,$update_arr);
                            }
                        }else{
                            $update_arr = [
                                'message' => json_encode($draftOrderCreateRes,1)
                            ];
                            $Quotes->update_quote($quote->id,$update_arr);
                        }
                    }
                }

            }
        }else{
            echo 'No records found.';
        }
    }

}

