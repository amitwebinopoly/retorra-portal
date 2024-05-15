<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QBCustomerTypes extends Model{
    protected $table = 'qb_customer_types';

    public function insert_qb_customer_type($arr){
        return DB::table($this->table)
            ->insertGetId($arr);
    }
    public function update_qb_customer_type($id,$arr){
        return DB::table($this->table)
            ->where('id',$id)
            ->update($arr);
    }
    public function delete_qb_customer_type($id){
        return DB::table($this->table)
            ->where('id',$id)
            ->delete();
    }
    public function select_field_by_id($id,$fields='*'){
        return DB::table($this->table)
            ->select($fields)
            ->where('id',$id)
            ->get()->toArray();
    }
    public function select_field_by_name($name,$fields='*'){
        return DB::table($this->table)
            ->select($fields)
            ->where('name',$name)
            ->get()->toArray();
    }
}
