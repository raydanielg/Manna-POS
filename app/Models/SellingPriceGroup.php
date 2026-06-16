<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SellingPriceGroup extends Model {
    protected $fillable = ['name','description','percentage','type','status'];
}
