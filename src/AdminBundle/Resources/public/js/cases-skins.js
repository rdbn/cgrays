var FilterCases = function () {
    var url = '/app_dev.php/api/admin/cases/show-list-skins';

    var showSkins = function (element, elementLoader, data) {
        var elementCasesList = $('#cases-list');
        elementCasesList.html('');

        $.ajax({
            url: url,
            method: "GET",
            data: data,
            statusCode: {
                200: function (data) {
                    var html = "";
                    for (var key in data) {
                        var isSkinsCase = '';
                        if (data[key]['is_skins_case']) {
                            isSkinsCase = 'check-skins';
                        }

                        html += '<div class="col-lg-2"><div class="thumbnail skins ' + isSkinsCase + '" >';
                        html += '<img src="/'+data[key]['icon_url']+'" alt="'+data[key]['name']+'" /><div class="caption"><h5>'+data[key]['name']+'</h5>';

                        html += '<p><input ' +
                            'class="form-control skins-count" ' +
                            'data-skins-id="'+data[key]['skins_id']+'" ' +
                            'name="rate_drop_item" ' +
                            'type="text" ' +
                            'placeholder="Коэфицент" ' +
                            'value="'+data[key]['count']+'" /></p></div></div></div>';
                    }

                    elementLoader.addClass('hidden');
                    elementCasesList.html(html);
                    element.removeClass('disabled');
                }
            }
        })
    };

    return {
        formFilter: function () {
            $('#send-filter').click(function () {
                var
                    elementLoader = $('#load-skins-img'),
                    element = $(this),
                    offset = $(this).attr("data-offset"),
                    limit = $(this).attr("data-limit");

                


                if (!element.hasClass('disabled')) {
                    element.addClass('disabled');
                    elementLoader.removeClass('hidden');

                    showSkins(element, elementLoader, {'offset': offset, 'limit': limit});
                }

                return false;
            });
        },
        pagination: function () {
            $('.pagination .page-button').click(function () {
                var
                    elementLoader = $('#load-skins-img'),
                    element = $(this),
                    offset = $(this).attr("data-offset"),
                    limit = $(this).attr("data-limit");

                if (!element.hasClass('disabled')) {
                    element.addClass('disabled');
                    elementLoader.removeClass('hidden');

                    showSkins(element, elementLoader, {'offset': offset, 'limit': limit});
                }

                return false;
            });
        }
    };
};

$(document).ready(function () {
    var filterCases = new FilterCases();
    filterCases.pagination();
    filterCases.formFilter();

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