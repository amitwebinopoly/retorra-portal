<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Quotes extends Model{
    protected $table = 'quotes';

    protected $shopify_customer_id = '';
    public function set_shopify_customer_id($val){ $this->shopify_customer_id=$val; }
    protected $qb_customer_ref_ids = '';
    public function set_qb_customer_ref_ids($val){ $this->qb_customer_ref_ids=$val; }
    protected $status = '';
    public function set_status($val){ $this->status=$val; }

    public function insert_quote($arr){
        return DB::table($this->table)
            ->insertGetId($arr);
    }
    public function update_quote($id,$arr){
        return DB::table($this->table)
            ->where('id',$id)
            ->update($arr);
    }
    public function delete_quote($id){
        return DB::table($this->table)
            ->where('id',$id)
            ->delete();
    }
    public function get_max_id(){
        return DB::table($this->table)
            ->max('id');
    }

    public function count_all($keyword){
        $cond_keyword = '';
        if(isset($keyword) && !empty($keyword)){
            $cond_keyword = "AND (
                shopify_customer_email LIKE '%$keyword%' OR
                shopify_customer_name LIKE '%$keyword%' OR
                shopify_product_title LIKE '%$keyword%' OR
                quote_number LIKE '%$keyword%' OR
                shopify_order_id = '$keyword' OR
                qb_estimate_id = '$keyword'
            )";
        }
        /*$cond_start_end = "";
        if(isset($this->start_date) && !empty($this->start_date) && isset($this->end_date) && !empty($this->end_date)){
            $cond_start_end = "AND cu_add_date BETWEEN ".$this->start_date." AND ".$this->end_date."";
        }*/

        $cond_shopify_customer_id = "";
        if(isset($this->shopify_customer_id) && !empty($this->shopify_customer_id) ){
            $cond_shopify_customer_id = "AND shopify_customer_id = '".$this->shopify_customer_id."'";
        }
        $cond_qb_customer_ref_ids = "";
        if(isset($this->qb_customer_ref_ids) && !empty($this->qb_customer_ref_ids) ){
            $cond_qb_customer_ref_ids = "AND qb_customer_ref_id IN (".$this->qb_customer_ref_ids.")";
        }
        $cond_status = "";
        if(isset($this->status) && !empty($this->status) ){
            $cond_status = "AND status = '".$this->status."'";
        }

        $sql="SELECT count(id) as count
                FROM `$this->table`
                WHERE 1
                $cond_keyword
                $cond_shopify_customer_id
                $cond_qb_customer_ref_ids
                $cond_status
            ";
        $results = DB::select( $sql );
        return $results;
    }
    public function select_all($start,$end,$keyword,$sort_field='',$sort_type=''){
        $cond_keyword = '';
        if(isset($keyword) && !empty($keyword)){
            $cond_keyword = "AND (
                shopify_customer_email LIKE '%$keyword%' OR
                shopify_customer_name LIKE '%$keyword%' OR
                shopify_product_title LIKE '%$keyword%' OR
                quote_number LIKE '%$keyword%' OR
                shopify_order_id = '$keyword' OR
                qb_estimate_id = '$keyword'
            )";
        }
        /*$cond_start_end = "";
        if(isset($this->start_date) && !empty($this->start_date) && isset($this->end_date) && !empty($this->end_date)){
            $cond_start_end = "AND cu_add_date BETWEEN ".$this->start_date." AND ".$this->end_date."";
        }*/

        $cond_shopify_customer_id = "";
        if(isset($this->shopify_customer_id) && !empty($this->shopify_customer_id) ){
            $cond_shopify_customer_id = "AND shopify_customer_id = '".$this->shopify_customer_id."'";
        }
        $cond_qb_customer_ref_ids = "";
        if(isset($this->qb_customer_ref_ids) && !empty($this->qb_customer_ref_ids) ){
            $cond_qb_customer_ref_ids = "AND qb_customer_ref_id IN (".$this->qb_customer_ref_ids.")";
        }
        $cond_status = "";
        if(isset($this->status) && !empty($this->status) ){
            $cond_status = "AND status = '".$this->status."'";
        }

        $cond_order = 'ORDER BY id DESC';
        if(!empty($sort_field)){
            $cond_order = 'ORDER BY '.$sort_field.' '.$sort_type;
        }

        $sql="
                SELECT id, quote_number, project_name, qb_estimate_id, shopify_product_title,
                shopify_customer_id, shopify_customer_email, shopify_customer_name,
                shape, material, native_arm_pom_color, own_arm_pom_color, sku,
                width_feet, width_inch, length_feet, length_inch,
                shopify_order_id, qb_status, status, add_date
                FROM `$this->table`
                WHERE 1
                $cond_keyword
                $cond_shopify_customer_id
                $cond_qb_customer_ref_ids
                $cond_status

                $cond_order
                LIMIT $start,$end
            ";
        $results = DB::select( $sql );
        return $results;
    }

    public function select_data_by_id($id){
        $sql="SELECT * FROM `$this->table` WHERE id = '$id' ";
        $results = DB::select( $sql );
        return $results;
    }
    public function select_data_by_shopify_customer_id_and_qb_estimate_id($shopify_customer_id,$qb_estimate_id){
        $sql="SELECT * FROM `$this->table` WHERE shopify_customer_id = '$shopify_customer_id' AND qb_estimate_id = '$qb_estimate_id' ";
        $results = DB::select( $sql );
        return $results;
    }
    public function select_data_for_status_cron(){
        $sql="SELECT * FROM `$this->table` WHERE qb_estimate_id != '' AND qb_status NOT IN ('Declined','Closed','Paid')";
        $results = DB::select( $sql );
        return $results;
    }
}
