<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'subject_type',
        'subject_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values'   => 'array',
            'new_values'   => 'array',
            'performed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity.
     */
    public static function log(
        string  $action,
        string  $module,
        string  $description,
        ?int    $userId = null,
        ?string $subjectType = null,
        ?int    $subjectId = null,
        ?array  $oldValues = null,
        ?array  $newValues = null,
    ): self {
        return self::create([
            'user_id'      => $userId ?? auth()->id(),
            'action'       => $action,
            'module'       => $module,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }
}
