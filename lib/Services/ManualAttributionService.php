<?php

namespace Novatorius\Siren\ManualAttribution\Services;

use Novatorius\Siren\ManualAttribution\Events\ManualAttributionRequested;
use PHPNomad\Events\Interfaces\EventStrategy;
use Siren\Collaborators\Core\Models\Collaborator;
use Siren\Conversions\Core\Events\ConversionInitialized;
use Siren\Opportunities\Core\Services\OpportunityCreateOrUpdateService;
use Siren\Transactions\Core\Models\Transaction;

class ManualAttributionService
{
    public function __construct(
        protected EventStrategy $event,
        protected OpportunityCreateOrUpdateService  $opportunityCreateOrUpdateService
    )
    {

    }

    public function attributeTransaction(Transaction $transaction, Collaborator $collaborator): void
    {
        $opportunity = $this->opportunityCreateOrUpdateService->createOrUpdateOpportunity();

        $this->event->broadcast(new ManualAttributionRequested(
            $collaborator,
            $transaction,
            $opportunity,
        ));

        // Triggers the conversion/obligation creation process.
        $this->event->broadcast(new ConversionInitialized(
            $opportunity->getId(),
            $transaction,
            'sale'
        ));
    }
}