?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductCategory extends Model {
    protected $table = "product_categories";
    protected $fillable = ["name","description","parent_id","status"];
    public function parent() { return $this->belongsTo(ProductCategory::class,"parent_id"); }
    public function children() { return $this->hasMany(ProductCategory::class,"parent_id"); }
    public function products() { return $this->hasMany(Product::class,"category_id"); }
}
