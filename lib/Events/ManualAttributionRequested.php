<?php

namespace Novatorius\Siren\ManualAttribution\Events;

use PHPNomad\Events\Interfaces\Event;
use Siren\Collaborators\Core\Models\Collaborator;
use Siren\Opportunities\Core\Models\Opportunity;
use Siren\Transactions\Core\Models\Transaction;

class ManualAttributionRequested implements Event
{
    public function __construct(
       public readonly Collaborator $collaborator,
       public readonly Transaction $transaction,
       public readonly Opportunity $opportunity,
    )
    {

    }
    public static function getId(): string
    {
        return 'manual_attribution_requested';
    }
}