<?php
/**
 * @var Transaction $transaction
 */

use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Utils\Helpers\Arr;
use PHPNomad\Utils\Helpers\Str;
use Siren\Transactions\Core\Datastores\TransactionDetail\Interfaces\TransactionDetailDatastore;
use Siren\Transactions\Core\Models\Transaction;
use Siren\Transactions\Core\Models\TransactionDetail;

/**
 * @var TransactionDetail[] $details
 */
$details = InstanceProvider::get(TransactionDetailDatastore::class)->getDetailsForTransactionId($transaction->getId());
$maxDetailsToShow = 1;
$left = count($details) - $maxDetailsToShow;

for ($i=0; $i < $maxDetailsToShow; $i++) {
    $detail = Arr::get($details, $i);
    if(!$detail){
        break;
    }
    echo '<strong>' . esc_html($detail->getName()) . '</strong>: ' . esc_html($detail->getDescription());
}
if(count($details) > $maxDetailsToShow){
    echo ' and ' . $left .' ' . Str::pluarize('other',$left);
}