<?php

namespace App\Models\Explorer\Communities;

use App\Contracts\Explorer\Circleable;
use App\Contracts\Explorer\Locatable;
use App\Traits\HasCircle;
use App\Traits\HasLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationCommunity extends Model implements Circleable, Locatable
{
    use HasCircle, HasLocation, SoftDeletes;

    protected $guarded = [];
}
