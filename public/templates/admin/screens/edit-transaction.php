<?php


use PHPNomad\Core\Facades\InstanceProvider;
use Siren\Commerce\Interfaces\CurrencyProviderService;
use Siren\Commerce\Models\Currency;

/** @var Currency[] $currencies */
$currencies = InstanceProvider::get(CurrencyProviderService::class)->getCurrencies();

?>
<h1>Create New Transaction</h1>
<form method="post">
    <?php wp_nonce_field('edit_transaction') ?>
    <input type="hidden" name="action" value="create">

    <div id="transaction-details" class="trait--flex-form" style="max-width: 100%; gap:10px; margin-bottom:10px;">
    </div>
    <button class="button button-secondary" id="add-detail">+ Add Detail</button>

    <?php submit_button('Create Transaction') ?>
</form>

<template id="transaction-detail-template">
    <div class="transaction-detail-row" style="grid-gap: 5px;">
        <div>
            <label for="quantity">Quantity
                <input id="quantity" type="number" step="1" min="1" name="quantity" placeholder="Quantity" required>
            </label>
        </div>
        <div>
            <label for="name">Name
                <input id="name" type="text" name="name" placeholder="Name" required>
            </label>
        </div>
        <div>
            <label for="description">Description
                <input id="description" type="text" name="description" placeholder="Description">
            </label>
        </div>
        <div>
            <label for="type">Type
                <select id="type" name="type">
                    <option value="fee">Fee</option>
                    <option value="product">Product</option>
                    <option value="discount">Discount</option>
                    <option value="tax">Tax</option>
                    <option value="shipping">Shipping</option>
                    <option value="custom">Custom</option>
                </select>
            </label>
        </div>

        <div class="custom-type-field" style="display:none">
            <label for="custom-type">Custom Type
                <input id="custom-type" type="text" name="custom-type" placeholder="Custom type">
            </label>
        </div>
        <div>
            <label for="value">Value
                <input id="value" type="number" name="value" placeholder="Value" min="0.01" step=".01" required>
            </label>
        </div>
        <div>
            <label for="currency">Currency
                <select id="currency" name="units">
                    <?php foreach ($currencies as $currency): ?>
                        <option value="<?= esc_attr($currency->getId()) ?>"><?= esc_html($currency->getId()) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div style="align-self:end">
            <button class="button button-link button-link-delete remove-detail">Remove</button>
        </div>
    </div>
</template>

<script>
    ( function () {
        let detailIndex = 0;
        const detailsContainer = document.getElementById( 'transaction-details' );
        const addBtn = document.getElementById( 'add-detail' );
        const template = document.getElementById( 'transaction-detail-template' );

        function addDetailRow() {
            const clone = template.content.cloneNode( true );
            const row = clone.querySelector( '.transaction-detail-row' );
            // Dynamically update all input/select/label associations
            row.querySelectorAll('input, select').forEach(function(el) {
                // Only update if id exists
                if (el.id) {
                    const newId = el.id + '-' + detailIndex;
                    el.id = newId;
                    // Update name to use details[index][fieldname]
                    if (el.name) {
                        el.name = `details[${detailIndex}][${el.name.replace(/-/g, '_')}]`;
                    }
                    // Find label in same parent div and update its 'for'
                    const parentDiv = el.closest('div');
                    if (parentDiv) {
                        const label = parentDiv.querySelector('label');
                        if (label) {
                            label.setAttribute('for', newId);
                        }
                    }
                }
            });
            // Add event listener for type select to show/hide custom type field
            const typeSelect = row.querySelector('select[name^="details"][name$="[type]"]');
            const customTypeDiv = row.querySelector('.custom-type-field');
            typeSelect.addEventListener('change', function () {
                if ( typeSelect.value === 'custom' ) {
                    customTypeDiv.style.display = '';
                } else {
                    customTypeDiv.style.display = 'none';
                }
            } );
            if ( typeSelect.value === 'custom' ) {
                customTypeDiv.style.display = '';
            }
            detailsContainer.appendChild( row );
            detailIndex++;
            updateRemoveButtons();
        }

        addBtn.addEventListener( 'click', function () {
            addDetailRow();
        } );

        function updateRemoveButtons() {
            const rows = detailsContainer.querySelectorAll( '.transaction-detail-row' );
            rows.forEach( ( row ) => {
                const btn = row.querySelector( '.remove-detail' );
                btn.style.display = rows.length > 1 ? '' : 'none';
                btn.onclick = function () {
                    row.remove();
                    updateRemoveButtons();
                };
            } );
        }

        // Add the initial detail row on page load
        addDetailRow();
    } )();
</script>