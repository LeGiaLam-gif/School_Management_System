<?php

namespace App\Shared\Traits;

use App\Shared\Audit\AuditLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Tu dong ghi audit_logs khi model thay doi (muc 6.9).
 *
 * Mac dinh chi ghi `updated` va `deleted`. Model co the ghi de
 * auditEvents() de them `created`, va auditExcluded() de loai cot nhay cam.
 *
 * Luu y: upsert()/insert()/update hang loat KHONG kich hoat model event, nen
 * Service dung cac ham do phai goi AuditLogger::record() thu cong.
 *
 * @mixin Model
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        $events = (new static)->auditEvents();

        if (in_array('created', $events, true)) {
            static::created(function (Model $model): void {
                app(AuditLogger::class)->record(
                    static::auditAction($model, 'created'),
                    $model,
                    null,
                    $model->auditFilter($model->getAttributes()),
                );
            });
        }

        if (in_array('updated', $events, true)) {
            static::updated(function (Model $model): void {
                $new = $model->auditFilter($model->getChanges());
                if ($new === []) {
                    return;
                }

                $old = array_intersect_key($model->getOriginal(), $new);

                app(AuditLogger::class)->record(
                    static::auditAction($model, 'updated'),
                    $model,
                    $old,
                    $new,
                );
            });
        }

        if (in_array('deleted', $events, true)) {
            static::deleted(function (Model $model): void {
                app(AuditLogger::class)->record(
                    static::auditAction($model, 'deleted'),
                    $model,
                    $model->auditFilter($model->getOriginal()),
                );
            });
        }
    }

    /**
     * @return list<string>
     */
    public function auditEvents(): array
    {
        return ['updated', 'deleted'];
    }

    /**
     * @return list<string>
     */
    public function auditExcluded(): array
    {
        return ['password', 'remember_token', 'updated_at', 'created_at'];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function auditFilter(array $attributes): array
    {
        return array_diff_key($attributes, array_flip($this->auditExcluded()));
    }

    protected static function auditAction(Model $model, string $event): string
    {
        return $model->getTable().'.'.$event;
    }
}
