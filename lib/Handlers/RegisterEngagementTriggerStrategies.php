<?php

namespace Novatorius\Siren\ManualAttribution\Handlers;

use Novatorius\Siren\ManualAttribution\Strategies\ManualEngagementTriggerStrategy;
use PHPNomad\Events\Interfaces\CanHandle;
use PHPNomad\Events\Interfaces\Event;
use Siren\Engagements\Core\Events\EngagementTriggerRegistryInitiated;

/**
 * @implements CanHandle<EngagementTriggerRegistryInitiated>
 */
class RegisterEngagementTriggerStrategies implements CanHandle
{
    public function handle(Event $event): void
    {
        if ($event instanceof EngagementTriggerRegistryInitiated) {
            $event->addStrategy(ManualEngagementTriggerStrategy::class);
        }
    }
}