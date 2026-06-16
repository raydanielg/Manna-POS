<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
    protected $fillable = ['first_name','last_name','email','phone','user_id','role','department','position','salary','pay_type','hire_date','status','address','emergency_contact','emergency_phone'];
    protected $casts = ['salary'=>'decimal:2','hire_date'=>'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function attendance() { return $this->hasMany(StaffAttendance::class); }
    public function schedules() { return $this->hasMany(StaffSchedule::class); }
    public function getFullNameAttribute() { return "{$this->first_name} {$this->last_name}"; }
}
