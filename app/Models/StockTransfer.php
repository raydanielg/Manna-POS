?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockTransfer extends Model {
    protected $fillable = ['reference','from_location','to_location','transfer_date','status','notes'];
    protected $casts = ['transfer_date' => 'date'];
}
