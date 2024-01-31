jQuery(document).ready(function($) {
    $('.profile-dropdown-wrapper .user-nickname').on('click keydown', function(event) {
        if (event.type === 'click' || event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            event.stopPropagation();
            toggleDropdown($(this).closest('.profile-dropdown-wrapper'));
        }
    });

    function toggleDropdown(wrapper) {
        var dropdown = wrapper.find('.dropdown-content');
        var isOpen = dropdown.is(':visible');
        wrapper.find('.user-nickname').attr('aria-expanded', isOpen ? 'false' : 'true');
        dropdown.attr('aria-hidden', isOpen ? 'true' : 'false');

        if (isOpen) {
            closeDropdown(dropdown);
        } else {
            openDropdown(dropdown, wrapper);
        }
    }

   function openDropdown(dropdown, wrapper) {
    dropdown.css({
        display: 'block',
        opacity: 0,
        transform: 'translateY(10px) translateX(-50%)'
    });

    var dropdownOffset = dropdown.offset().left;
    var dropdownWidth = dropdown.outerWidth();
    var windowWidth = $(window).width();
    var defaultShift = 10; // default

    if (dropdownOffset + dropdownWidth > windowWidth) {
        var shiftAmount = (dropdownOffset + dropdownWidth) - windowWidth + defaultShift;
        dropdown.css('left', (parseInt(dropdown.css('left')) - shiftAmount) + 'px');
    } else if (dropdownOffset < 0) {
        dropdown.css('left', (parseInt(dropdown.css('left')) - dropdownOffset) + 'px');
    } else {
        dropdown.css('left', (parseInt(dropdown.css('left')) - defaultShift) + 'px');
    }

    setTimeout(function() {
        dropdown.css({
            opacity: 1,
            transform: 'translateY(0px) translateX(-50%)'
        });
    }, 10);
}




    function closeDropdown(dropdown) {
        dropdown.css({
            opacity: 0,
            transform: 'translateY(10px) translateX(-50%)'
        });
        setTimeout(function() {
            dropdown.hide();
        }, 300);
    }

    $(document).on('click', function() {
        $('.dropdown-content').each(function() {
            closeDropdown($(this));
        });
    });
});
