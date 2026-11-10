<?php

namespace App\Modules\Identity\Models;

use App\Modules\Academic\Models\Program;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Identity\Database\Factories\StudentFactory;
use App\Shared\Enums\Gender;
use App\Shared\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $student_code
 * @property int $program_id
 * @property int $cohort_year
 * @property StudentStatus $status
 * @property Gender|null $gender
 * @property Carbon|null $date_of_birth
 * @property string|null $phone
 * @property string|null $address
 * @property-read User $user
 * @property-read Program|null $program
 * @property-read Collection<int, Enrollment> $enrollments
 */
class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'student_code', 'program_id', 'cohort_year', 'date_of_birth',
        'gender', 'phone', 'address', 'status',
    ];

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'cohort_year' => 'integer',
            'gender' => Gender::class,
            'status' => StudentStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Program, $this> */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /** @return HasMany<Enrollment, $this> */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
