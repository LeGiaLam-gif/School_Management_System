<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\SemesterFactory;
use App\Modules\Teaching\Models\ClassSection;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $academic_year
 * @property CarbonInterface $start_date
 * @property CarbonInterface $end_date
 * @property CarbonInterface|null $registration_open_at
 * @property CarbonInterface|null $registration_close_at
 * @property CarbonInterface|null $drop_deadline
 * @property int $min_credits
 * @property int $max_credits
 * @property bool $is_current
 * @property-read Collection<int, ClassSection> $classSections
 * @property-read Collection<int, AcademicWarning> $academicWarnings
 */
class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'academic_year', 'start_date', 'end_date',
        'registration_open_at', 'registration_close_at', 'drop_deadline',
        'min_credits', 'max_credits', 'is_current',
    ];

    protected static function newFactory(): SemesterFactory
    {
        return SemesterFactory::new();
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_open_at' => 'datetime',
            'registration_close_at' => 'datetime',
            'drop_deadline' => 'date',
            'min_credits' => 'integer',
            'max_credits' => 'integer',
            'is_current' => 'boolean',
        ];
    }

    /** @return HasMany<ClassSection, $this> */
    public function classSections(): HasMany
    {
        return $this->hasMany(ClassSection::class);
    }

    /** @return HasMany<AcademicWarning, $this> */
    public function academicWarnings(): HasMany
    {
        return $this->hasMany(AcademicWarning::class);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    /**
     * Dot dang ky dang mo? (ap dung cho RegistrationWindowRule, muc 6.5)
     */
    public function isRegistrationOpen(?CarbonInterface $at = null): bool
    {
        $at ??= now();

        return $this->registration_open_at !== null
            && $this->registration_close_at !== null
            && $at->greaterThanOrEqualTo($this->registration_open_at)
            && $at->lessThan($this->registration_close_at);
    }

    /**
     * Con trong han rut hoc phan? (FR-ENR-03)
     */
    public function canDropOn(?CarbonInterface $at = null): bool
    {
        $at ??= now();

        return $this->drop_deadline !== null
            && $at->copy()->startOfDay()->lessThanOrEqualTo($this->drop_deadline);
    }
}
