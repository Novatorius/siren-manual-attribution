<?php

namespace Novatorius\Siren\ManualAttribution\Handlers;

use Novatorius\Siren\ManualAttribution\Services\ManualAttributionService;
use PHPNomad\Datastore\Exceptions\DatastoreErrorException;
use PHPNomad\Events\Interfaces\CanHandle;
use PHPNomad\Events\Interfaces\Event;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Collaborators\Core\Datastores\Collaborator\Interfaces\CollaboratorDatastore;
use Siren\Collaborators\Core\Models\Collaborator;
use Siren\Transactions\Core\Datastores\Transaction\Interfaces\TransactionDatastore;
use Siren\Transactions\Core\Datastores\TransactionDetail\Interfaces\TransactionDetailDatastore;
use Siren\Transactions\Core\Models\Transaction;
use Siren\WordPress\Core\Events\BulkActionInitiated;

/**
 * @extends CanHandle<BulkActionInitiated>
 */
class HandleTransactionDelete implements CanHandle
{
    public function __construct(protected TransactionDatastore $transactions, protected TransactionDetailDatastore $transactionDetails)
    {

    }

    /**
     * @inheritDoc
     */
    public function handle(Event $event): void
    {
        if ($event instanceof BulkActionInitiated && $event->getModel() === Transaction::class) {
            if($event->getAction() === 'delete') {
                foreach($event->getIds() as $id) {
                    try {
                        $this->transactions->delete($id);

                        foreach($this->transactionDetails->getDetailsForTransactionId($id) as $detail) {
                            $this->transactionDetails->delete($detail->getId());
                        }
                    } catch (DatastoreErrorException $e) {
                        // Handle exception if needed
                    }
                }
            }
        }
    }
}