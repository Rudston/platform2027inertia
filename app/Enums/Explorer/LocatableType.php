<?php

namespace App\Enums\Explorer;

enum LocatableType: string
{
    case Country              = 'App\Models\Explorer\Demography\Country';
    case Province             = 'App\Models\Explorer\Demography\Province';
    case DistrictMunicipality = 'App\Models\Explorer\Demography\DistrictMunicipality';
    case LocalMunicipality    = 'App\Models\Explorer\Demography\LocalMunicipality';
    case MainPlace            = 'App\Models\Explorer\Demography\MainPlace';
    case City                 = 'App\Models\Explorer\Demography\City';

    public function modelClass(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return match ($this) {
            self::Country              => __('geographic.level.country'),
            self::Province             => __('geographic.level.province'),
            self::DistrictMunicipality => __('geographic.level.district_municipality'),
            self::LocalMunicipality    => __('geographic.level.local_municipality'),
            self::MainPlace            => __('geographic.level.main_place'),
            self::City                 => __('geographic.level.city'),
        };
    }

    /**
     * Country-agnostic level for this SA-specific type, so callers can reason
     * about geography generically (e.g. $locatableType->locationLevel()).
     */
    public function locationLevel(): LocationLevel
    {
        return match ($this) {
            self::Country              => LocationLevel::Country,
            self::Province             => LocationLevel::Region,
            self::DistrictMunicipality => LocationLevel::District,
            self::LocalMunicipality    => LocationLevel::Local,
            self::City                 => LocationLevel::City,
            self::MainPlace            => LocationLevel::Place,
        };
    }

    /** Convenience proxy: is this the terminal (bottom) geographic level? */
    public function isTerminal(): bool
    {
        return $this->locationLevel()->isTerminal();
    }
}
