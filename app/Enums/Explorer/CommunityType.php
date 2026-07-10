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

    /** Resolve a case from its short name (e.g. 'Campaign'); null if unknown. */
    public static function fromName(?string $name): ?self
    {
        if ($name === null || $name === '') {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    /** Emoji icon for this type (single source; was duplicated across views). */
    public function icon(): string
    {
        return match ($this) {
            self::LocationCommunity => '📍',
            self::Organisation      => '🏛',
            self::Campaign          => '📢',
            self::Course            => '🎓',
            self::Event             => '📅',
            self::ThemeCommunity    => '💡',
        };
    }

    /** Localised singular label (e.g. "Campaign"). */
    public function singular(): string
    {
        return __('communities.singular.'.match ($this) {
            self::LocationCommunity => 'location',
            self::Organisation      => 'organisation',
            self::Campaign          => 'campaign',
            self::Course            => 'course',
            self::Event             => 'event',
            self::ThemeCommunity    => 'theme',
        });
    }

    /** Localised plural label (e.g. "Campaigns"). */
    public function plural(): string
    {
        return __('communities.plural.'.match ($this) {
            self::LocationCommunity => 'locations',
            self::Organisation      => 'organisations',
            self::Campaign          => 'campaigns',
            self::Course            => 'courses',
            self::Event             => 'events',
            self::ThemeCommunity    => 'theme_communities',
        });
    }
}
