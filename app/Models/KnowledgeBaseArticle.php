<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $this->increment('views');
    }
}
