<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BusinessLocation extends Model {
    protected $fillable = ['name','address','city','country','phone','status'];
}
