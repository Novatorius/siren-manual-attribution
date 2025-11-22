<?php

namespace Novatorius\Siren\ManualAttribution\Strategies;

use Novatorius\Siren\ManualAttribution\Events\ManualAttributionRequested;
use PHPNomad\Events\Interfaces\Event;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Collaborators\Core\Models\Program;
use Siren\Engagements\Core\Interfaces\EngagementTriggerStrategy;
use Siren\Engagements\Core\Services\CollaboratorActiveProgramService;
use Siren\Engagements\Core\Services\EngagementTriggerService;

class ManualEngagementTriggerStrategy implements EngagementTriggerStrategy
{
    public function __construct(
        protected CollaboratorActiveProgramService $activeProgramService,
        protected EngagementTriggerService         $engagementTriggerService
    )
    {

    }

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
        if (!$event instanceof ManualAttributionRequested) {
            return [];
        }

        $programs = $this->activeProgramService->getActiveProgramIds($this->getId(), $event->collaborator->getId());

        return Arr::map(
            $programs,
            fn(Program $program) => $this->engagementTriggerService->triggerEngagement(
                $this->getId(), $program, $event->collaborator, $event->opportunity
            )
        );
    }

    public static function getId(): string
    {
        return 'manual';
    }
}