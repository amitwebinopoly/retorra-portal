<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $table = 'users';

    public function insert_user($arr){
        return DB::table($this->table)
            ->insertGetId($arr);
    }
    public function update_user($id,$arr){
        return DB::table($this->table)
            ->where('id',$id)
            ->update($arr);
    }

    public function check_admin_for_login($email){
        return DB::table($this->table)
            ->select('id','password')
            ->where('email',$email)
            ->get()->toArray();
    }
    public function select_fields_by_id($id,$fields='*'){
        return DB::table($this->table)
            ->select($fields)
            ->where('id',$id)
            ->get()->toArray();
    }
    public function select_fields_by_email($email,$fields='*'){
        return DB::table($this->table)
            ->select($fields)
            ->where('email',$email)
            ->get()->toArray();
    }

    public function count_all($keyword){
        $cond_keyword = '';
        if(isset($keyword) && !empty($keyword)){
            $cond_keyword = "AND (
                first_name LIKE '%$keyword%' OR
                last_name LIKE '%$keyword%' OR
                email LIKE '%$keyword%' OR
                role = '$keyword'
            )";
        }
        /*$cond_start_end = "";
        if(isset($this->start_date) && !empty($this->start_date) && isset($this->end_date) && !empty($this->end_date)){
            $cond_start_end = "AND cu_add_date BETWEEN ".$this->start_date." AND ".$this->end_date."";
        }*/


        $sql="SELECT count(id) as count
                FROM `$this->table`
                WHERE 1
                $cond_keyword
            ";
        $results = DB::select( $sql );
        return $results;
    }
    public function select_all($start,$end,$keyword,$sort_field='',$sort_type=''){
        $cond_keyword = '';
        if(isset($keyword) && !empty($keyword)){
            $cond_keyword = "AND (
                first_name LIKE '%$keyword%' OR
                last_name LIKE '%$keyword%' OR
                email LIKE '%$keyword%' OR
                role = '$keyword'
            )";
        }
        /*$cond_start_end = "";
        if(isset($this->start_date) && !empty($this->start_date) && isset($this->end_date) && !empty($this->end_date)){
            $cond_start_end = "AND cu_add_date BETWEEN ".$this->start_date." AND ".$this->end_date."";
        }*/

        $cond_order = 'ORDER BY id DESC';
        if(!empty($sort_field)){
            $cond_order = 'ORDER BY '.$sort_field.' '.$sort_type;
        }

        $sql="
                SELECT id, first_name, last_name, email, role
                FROM `$this->table`
                WHERE 1
                $cond_keyword
                $cond_order
                LIMIT $start,$end
            ";
        $results = DB::select( $sql );
        return $results;
    }
}
