<?php
/**
 * @var Transaction $transaction
 */

use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Mappings\Core\Datastores\Mapping\Interfaces\MappingDatastore;
use Siren\Transactions\Core\Models\Transaction;

/**
 * TODO: MOVE THIS INTO A REGISTRY THAT INTEGRATIONS PROVIDE.
 * WOULD BE NICE TO USE THIS IN BOTH THE EDIT SCREEN AND HERE.
 */
$types = [
    'llms_order' => [
        'name' => 'LifterLMS',
        'order_url_base' => ''
    ],
    'edd_payment' => [
        'name' => 'EDD',
        'order_url_base' => ''
    ],
    'wc_order' => [
        'name' => 'WooCommerce',
        'order_url_base' => admin_url('admin.php?page=wc-orders&action=edit&id=%s')
    ],
];

$source = Arr::first(InstanceProvider::get(MappingDatastore::class)->andWhere([
    ['column' => 'localId', 'value' => $transaction->getId(), 'operator' => '='],
    ['column' => 'externalType', 'value' => array_keys($types), 'operator' => 'IN'],
    ['column' => 'localType', 'value' => 'transaction', 'operator' => '='],
]));

if(!$source){
    echo 'Manual';
    return;
}

$type = $types[$source->getExternalType()];
$name = $type['name'];
$url = sprintf($type['order_url_base'], $source->getExternalId());

echo $name . ' (<a href="' . $url . '">#' . $source->getExternalId() . '</a>)';