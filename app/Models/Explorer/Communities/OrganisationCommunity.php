<?php

namespace App\Models\Explorer\Communities;

use App\Contracts\Explorer\Circleable;
use App\Contracts\Explorer\Locatable;
use App\Models\Explorer\Organisation;
use App\Traits\HasCircle;
use App\Traits\HasLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganisationCommunity extends Model implements Circleable, Locatable
{
    use HasCircle, HasLocation, SoftDeletes;

    protected $table = 'organisation_communities';

    protected $guarded = [];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function circleName(): string
    {
        return $this->organisation?->name
            ?? $this->name
            ?? 'Unnamed Organisation';
    }
}
