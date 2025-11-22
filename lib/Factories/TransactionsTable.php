<?php

namespace Novatorius\Siren\ManualAttribution\Factories;

use Novatorius\Siren\ManualAttribution\Services\TransactionSearchService;
use Novatorius\Siren\ManualAttribution\Strategies\PathResolver;
use PHPNomad\Datastore\Exceptions\DatastoreErrorException;
use PHPNomad\Logger\Interfaces\LoggerStrategy;
use PHPNomad\Template\Exceptions\TemplateNotFound;
use PHPNomad\Template\Interfaces\CanRender;
use PHPNomad\Template\Interfaces\CanResolvePaths;
use PHPNomad\Utils\Helpers\Arr;
use PHPNomad\Utils\Helpers\Str;
use Siren\Collaborators\Core\Models\Collaborator;
use Siren\Transactions\Core\Datastores\Transaction\Interfaces\TransactionDatastore;
use Siren\Transactions\Core\Models\Transaction;
use WP_List_Table;

class TransactionsTable extends WP_List_Table
{
    protected TransactionDatastore $transactions;
    protected CanRender $template;
    protected LoggerStrategy $logger;
    protected CanResolvePaths $pathResolver;
    protected TransactionSearchService $searchService;

    public function __construct(TransactionDatastore $transactions, TransactionSearchService $searchService, CanRender $template, LoggerStrategy $logger, PathResolver $pathResolver)
    {
        $this->pathResolver = $pathResolver;
        $this->logger = $logger;
        $this->transactions = $transactions;
        $this->template = $template;
        $this->searchService = $searchService;

        parent::__construct([
            'singular' => 'Transaction', // Singular label for one item
            'plural' => 'Transactions', // Plural label for multiple items
            'ajax' => false
        ]);
    }

    /**
     * @param Transaction $item
     * @return string|void
     */
    protected function column_cb($item)
    {
        if (!$item instanceof Transaction) {
            return;
        }

        return sprintf(
            '<input type="checkbox" name="item_id[]" value="%s" />', $item->getId()
        );
    }

    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />',
            'id' => 'ID',
            'source' => 'Source',
            'details' => 'Details',
            'total' => 'Total',
            'dateCreated' => 'Date',
            'status' => 'Status',
        ];
    }


    protected function get_bulk_actions(): array
    {
        return [
            'credit_collaborator' => 'Credit Collaborator',
            'delete' => 'Delete'
        ];
    }

    /**
     * @param Collaborator $item
     * @param $columnName
     * @return mixed
     */
    function column_default($item, $columnName)
    {
        $path = 'public/templates/admin/tables/transactions/columns/' . Str::camelCaseToDashCase($columnName);
        try {
            return $this->template->render($this->pathResolver->getPath($path), ['transaction' => $item]);
        } catch (TemplateNotFound $e) {
            $method = "get" . ucfirst($columnName);
            if (method_exists($item, $method)) {
                return $item->$method();
            }

            $this->logger->logException($e);
            return "Could not render $columnName";
        }
    }

    /**
     * @inheritDoc
     */
    protected function get_default_primary_column_name(): string
    {
        return 'id';
    }

    function prepare_items()
    {
        try {
            $perPage = 50;
            $page = $_GET['paged'] ?? 1;
            $offset = absint($page - 1) * $perPage;
            $args = [];
            $subQueryArgs = [];

            if (in_array(Arr::get($_REQUEST, 'status'), ['active', 'inactive', 'pending'])) {
                $args[] = ['column' => 'status', 'operator' => '=', 'value' => Arr::get($_REQUEST, 'status')];
            }

            if (isset($_REQUEST['s'])) {
                $args[] = ['column' => 'id', 'operator' => 'IN', 'value' => $this->searchService->getFoundTransactionIds($_REQUEST['s'], $perPage, $offset)];
            }

            $this->items = $this->transactions->orWhere($args, $perPage, $offset, 'id', 'DESC');

            if (empty($args)) {
                $count = $this->transactions->getEstimatedCount();
            } else {
                $count = $this->transactions->countAndWhere($args);
            }

            $this->set_pagination_args([
                'total_items' => $count,
                'per_page' => $perPage,
            ]);
        } catch (DatastoreErrorException $e) {
            $this->logger->logException($e);
            $this->items = [];
        }

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];
    }
}