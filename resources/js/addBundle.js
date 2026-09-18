let couponIndex = 0;

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
        '<td><input type="date" name="coupons[' + couponIndex + '][expires_at]"></td>' +
        '<td><button type="button" class="btn-remove-row" onclick="this.closest(\'tr\').remove()">&times;</button></td>';

    tbody.appendChild(row);
    couponIndex++;
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

document.addEventListener('click', function (event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu.open').forEach(function (openMenu) {
            openMenu.classList.remove('open');
        });
    }
});

var tierIndex = 0;

function addTierRow() {
    var tbody = document.getElementById('tiers-tbody');
    var row = document.createElement('tr');

    row.innerHTML =
        '<td><input type="number" min="1" step="1" name="tiers[' + tierIndex + '][quantity]" oninput="updateTierTotal()" required></td>' +
        '<td><input type="number" min="0" step="0.01" name="tiers[' + tierIndex + '][amount]" oninput="updateTierTotal()" required></td>' +
        '<td><input type="date" name="tiers[' + tierIndex + '][expires_at]"></td>' +
        '<td><button type="button" class="btn-remove-row" onclick="removeTierRow(this)">&times;</button></td>';

    tbody.appendChild(row);
    tierIndex++;
}

function removeTierRow(button) {
    var row = button.closest('tr');
    row.remove();
    updateTierTotal();
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

window.openModal = openModal;
window.closeModal = closeModal;
window.addCouponRow = addCouponRow;
window.toggleDropdown = toggleDropdown;
window.addTierRow = addTierRow;
window.removeTierRow = removeTierRow;
window.updateTierTotal = updateTierTotal;
window.clearExportFilters = clearExportFilters;
