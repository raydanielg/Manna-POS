<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';
    protected $fillable = ['name','subject','code','body','variables','category','is_active'];
    protected $casts = ['variables'=>'array','is_active'=>'boolean'];
}
