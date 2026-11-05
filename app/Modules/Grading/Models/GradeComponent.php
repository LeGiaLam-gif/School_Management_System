<?php

namespace App\Modules\Grading\Models;

use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeComponent extends Model
{
    protected $fillable = ['class_section_id', 'name', 'weight', 'max_score', 'sort_order'];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
            'max_score' => 'float',
            'sort_order' => 'integer',
        ];
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
