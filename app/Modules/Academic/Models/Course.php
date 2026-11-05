<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\CourseFactory;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Hoc phan (mon hoc trong chuong trinh dao tao, co so tin chi).
 */
class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id', 'code', 'name', 'credits', 'theory_periods',
        'practice_periods', 'description', 'is_active',
    ];

    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'theory_periods' => 'integer',
            'practice_periods' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function classSections(): HasMany
    {
        return $this->hasMany(ClassSection::class);
    }

    /**
     * Cac hoc phan phai dat truoc khi duoc dang ky hoc phan nay.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'course_prerequisites', 'course_id', 'prerequisite_course_id');
    }

    /**
     * Cac hoc phan yeu cau hoc phan nay lam tien quyet.
     */
    public function requiredBy(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'course_prerequisites', 'prerequisite_course_id', 'course_id');
    }
}
