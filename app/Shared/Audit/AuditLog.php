<?php

namespace App\Shared\Audit;

use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LogicException;

/**
 * Nhat ky thay doi du lieu nhay cam (NFR-SEC-04, BR-10).
 *
 * Append-only: khong cap nhat, khong xoa; chi co `created_at`.
 * Phan GHI nam o Shared (moi module deu can ghi tu Phase 1); module Reporting chi DOC.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'action', 'auditable_type', 'auditable_id',
        'old_values', 'new_values', 'reason', 'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): never {
            throw new LogicException('audit_logs là append-only, không được cập nhật.');
        });

        static::deleting(function (): never {
            throw new LogicException('audit_logs là append-only, không được xóa.');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
