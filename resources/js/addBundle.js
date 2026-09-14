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
        '<td><input type="text" name="coupons[' + couponIndex + '][receiver_name]" required></td>' +
        '<td><input type="email" name="coupons[' + couponIndex + '][receiver_email]" required></td>' +
        '<td><input type="number" step="0.01" min="0" name="coupons[' + couponIndex + '][discount_amount]" required></td>' +
        '<td><input type="date" name="coupons[' + couponIndex + '][send_date]"></td>' +
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


window.openModal = openModal;
window.closeModal = closeModal;
window.addCouponRow = addCouponRow;
window.toggleDropdown = toggleDropdown;
