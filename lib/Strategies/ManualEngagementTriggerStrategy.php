<?php

namespace Novatorius\Siren\ManualAttribution\Strategies;

use Novatorius\Siren\ManualAttribution\Events\ManualAttributionRequested;
use PHPNomad\Events\Interfaces\Event;
use Siren\Engagements\Core\Interfaces\EngagementTriggerStrategy;

class ManualEngagementTriggerStrategy implements EngagementTriggerStrategy
{
    public function getName(): string
    {
        return 'Manual Attribution';
    }

    public function getDescription(): string
    {
        return 'Triggers an engagement when a manager manually attributes a transaction to a collaborator.';
    }

    public function getTriggeringEvents(): array
    {
        return [ManualAttributionRequested::class];
    }

    public function maybeCreateOrUpdateEngagements(Event $event): array
    {
        // TODO: Implement maybeCreateOrUpdateEngagements() method.
    }

    public static function getId(): string
    {
        return 'manual';
    }
}