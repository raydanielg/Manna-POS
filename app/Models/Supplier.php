?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Supplier extends Model {
    protected $fillable = ['name','company','email','phone','address','city','country','tax_number','pay_term','credit_limit','balance','status','notes'];
    public function purchases() { return $this->hasMany(Purchase::class); }
}
