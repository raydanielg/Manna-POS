<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'system_configs';
    protected $fillable = ['key','value','group','type','description','is_encrypted'];
    protected $casts = ['is_encrypted'=>'boolean'];

    public static function getValue($key, $default = null) {
        $config = static::where('key', $key)->first();
        return $config ? $config->value : $default;
    }

    public static function setValue($key, $value, $group = 'general') {
        return static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
    }

    public function scopeGroup($q, $group) { return $q->where('group', $group); }
}
