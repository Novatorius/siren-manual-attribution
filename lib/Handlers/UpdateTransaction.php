<?php

namespace Novatorius\Siren\ManualAttribution\Handlers;

use NorthCommerceStripe\Issuing\Transaction;
use PHPNomad\Events\Interfaces\CanHandle;
use PHPNomad\Events\Interfaces\Event;
use Siren\WordPress\Core\Events\SingleActionInitiated;

class UpdateTransaction implements CanHandle
{
    public function handle(Event $event): void
    {
        if ($event instanceof SingleActionInitiated && $event->getModel() === Transaction::class) {
            // TODO: Implement the logic to update the transaction based on the event data.
            // @see: Siren\WordPress\Integration\Listeners\UpdateProgram For example implementation.
        }
    }
}