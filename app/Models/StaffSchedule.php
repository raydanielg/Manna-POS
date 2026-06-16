<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StaffSchedule extends Model
{
    protected $table = 'staff_schedules';
    protected $fillable = ['staff_id','day_of_week','start_time','end_time','is_working_day'];
    protected $casts = ['is_working_day'=>'boolean'];

    public function staff() { return $this->belongsTo(Staff::class); }
}
