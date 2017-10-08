$(document).ready(function () {
    $('.skins').click(function (e) {
        var
            sort = {},
            element = $(this),
            elementSort = $('#cases_form_sort'),
            sortVal = elementSort.val(),
            elementSkinsCount = element.find('.skins-count'),
            skinsId = elementSkinsCount.attr('data-skins-id'),
            skinsCount = elementSkinsCount.val();

        if (skinsCount <= 0) {
            elementSkinsCount.val('');
        }

        if (sortVal.length > 0) {
            sort = JSON.parse(sortVal);
        }

        if (element.hasClass('check-skins')) {
            if (e.target.nodeName === 'INPUT') {
                return;
            }

            element.removeClass('check-skins');
            delete sort[skinsId];
        } else {
            element.addClass('check-skins');
            elementSkinsCount.focus();
            sort[skinsId] = skinsCount;
        }

        elementSort.val(JSON.stringify(sort));
    });

    var elementSkinsCount = $('.skins-count');
    elementSkinsCount.click(function () {
        var
            elementSkinsCount = $(this),
            skinsCount = elementSkinsCount.val();

        if (skinsCount <= 0) {
            elementSkinsCount.val('');
        }
    });

    elementSkinsCount.keyup(function () {
        var
            sort = {},
            elementSort = $('#cases_form_sort'),
            sortVal = elementSort.val(),
            skinsId = $(this).attr('data-skins-id'),
            skinsCount = $(this).val();

        if (sortVal.length > 0) {
            sort = JSON.parse(sortVal);
        }

        sort[skinsId] = skinsCount;
        elementSort.val(JSON.stringify(sort));
    });

    elementSkinsCount.keydown(function () {
        var
            sort = {},
            elementSort = $('#cases_form_sort'),
            sortVal = elementSort.val(),
            skinsId = $(this).attr('data-skins-id'),
            skinsCount = $(this).val();

        if (sortVal.length > 0) {
            sort = JSON.parse(sortVal);
        }

        sort[skinsId] = skinsCount;
        elementSort.val(JSON.stringify(sort));
    })
});