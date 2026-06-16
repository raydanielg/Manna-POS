<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $table = 'backups';
    protected $fillable = ['name','file_path','type','size','status','notes','created_by'];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
