<?php

namespace App\Modules\Academic\Models;

use App\Modules\Academic\Database\Factories\DepartmentFactory;
use App\Modules\Identity\Models\Lecturer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description'];

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }
}
