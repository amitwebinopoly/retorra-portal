<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Quotes extends Model{
    protected $table = 'quotes';

    protected $shopify_customer_id = '';
    public function set_shopify_customer_id($val){ $this->shopify_customer_id=$val; }

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
            /*$cond_keyword = "AND (
                    cu_name LIKE '%$keyword%' OR
                    cu_email LIKE '%$keyword%' OR
                    cu_mobile LIKE '%$keyword%' OR
                    cu_subject LIKE '%$keyword%'
                )"; */
        }
        /*$cond_start_end = "";
        if(isset($this->start_date) && !empty($this->start_date) && isset($this->end_date) && !empty($this->end_date)){
            $cond_start_end = "AND cu_add_date BETWEEN ".$this->start_date." AND ".$this->end_date."";
        }*/

        $cond_shopify_customer_id = "";
        if(isset($this->shopify_customer_id) && !empty($this->shopify_customer_id) ){
            $cond_shopify_customer_id = "AND shopify_customer_id = '".$this->shopify_customer_id."'";
        }

        $sql="SELECT count(id) as count
                FROM `$this->table`
                WHERE 1
                $cond_keyword
                $cond_shopify_customer_id
            ";
        $results = DB::select( $sql );
        return $results;
    }
    public function select_all($start,$end,$keyword,$sort_field='',$sort_type=''){
        $cond_keyword = '';
        if(isset($keyword) && !empty($keyword)){
            /*$cond_keyword = "AND (
                    cu_name LIKE '%$keyword%' OR
                    cu_email LIKE '%$keyword%' OR
                    cu_mobile LIKE '%$keyword%' OR
                    cu_subject LIKE '%$keyword%'
                )"; */
        }
        /*$cond_start_end = "";
        if(isset($this->start_date) && !empty($this->start_date) && isset($this->end_date) && !empty($this->end_date)){
            $cond_start_end = "AND cu_add_date BETWEEN ".$this->start_date." AND ".$this->end_date."";
        }*/

        $cond_shopify_customer_id = "";
        if(isset($this->shopify_customer_id) && !empty($this->shopify_customer_id) ){
            $cond_shopify_customer_id = "AND shopify_customer_id = '".$this->shopify_customer_id."'";
        }

        $cond_order = 'ORDER BY id DESC';
        if(!empty($sort_field)){
            $cond_order = 'ORDER BY '.$sort_field.' '.$sort_type;
        }

        $sql="
                SELECT id, quote_number, shape, ars_pom_color_1, ars_pom_color_2, ars_pom_color_3,
                width_feet, width_inch, length_feet, length_inch, status, add_date
                FROM `$this->table`
                WHERE 1
                $cond_keyword
                $cond_shopify_customer_id
                $cond_order
                LIMIT $start,$end
            ";
        $results = DB::select( $sql );
        return $results;
    }

    public function select_data_by_id($id){
        $sql="SELECT * FROM `$this->table` WHERE id = '$id' ";
        $results = DB::select( DB::raw($sql) );
        return $results;
    }
}
