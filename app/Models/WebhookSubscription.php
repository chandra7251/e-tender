<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WebhookSubscription extends Model {
    protected $fillable = ['name','url','events','secret','is_active','last_triggered_at','failure_count'];
    protected function casts(): array { return ['events'=>'array','is_active'=>'boolean','last_triggered_at'=>'datetime']; }
}
