jQuery(document).ready(function ($) {
    const dropdownLabelField = $('#mymenu_dropdown_label');
    const customLabelField = $('#mymenu_custom_label');

    function toggleCustomLabelField() {
        console.log('Dropdown changed:', dropdownLabelField.val());
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
});
