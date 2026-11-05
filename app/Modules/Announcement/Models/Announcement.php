<?php

namespace App\Modules\Announcement\Models;

use App\Modules\Announcement\Database\Factories\AnnouncementFactory;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\AnnouncementScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id', 'class_section_id', 'scope', 'title', 'body', 'is_pinned', 'published_at',
    ];

    protected static function newFactory(): AnnouncementFactory
    {
        return AnnouncementFactory::new();
    }

    protected function casts(): array
    {
        return [
            'scope' => AnnouncementScope::class,
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }

    /**
     * Da den gio dang (published_at <= bay gio).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
