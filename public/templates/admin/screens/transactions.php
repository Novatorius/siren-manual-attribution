<?php

use Novatorius\Siren\ManualAttribution\Factories\TransactionsTable;
use PHPNomad\Core\Facades\InstanceProvider;
use PHPNomad\Core\Facades\PathResolver;
use PHPNomad\Core\Facades\Template;
use PHPNomad\Template\Interfaces\ScreenResolverStrategy;
use Siren\Collaborators\Core\Datastores\Collaborator\Interfaces\CollaboratorDatastore;
use Siren\Collaborators\Core\Models\Collaborator;


$table = InstanceProvider::get(TransactionsTable::class);
$table->prepare_items();
$editUrl = InstanceProvider::get(ScreenResolverStrategy::class)->getUrlForSlug('siren_transactions_edit');
$collaborators = InstanceProvider::get(CollaboratorDatastore::class)->andWhere([]);

?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Transactions</h1>
        <a href="<?= $editUrl ?>" class="page-title-action">Add New</a>
        <hr class="wp-header-end">
        <form method="post">
            <p class="search-box">
                <?= Template::render(PathResolver::getPath('public/templates/admin/search')) ?>
            </p>
        </form>
        <form id="primary-action-form" method="post">
            <?php $table->display(); ?>
        </form>
    </div>

    <!-- action modals -->
    <template id="siren-action-credit_collaborator">
        <h1>Credit Collaborator With Transaction</h1>
        <p>Specify the collaborator to credit.</p>
        <form id="siren-modal-confirm" class="trait--flex-form">
            <label for="collaboratorId">Collaborator
                <select name="collaboratorId">
                    <?php
                    // TODO: Make this restful.
                    /**
                     * @var Collaborator $collaborator
                     */
                    ?>
                    <?php foreach ($collaborators as $collaborator): ?>
                        <option value="<?= $collaborator->getId() ?>"><?= $collaborator->getFullName() ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="button action">Credit Collaborator</button>
        </form>
    </template>

<?= Template::render(PathResolver::getPath('public/templates/admin/components/modal/modal'), [
    'title' => 'Credit Collaborators',
]);
?>