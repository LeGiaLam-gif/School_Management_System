<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\DepartmentFactory;
use App\Modules\Identity\Models\Lecturer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Program> $programs
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Course> $courses
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lecturer> $lecturers
 */
class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description'];

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    /** @return HasMany<Program, $this> */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /** @return HasMany<Course, $this> */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /** @return HasMany<Lecturer, $this> */
    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }
}
