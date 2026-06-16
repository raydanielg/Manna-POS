<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendance';
    protected $fillable = ['staff_id','date','clock_in','clock_out','status','notes'];
    protected $casts = ['date'=>'date','clock_in'=>'datetime:H:i','clock_out'=>'datetime:H:i'];

    public function staff() { return $this->belongsTo(Staff::class); }
}
