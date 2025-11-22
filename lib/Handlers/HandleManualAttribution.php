<?php

namespace Novatorius\Siren\ManualAttribution\Handlers;

use Novatorius\Siren\ManualAttribution\Services\ManualAttributionService;
use PHPNomad\Database\Exceptions\RecordNotFoundException;
use PHPNomad\Datastore\Exceptions\DatastoreErrorException;
use PHPNomad\Events\Interfaces\CanHandle;
use PHPNomad\Events\Interfaces\Event;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Collaborators\Core\Datastores\Collaborator\Interfaces\CollaboratorDatastore;
use Siren\Collaborators\Core\Models\Collaborator;
use Siren\Transactions\Core\Datastores\Transaction\Interfaces\TransactionDatastore;
use Siren\Transactions\Core\Models\Transaction;
use Siren\WordPress\Core\Events\BulkActionInitiated;

/**
 * @extends CanHandle<BulkActionInitiated>
 */
class HandleManualAttribution implements CanHandle
{
    public function __construct(protected ManualAttributionService $service, protected CollaboratorDatastore $collaborators, protected TransactionDatastore $transactions)
    {

    }

    /**
     * Attributes multiple transactions to a collaborator.
     *
     * @param array $ids
     * @param int $collaboratorId
     * @return void
     */
    protected function attributeTransactions(array $ids, int $collaboratorId)
    {
        try {
            /**
             * @var Collaborator $collaborator
             */
            $collaborator = $this->collaborators->find($collaboratorId);

            /**
             * @var Transaction[] $transactions
             */
            $transactions = $this->transactions->findMultiple($ids);

            Arr::each($transactions, function ($transaction) use ($collaborator) {
                $this->service->attributeTransaction($transaction, $collaborator);
            });
        } catch (DatastoreErrorException $e) {
            return;
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(Event $event): void
    {
        if ($event instanceof BulkActionInitiated && $event->getModel() === Transaction::class) {
            if($event->getAction() === 'credit_collaborator' && isset($_POST['collaboratorId'])) {
                $this->attributeTransactions($event->getIds(), $_POST['collaboratorId']);
            }
        }
    }
}