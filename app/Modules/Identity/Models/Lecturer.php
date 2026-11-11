<?php

namespace App\Modules\Identity\Models;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Department;
use App\Modules\Identity\Database\Factories\LecturerFactory;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $lecturer_code
 * @property int $department_id
 * @property string|null $academic_title
 * @property string|null $phone
 * @property string|null $office
 * @property string|null $address
 * @property-read User $user
 * @property-read Department $department
 * @property-read Collection<int, ClassSection> $classSections
 * @property-read Collection<int, AdministrativeClass> $advisedClasses
 */
class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'lecturer_code', 'department_id', 'academic_title', 'phone', 'office', 'address',
    ];

    protected static function newFactory(): LecturerFactory
    {
        return LecturerFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function classSections(): HasMany
    {
        return $this->hasMany(ClassSection::class);
    }

    /** @return HasMany<AdministrativeClass, $this> */
    public function advisedClasses(): HasMany
    {
        return $this->hasMany(AdministrativeClass::class, 'advisor_lecturer_id');
    }
}
