<?php

namespace Novatorius\Siren\ManualAttribution\Handlers;

use PHPNomad\Events\Interfaces\CanHandle;
use PHPNomad\Events\Interfaces\Event;
use PHPNomad\Events\Interfaces\EventStrategy;
use PHPNomad\Template\Interfaces\ScreenResolverStrategy;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Commerce\Adapters\FloatToIntPriceAdapter;
use Siren\Transactions\Core\Models\Transaction;
use Siren\Transactions\Core\Services\TransactionCreateService;
use Siren\WordPress\Core\Events\SingleActionCompleted;
use Siren\WordPress\Core\Events\SingleActionInitiated;
use Siren\WordPress\Core\Providers\AdminNoticeProvider;

class UpdateTransaction implements CanHandle
{
    public function __construct(
        protected TransactionCreateService $transactionCreateService,
        protected FloatToIntPriceAdapter $priceAdapter,
        protected AdminNoticeProvider $notices,
        protected EventStrategy $event,
        protected ScreenResolverStrategy $screenResolver
    )
    {

    }

    protected function prepareDetails(array $details)
    {
        return Arr::map($details, function($detail){
            $detail['value'] = $this->priceAdapter->toInt(Arr::get($detail, 'value'));
            settype($detail['quantity'], 'int');

            if($detail['type'] === 'discount'){
                $detail['value'] = -abs($detail['value']);
            }
            if($detail['type'] === 'custom'){
                $detail['type'] = $detail['custom_type'];
            }

            unset($detail['custom_type']);

            return $detail;
        });
    }

    public function handle(Event $event): void
    {
        if ($event instanceof SingleActionInitiated && $event->getModel() === Transaction::class) {
            $details = $this->prepareDetails(Arr::get($_POST, 'details', []));

            if(empty($details)){
                return;
            }

            $transaction = $this->transactionCreateService->maybeCreateTransaction($details);

            if(!$transaction){
                $this->notices->addNotice("Failed to create transaction.", 'error');
                wp_redirect($this->screenResolver->getUrlForSlug('siren_transactions', ['siren_notices' => json_encode($this->notices->getNotices())]));
                return;
            }
            $event->setItemId($transaction->getId());
            $this->notices->addNotice("Transaction created successfully.", 'success');

            $this->event->broadcast(new SingleActionCompleted($event->getItemId(), $event->getAction(), $event->getModel()));
            wp_redirect($this->screenResolver->getUrlForSlug('siren_transactions', ['siren_notices' => json_encode($this->notices->getNotices())]));
        }
    }
}