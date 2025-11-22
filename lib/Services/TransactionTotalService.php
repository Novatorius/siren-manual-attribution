<?php

namespace Novatorius\Siren\ManualAttribution\Services;

use PHPNomad\Datastore\Exceptions\DatastoreErrorException;
use PHPNomad\Logger\Interfaces\LoggerStrategy;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Transactions\Core\Datastores\TransactionDetail\Interfaces\TransactionDetailDatastore;
use Siren\Transactions\Core\Models\Transaction;
use Siren\Transactions\Core\Models\TransactionDetail;

/**
 * TODO: PROBABLY SHOULD REFACTOR THIS TO CORE IF IT'S USEFUL THERE.
 */
class TransactionTotalService
{
    public function __construct(
        protected TransactionDetailDatastore $transactionDetails,
        protected LoggerStrategy $loggerStrategy
    )
    {

    }
    public function calculateTransactionTotal(Transaction $transaction)
    {
        try {
            $details = $this->transactionDetails->getDetailsForTransactionId($transaction->getId());
        } catch (DatastoreErrorException $e) {
            $this->loggerStrategy->logException($e);
            return 0;
        }

        //TODO: THIS IS RE-USED IN MULTIPLE PLACES IN CORE AND HERE. PROBABLY SHOULD REFACTOR TO A SERVICE, OR SOMETHING.
        return Arr::reduce($details, function (int $acc, TransactionDetail $detail) {
            $acc += $detail->getValue()->getValue() * $detail->getQuantity();
            return $acc;
        }, 0);
    }
}