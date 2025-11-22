<?php
/**
 * @var Transaction $transaction
 */

use Siren\Transactions\Core\Models\Transaction;

echo $transaction->getCreatedDate()->format('Y/m/d \a\t g:i a')
?>