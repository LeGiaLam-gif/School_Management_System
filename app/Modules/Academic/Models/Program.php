<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\ProgramFactory;
use App\Modules\Identity\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $department_id
 * @property string $code
 * @property string $name
 * @property int $total_credits_required
 * @property int $duration_years
 * @property-read Department|null $department
 * @property-read Collection<int, Student> $students
 * @property-read Collection<int, Course> $courses
 */
class Program extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'code', 'name', 'total_credits_required', 'duration_years'];

    protected static function newFactory(): ProgramFactory
    {
        return ProgramFactory::new();
    }

    /** @return BelongsTo<Department, $this> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return HasMany<Student, $this> */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Khung chuong trinh: hoc phan kem hoc ky goi y va bat buoc/tu chon.
     */
    /** @return BelongsToMany<Course, $this> */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot(['suggested_semester_no', 'type'])
            ->withTimestamps();
    }
}
