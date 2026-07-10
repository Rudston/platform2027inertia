<?php

namespace App\Enums\Explorer;

enum CommunityType: string
{
    case Organisation        = 'App\Models\Explorer\Communities\OrganisationCommunity';
    case Campaign            = 'App\Models\Explorer\Communities\Campaign';
    case Course              = 'App\Models\Explorer\Communities\CourseCommunity';
    case Event               = 'App\Models\Explorer\Communities\Event';
    case LocationCommunity   = 'App\Models\Explorer\Communities\LocationCommunity';
    case ThemeCommunity      = 'App\Models\Explorer\Communities\ThemeCommunity';

    public function modelClass(): string
    {
        return $this->value;
    }
}
