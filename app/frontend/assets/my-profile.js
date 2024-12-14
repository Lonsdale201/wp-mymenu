jQuery(document).ready(function ($) {
    $('.profile-dropdown-wrapper').on('click keydown', function (event) {
        var target = $(event.target);

        if (target.is('a')) {
            return;
        }

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

        setTimeout(function () {
            dropdown.css({
                opacity: 1,
                transform: 'translateY(0px) translateX(-50%)'
            });
            dropdown.find('a, button, [tabindex]').first().focus();
        }, 10);

        // Trap focus inside the dropdown
        trapFocus(dropdown);
    }

    function trapFocus(dropdown) {
        var focusableElements = dropdown.find('a, button, [tabindex]').filter(':visible');
        var firstElement = focusableElements.first();
        var lastElement = focusableElements.last();

        dropdown.on('keydown', function (event) {
            if (event.key === 'Tab') {
                if (event.shiftKey) {
                    // Shift + Tab
                    if ($(document.activeElement).is(firstElement)) {
                        event.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    // Tab
                    if ($(document.activeElement).is(lastElement)) {
                        event.preventDefault();
                        firstElement.focus();
                    }
                }
            } else if (event.key === 'ArrowDown') {
                // Arrow Down
                event.preventDefault();
                var next = focusableElements.eq(focusableElements.index(document.activeElement) + 1);
                if (next.length) {
                    next.focus();
                } else {
                    firstElement.focus();
                }
            } else if (event.key === 'ArrowUp') {
                // Arrow Up
                event.preventDefault();
                var prev = focusableElements.eq(focusableElements.index(document.activeElement) - 1);
                if (prev.length) {
                    prev.focus();
                } else {
                    lastElement.focus();
                }
            }
        });
    }

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            $('.dropdown-content').each(function () {
                closeDropdown($(this));
            });
        }
    });

    function closeDropdown(dropdown) {
        dropdown.css({
            opacity: 0,
            transform: 'translateY(10px) translateX(-50%)'
        });
        setTimeout(function () {
            dropdown.hide();
        }, 300);
    }

    $(document).on('click', function () {
        $('.dropdown-content').each(function () {
            closeDropdown($(this));
        });
    });
});
