<?php

namespace App\Modules\Identity\Models;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Identity\Database\Factories\UserFactory;
use App\Shared\Enums\RoleName;
use App\Shared\Enums\UserStatus;
use App\Shared\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Auditable, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'email', 'password', 'status', 'must_change_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Tao tai khoan moi cung ghi audit (doi status/role truy vet duoc).
     *
     * @return list<string>
     */
    public function auditEvents(): array
    {
        return ['created', 'updated', 'deleted'];
    }

    /**
     * Khong audit cot thay doi moi lan dang nhap (nhieu va khong co gia tri truy vet).
     *
     * @return list<string>
     */
    public function auditExcluded(): array
    {
        return ['password', 'remember_token', 'updated_at', 'created_at', 'last_login_at'];
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer(): HasOne
    {
        return $this->hasOne(Lecturer::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::Admin->value);
    }

    public function isLecturer(): bool
    {
        return $this->hasRole(RoleName::Lecturer->value);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(RoleName::Student->value);
    }
}
