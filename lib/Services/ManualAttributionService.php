<?php

namespace Novatorius\Siren\ManualAttribution\Services;

use NorthCommerceStripe\Issuing\Transaction;
use Siren\Collaborators\Core\Models\Collaborator;

class ManualAttributionService
{
    public function attributeTransaction(Transaction $transaction, Collaborator $collaborator): void
    {
        //TODO : Implement the logic to attribute the transaction to the collaborator.
    }
}