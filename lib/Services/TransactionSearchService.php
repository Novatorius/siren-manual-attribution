<?php

namespace Novatorius\Siren\ManualAttribution\Services;

use PHPNomad\Database\Interfaces\QueryBuilder;
use PHPNomad\Database\Interfaces\QueryStrategy;
use PHPNomad\Datastore\Exceptions\DatastoreErrorException;
use PHPNomad\Integrations\WordPress\Database\ClauseBuilder;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Transactions\Core\Datastores\Transaction\Interfaces\TransactionDatastore;
use Siren\Transactions\Service\Datastores\Transaction\TransactionsTable;
use Siren\Transactions\Service\Datastores\TransactionDetail\TransactionDetailsTable;

class TransactionSearchService
{
    public function __construct(
        protected QueryBuilder $queryBuilder,
        protected TransactionDetailsTable $detailsTable,
        protected TransactionsTable $transactionsTable,
        protected ClauseBuilder $clauseBuilder,
        protected QueryStrategy $queryStrategy,
        protected TransactionDatastore $transactions
    )
    {
    }

    protected function buildSearchClause(string $term): ClauseBuilder
    {
        return $this->clauseBuilder
            ->reset()
            ->useTable($this->detailsTable)
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('description', 'LIKE', '%' . $term . '%');
    }

    public function getFoundTransactionIds(string $term, int $limit, int $offset)
    {
        $query = $this->queryBuilder->reset()->useTable($this->detailsTable)->select('transactionId')
            ->rightJoin($this->transactionsTable, 'transactionId', 'id')
            ->from($this->detailsTable)
            ->where($this->buildSearchClause($term))
            ->groupBy('transactionId')
            ->limit($limit)
            ->offset($offset);

        try {
            $results = Arr::cast(Arr::pluck($this->queryStrategy->query($query), 'transactionId'), 'int');
        } catch (DatastoreErrorException $e) {
            $results = [];
        }

        $this->queryBuilder->reset();
        // TODO : investigate why join is not reset properly
        $this->queryBuilder->join = [];
        $this->clauseBuilder->reset();
        return $results;
    }
}