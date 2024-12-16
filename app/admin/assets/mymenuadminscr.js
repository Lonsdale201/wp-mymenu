jQuery(document).ready(function ($) {
    const membershipSelector = $('.mpm-select2[name^="menu-item-membership"]');
    const userRoleSelector = $('.mpm-select2[name^="menu-item-role"]');
    const deviceTypeSelector = $('.mpm-select2[name^="menu-item-device-type"]');

    // Initialize Select2 for Membership Selector
    if (membershipSelector.length > 0) {
        membershipSelector.select2();
        // console.log('Membership selector initialized with Select2');
    }

    // Initialize Select2 for User Role Selector
    if (userRoleSelector.length > 0) {
        userRoleSelector.select2();
        // console.log('User role selector initialized with Select2');
    }

    // Initialize Select2 for Device Type Selector
    if (deviceTypeSelector.length > 0) {
        deviceTypeSelector.select2();
        // console.log('Device type selector initialized with Select2');
    }

    // Dropdown-specific logic
    const dropdownLabelField = $('#mymenu_dropdown_label');
    const customLabelField = $('#mymenu_custom_label');

    if (dropdownLabelField.length > 0 && customLabelField.length > 0) {
        function toggleCustomLabelField() {
            // console.log('Dropdown changed:', dropdownLabelField.val());
            if (dropdownLabelField.val() === 'custom') {
                customLabelField.closest('tr').show();
            } else {
                customLabelField.closest('tr').hide();
            }
        }

        // Initial state
        toggleCustomLabelField();

        dropdownLabelField.on('change', function () {
            toggleCustomLabelField();
        });
    }
});
