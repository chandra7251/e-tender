<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InstansiSetting extends Model {
    protected $fillable = ['key','value','type'];
    public static function get(string $key, mixed $default = null): mixed {
        $s = static::where('key',$key)->first();
        if (!$s) return $default;
        return match($s->type) {
            'boolean' => filter_var($s->value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($s->value, true),
            default   => $s->value,
        };
    }
    public static function set(string $key, mixed $value): void {
        static::updateOrCreate(['key'=>$key],['value'=>is_array($value)?json_encode($value):(string)$value]);
    }
    public static function allAsArray(): array {
        return static::all()->pluck('value','key')->toArray();
    }
}
