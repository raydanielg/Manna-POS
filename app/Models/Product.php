?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['name','sku','barcode','brand_id','category_id','unit_id','tax_rate_id','description','image','purchase_price','selling_price','stock_quantity','reorder_level','status'];
    public function brand()    { return $this->belongsTo(Brand::class); }
    public function category() { return $this->belongsTo(ProductCategory::class,'category_id'); }
    public function unit()     { return $this->belongsTo(Unit::class); }
    public function taxRate()  { return $this->belongsTo(TaxRate::class); }
}
