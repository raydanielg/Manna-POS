<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $table = 'sms_templates';
    protected $fillable = ['name','code','message','variables','category','is_active'];
    protected $casts = ['variables'=>'array','is_active'=>'boolean'];
}
