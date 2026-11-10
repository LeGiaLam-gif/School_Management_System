<?php

namespace App\Modules\Academic\Models;

use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $program_id
 * @property int $academic_year
 * @property int|null $advisor_lecturer_id
 * @property-read Program $program
 * @property-read Lecturer|null $advisorLecturer
 * @property-read Collection<int, Student> $students
 */
class AdministrativeClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'program_id',
        'academic_year',
        'advisor_lecturer_id',
    ];

    protected function casts(): array
    {
        return [
            'academic_year' => 'integer',
        ];
    }

    /** @return BelongsTo<Program, $this> */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /** @return BelongsTo<Lecturer, $this> */
    public function advisorLecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'advisor_lecturer_id');
    }

    /** @return HasMany<Student, $this> */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
