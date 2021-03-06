<?php

namespace App\Models;

use App\Models\Contracts\BelongsToAUser;
use App\Models\Contracts\HasLikeable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property ?Commentable $commentable
 * @property ?Comment $rootComment
 * @property ?Comment $parentComment
 */
class Comment extends Model implements BelongsToAUser, HasLikeable
{
    use HasFactory,
        Concerns\BelongsToAUser,
        Concerns\HasLikeable;

    protected $attributes = [
        'root_child_comments_cached_count' => 0,
    ];

    protected $fillable = [
        'comment_text',
    ];

    protected static function booted()
    {
        static::created(function (self $comment) {
            $comment->commentable()->increment('comments_cached_count');
        });

        static::deleted(function (self $comment) {
            $comment->commentable()->increment('comments_cached_count');
        });
    }

    public function commentable()
    {
        return $this->belongsTo(Commentable::class);
    }

    public function rootComment()
    {
        return $this->belongsTo(self::class, 'root_comment_id');
    }

    public function parentComment()
    {
        return $this->belongsTo(self::class, 'parent_comment_id');
    }

    public function isRoot()
    {
        return is_null($this->root_comment_id);
    }

    public function scopeRootsOnly(Builder $query)
    {
        return $query->whereNull('root_comment_id');
    }

    public function scopeWhereRoot(Builder $query, self|int $root)
    {
        return $query->where('root_comment_id', modelKey($root));
    }
}
