<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\ProgramFactory;
use App\Modules\Identity\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'code', 'name', 'total_credits_required', 'duration_years'];

    protected static function newFactory(): ProgramFactory
    {
        return ProgramFactory::new();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Khung chuong trinh: hoc phan kem hoc ky goi y va bat buoc/tu chon.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot(['suggested_semester_no', 'type'])
            ->withTimestamps();
    }
}
