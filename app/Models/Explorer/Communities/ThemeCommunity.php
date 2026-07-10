<?php

namespace App\Models\Explorer\Communities;

use App\Contracts\Explorer\Circleable;
use App\Contracts\Explorer\Locatable;
use App\Models\Explorer\Theme;
use App\Traits\HasCircle;
use App\Traits\HasLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThemeCommunity extends Model implements Circleable, Locatable
{
    use HasCircle, HasLocation, SoftDeletes;

    protected $guarded = [];

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function circleName(): string
    {
        return $this->theme->name . " (" . $this->circle->locatable->circleNameShort() . ")";
    }

    public function circleDescription(): string
    {
       if ($this->circle->locatable->circleNameShort() == 'National') {
           return "This national-level community focuses on " . $this->theme->name;
       } elseif (str_contains($this->circle->locatable->circleNameShort(), 'Province')) {
           return "This provincial-level community focuses on " . $this->theme->name;
       } else {
           return "This community based in the ".$this->circle->locatable->circleNameShort()." focuses on " . $this->theme->name;
       }
    }
}
