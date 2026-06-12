<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Discount extends Model {
    protected $fillable = ['name','amount','type','starts_at','ends_at','status'];
    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date'];
}
