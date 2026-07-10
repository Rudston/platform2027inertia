<?php

namespace App\Models\Explorer\Communities;

use App\Contracts\Explorer\Circleable;
use App\Contracts\Explorer\Locatable;
use App\Models\Explorer\Course;
use App\Traits\HasCircle;
use App\Traits\HasLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCommunity extends Model implements Circleable, Locatable
{
    use HasCircle, HasLocation, SoftDeletes;

    protected $table = 'course_communities';

    protected $guarded = [];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_course_community'
        )->withTimestamps();
    }

    public function circleName(): string
    {
        return $this->name ?? 'Unnamed Course Community';
    }
}
