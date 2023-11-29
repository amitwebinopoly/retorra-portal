<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Settings extends Model{
    protected $table = 'settings';

    public function insert_setting($arr){
        return DB::table($this->table)
            ->insertGetId($arr);
    }
    public function update_setting($id,$arr){
        return DB::table($this->table)
            ->where('id',$id)
            ->update($arr);
    }
    public function delete_setting($id){
        return DB::table($this->table)
            ->where('id',$id)
            ->delete();
    }
    public function select_field_by_key($key,$fields='*'){
        return DB::table($this->table)
            ->select($fields)
            ->where('key',$key)
            ->get()->toArray();
    }
}
