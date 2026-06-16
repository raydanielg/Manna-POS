<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Shipment extends Model {
    protected $fillable = ['reference','sale_id','recipient_name','shipping_address','carrier','tracking_number','ship_date','expected_delivery','status','notes'];
}
