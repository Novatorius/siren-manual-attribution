<?php
/**
 * @var Transaction $transaction
 */

use Novatorius\Siren\ManualAttribution\Services\TransactionTotalService;
use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Commerce\Adapters\FloatToIntPriceAdapter;
use Siren\Commerce\Models\Amount;
use Siren\Transactions\Core\Datastores\TransactionDetail\Interfaces\TransactionDetailDatastore;
use Siren\Transactions\Core\Models\Transaction;
use Siren\Transactions\Core\Models\TransactionDetail;

$total = InstanceProvider::get(TransactionTotalService::class)->calculateTransactionTotal($transaction);

/**
 * @var TransactionDetail[] $details
 */
$details = InstanceProvider::get(TransactionDetailDatastore::class)->getDetailsForTransactionId($transaction->getId());
$currency = Arr::first($details)->getValue()->getCurrency();
$adapter = InstanceProvider::get(FloatToIntPriceAdapter::class)->toString(new Amount(
    $total,
    $currency
));

echo esc_html($adapter);