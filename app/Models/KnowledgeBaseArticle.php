<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class KnowledgeBaseArticle extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'views',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'views' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function incrementViews(): void
    {
        // Debounce: only count one view per visitor per article per hour using cache
        $key = 'kb_view_' . $this->id . '_' . request()->ip();
        if (! Cache::has($key)) {
            Cache::put($key, true, now()->addHour());
            $this->increment('views');
        }
    }
}
