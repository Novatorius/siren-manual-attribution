<?php

namespace Novatorius\Siren\ManualAttribution;

use Novatorius\Siren\ManualAttribution\Handlers\HandleManualAttribution;
use Novatorius\Siren\ManualAttribution\Handlers\RegisterEngagementTriggerStrategies;
use Novatorius\Siren\ManualAttribution\Handlers\UpdateTransaction;
use Novatorius\Updater\Interfaces\VersionProvider;
use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Core\Facades\Template;
use PHPNomad\Events\Interfaces\HasEventBindings;
use PHPNomad\Events\Interfaces\HasListeners;
use PHPNomad\Loader\Interfaces\Loadable;
use PHPNomad\Template\Exceptions\TemplateNotFound;
use PHPNomad\Utils\Helpers\Arr;
use Siren\Engagements\Core\Events\EngagementTriggerRegistryInitiated;
use Siren\Extensions\Core\Interfaces\Extension;
use Siren\Roles\Enums\Roles;
use Siren\Transactions\Core\Models\Transaction;
use Siren\WordPress\Core\Events\BulkActionInitiated;
use Siren\WordPress\Core\Events\SingleActionInitiated;

final class Initializer implements Extension, Loadable, HasEventBindings, HasListeners
{
    protected bool $isActive = false;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Manual Collaborator Attribution';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Allows manual attribution of collaborators to transactions.';
    }

    /**
     * @inheritDoc
     */
    public function canActivate(): bool
    {
        return version_compare(InstanceProvider::get(VersionProvider::class)->getVersion(), '2.1.0', '>=');
    }

    /**
     * @inheritDoc
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @inheritDoc
     */
    public function getSupports(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function load(): void
    {
        $this->isActive = true;
        add_action('admin_menu', fn() => $this->loadSubmenus());
    }

    /**
     * Loads the admin submenus.
     *
     * @return void
     */
    protected function loadSubmenus(): void
    {
         add_submenu_page(
            'siren_main_menu',
            'Transactions',
            'Transactions',
            'siren_' . Roles::FulfillmentManager,
            'siren_transactions',
            fn() => $this->renderTemplate('public/templates/admin/screens/transactions')
        );

        add_submenu_page(
            ' ',
            'Edit Transaction',
            'Edit Transaction',
            'siren_' . Roles::FulfillmentManager,
            'siren_transactions_edit',
            fn() => $this->renderTemplate('public/templates/admin/screens/edit-transaction')
        );
    }

    /**
     * Renders a template.
     *
     * @param $template
     * @return void
     */
    private function renderTemplate($template)
    {
        try {
            echo Template::render(SIREN_MANUAL_AFFILIATE_PAYOUTS_PATH . $template);
        } catch (TemplateNotFound $exception) {
            wp_die($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getEventBindings(): array
    {
        return [
            SingleActionInitiated::class => [
                ['action' => 'admin_init', 'transformer' => function () {
                    $page = Arr::get($_REQUEST, 'page');
                    $action = Arr::get($_REQUEST, 'action');
                    $nonce = Arr::get($_REQUEST, '_wpnonce', '');


                    if (!$action || !$nonce) {
                        return null;
                    }

                    if (($page === 'siren_transactions_edit') && wp_verify_nonce($nonce, 'edit_transaction')) {
                        return new SingleActionInitiated(null, $action, Transaction::class);
                    }

                    return null;
                }]
            ],
            BulkActionInitiated::class => [
                ['action' => 'admin_init', 'transformer' => function () {
                    $page = Arr::get($_REQUEST, 'page');
                    $action = Arr::get($_REQUEST, 'action');
                    $items = Arr::cast(Arr::get($_REQUEST, 'item_id', []), 'integer');
                    $nonce = Arr::get($_REQUEST, '_wpnonce', '');


                    if (!$action || !$nonce) {
                        return null;
                    }

                    if (($page === 'siren_transactions') && wp_verify_nonce($nonce, 'bulk-transactions')) {
                        return new BulkActionInitiated($items, $action, Transaction::class);
                    }

                    return null;
                }]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getListeners(): array
    {
        return [
            EngagementTriggerRegistryInitiated::class => RegisterEngagementTriggerStrategies::class,
            SingleActionInitiated::class => UpdateTransaction::class,
            BulkActionInitiated::class => HandleManualAttribution::class
        ];
    }
}