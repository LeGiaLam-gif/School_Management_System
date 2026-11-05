<?php

namespace App\Shared\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Diem ghi audit duy nhat. Service goi truc tiep cho hanh dong co ly do
 * (mo khoa diem, ghi de quy tac dang ky...), con thay doi thuoc tinh model
 * duoc ghi tu dong qua trait Auditable.
 */
class AuditLogger
{
    public function __construct(private readonly Request $request) {}

    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    public function record(
        string $action,
        ?Model $auditable = null,
        ?array $old = null,
        ?array $new = null,
        ?string $reason = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'reason' => $reason,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent() !== null
                ? mb_substr($this->request->userAgent(), 0, 255)
                : null,
        ]);
    }
}
