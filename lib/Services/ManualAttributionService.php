<?php

namespace Novatorius\Siren\ManualAttribution\Services;

use Siren\Collaborators\Core\Models\Collaborator;
use Siren\Transactions\Core\Models\Transaction;

class ManualAttributionService
{
    public function attributeTransaction(Transaction $transaction, Collaborator $collaborator): void
    {
        //TODO : Implement the logic to attribute the transaction to the collaborator.
    }
}