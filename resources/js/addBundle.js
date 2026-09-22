let couponIndex = 0;
let tierIndex = 0;

function openModal(modalId) {
    var id = modalId || 'bundle-modal';
    document.getElementById(id).classList.add('active');
}

function closeModal(modalId) {
    var id = modalId || 'bundle-modal';
    document.getElementById(id).classList.remove('active');
}

function addCouponRow() {
    const tbody = document.getElementById('coupons-tbody');
    const row = document.createElement('tr');

    row.innerHTML =
        '<td><input type="text" name="coupons[' + couponIndex + '][receiver_name]"></td>' +
        '<td><input type="email" name="coupons[' + couponIndex + '][receiver_email]"></td>' +
        '<td><input type="number" step="0.01" min="0" name="coupons[' + couponIndex + '][discount_amount]" required></td>' +
        '<td><input type="date" name="coupons[' + couponIndex + '][send_date]"></td>' +
        '<td><input type="checkbox" name="coupons[' + couponIndex + '][send_immediately]" value="1"></td>' +
        '<td><input type="date" name="coupons[' + couponIndex + '][expires_at]"></td>' +
        '<td><button type="button" class="btn-remove-row" data-remove-row>&times;</button></td>';

    tbody.appendChild(row);
    couponIndex++;
}

function addTierRow() {
    var tbody = document.getElementById('tiers-tbody');
    var row = document.createElement('tr');

    row.innerHTML =
        '<td><input type="number" min="1" step="1" name="tiers[' + tierIndex + '][quantity]" data-tier-input required></td>' +
        '<td><input type="number" min="0" step="0.01" name="tiers[' + tierIndex + '][amount]" data-tier-input required></td>' +
        '<td><input type="date" name="tiers[' + tierIndex + '][expires_at]"></td>' +
        '<td><button type="button" class="btn-remove-row" data-remove-row>&times;</button></td>';

    tbody.appendChild(row);
    tierIndex++;
}

function toggleDropdown(button) {
    const menu = button.nextElementSibling;
    const isOpen = menu.classList.contains('open');

    document.querySelectorAll('.dropdown-menu.open').forEach(function (openMenu) {
        openMenu.classList.remove('open');
    });

    if (!isOpen) {
        menu.classList.add('open');
    }
}

function updateTierTotal() {
    var rows = document.querySelectorAll('#tiers-tbody tr');
    var total = 0;

    for (var i = 0; i < rows.length; i++) {
        var quantityInput = rows[i].querySelector('input[name*="[quantity]"]');
        var amountInput = rows[i].querySelector('input[name*="[amount]"]');

        var quantity = parseFloat(quantityInput.value);
        var amount = parseFloat(amountInput.value);

        if (isNaN(quantity)) {
            quantity = 0;
        }
        if (isNaN(amount)) {
            amount = 0;
        }

        total = total + (quantity * amount);
    }

    document.getElementById('tier-total-value').textContent = '$' + total.toFixed(2);
}

function clearExportFilters(modalId) {
    var modal = document.getElementById(modalId);

    var createdFromInput = modal.querySelector('input[name="created_from"]');
    var createdToInput = modal.querySelector('input[name="created_to"]');
    var statusSelect = modal.querySelector('select[name="status"]');
    var amountMinInput = modal.querySelector('input[name="amount_min"]');
    var amountMaxInput = modal.querySelector('input[name="amount_max"]');

    createdFromInput.value = '';
    createdToInput.value = '';
    statusSelect.value = 'all';
    amountMinInput.value = '';
    amountMaxInput.value = '';

    var checkboxes = modal.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
}

document.addEventListener('click', function (event) {
    var openModalTrigger = event.target.closest('[data-open-modal]');
    if (openModalTrigger) {
        openModal(openModalTrigger.getAttribute('data-open-modal'));
        return;
    }

    var closeModalTrigger = event.target.closest('[data-close-modal]');
    if (closeModalTrigger) {
        closeModal(closeModalTrigger.getAttribute('data-close-modal'));
        return;
    }

    var addCouponTrigger = event.target.closest('[data-add-coupon-row]');
    if (addCouponTrigger) {
        addCouponRow();
        return;
    }

    var addTierTrigger = event.target.closest('[data-add-tier-row]');
    if (addTierTrigger) {
        addTierRow();
        return;
    }

    var removeRowTrigger = event.target.closest('[data-remove-row]');
    if (removeRowTrigger) {
        var row = removeRowTrigger.closest('tr');
        var parentTbody = row.closest('tbody');

        row.remove();

        if (parentTbody && parentTbody.id === 'tiers-tbody') {
            updateTierTotal();
        }

        return;
    }

    var clearFiltersTrigger = event.target.closest('[data-clear-filters]');
    if (clearFiltersTrigger) {
        clearExportFilters(clearFiltersTrigger.getAttribute('data-clear-filters'));
        return;
    }

    var dropdownTrigger = event.target.closest('[data-toggle-dropdown]');
    if (dropdownTrigger) {
        toggleDropdown(dropdownTrigger);
        return;
    }

    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu.open').forEach(function (openMenu) {
            openMenu.classList.remove('open');
        });
    }
});

document.addEventListener('input', function (event) {
    if (event.target.closest('[data-tier-input]')) {
        updateTierTotal();
    }
});

document.addEventListener('submit', function (event) {
    var form = event.target;

    var confirmMessage = form.getAttribute('data-confirm');

    if (confirmMessage) {
        if (!confirm(confirmMessage)) {
            event.preventDefault();
            return;
        }
    }

    var closeOnSubmitModalId = form.getAttribute('data-close-on-submit');

    if (closeOnSubmitModalId) {
        closeModal(closeOnSubmitModalId);
    }
});


document.addEventListener('DOMContentLoaded', function () {
    var modalToOpen = document.body.getAttribute('data-open-modal-on-load');

    if (modalToOpen) {
        openModal(modalToOpen);
    }
});
