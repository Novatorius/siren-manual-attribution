<?php
/**
 * @var Transaction $transaction
 */

use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Template\Interfaces\ScreenResolverStrategy;
use Siren\Transactions\Core\Models\Transaction;

$screenResolver = InstanceProvider::get(ScreenResolverStrategy::class);

?>
<a href="<?= $screenResolver->getUrlForSlug('siren_transaction', ['id' => $transaction->getId()]); ?>"><?= $transaction->getId() ?></a>
