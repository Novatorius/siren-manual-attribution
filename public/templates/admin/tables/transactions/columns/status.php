<?php
/**
 * @var Transaction $transaction
 */

use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Template\Interfaces\ScreenResolverStrategy;
use Siren\Transactions\Core\Models\Transaction;

$statusMap = [
    'draft' => 'draft',
    'pending' => 'pending',
    'rejected' => 'inactive',
    'complete' => 'active'
];
$status = $transaction->getStatus();
$screenResolver = InstanceProvider::get(ScreenResolverStrategy::class);
?>

<a href="<?= $screenResolver->getUrlForSlug('siren_transactions', ['status' => $status]); ?>" class="trait--tag trait--status-<?= $statusMap[$status] ?>"><?= ucwords($status) ?></a>