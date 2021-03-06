<?php

namespace App\Models;

use App\Models\Contracts\HasCommentable;
use App\Models\Contracts\HasLikeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $title (max length 256)
 * @property string $description (max length 512)
 *
 * @property DateTime $publishing_start
 * @property DateTime $publishing_end
 * @property PublicationStatus $publicationStatus
 * @property Author $author
 * @property ComicPoster $comicPoster
 * @property ComicHeaderBackground $comicHeaderBackground
 * @property ?Episode $cachedLatestViewedEpisode
 */
class Comic extends Model implements HasCommentable, HasLikeable
{
    use HasFactory,
        Concerns\HasCommentable,
        Concerns\HasLikeable,
        Concerns\HasSlugColumn;

    protected $casts = [
        'publishing_start' => 'datetime',
        'publishing_end' => 'datetime',
    ];

    protected $slugSource = ['title', '-by-', 'author.full_name'];

    public function publicationStatus()
    {
        return $this->belongsTo(PublicationStatus::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function comicPoster()
    {
        return $this->hasOne(ComicPoster::class);
    }

    public function comicHeaderBackground()
    {
        return $this->hasOne(ComicHeaderBackground::class);
    }

    public function comicTags()
    {
        return $this->belongsToMany(ComicTag::class)->withTimestamps();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function cacheLatestViewedEpisodeByRequestUser()
    {
        return $this->hasOne(CacheLatestViewedEpisodeByUser::class)->whereUser(request()->user() ?? -1);
    }

    public function latestEpisode()
    {
        return $this->hasOne(Episode::class)->latestOfMany();
    }

    public function characterRoles()
    {
        return $this->hasMany(CharacterRole::class);
    }

    public function comicUserListEntries()
    {
        return $this->hasMany(ComicUserListEntry::class);
    }
}
