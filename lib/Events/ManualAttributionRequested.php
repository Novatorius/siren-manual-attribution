<?php

namespace Novatorius\Siren\ManualAttribution\Events;

use PHPNomad\Events\Interfaces\Event;

class ManualAttributionRequested implements Event
{
    public static function getId(): string
    {
        return 'manual_attribution_requested';
    }
}