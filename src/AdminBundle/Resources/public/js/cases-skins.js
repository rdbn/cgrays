var FilterCases = function () {
    var url = '/api/admin/cases/show-list-skins';

    var showSkins = function (element, elementLoader, data) {
        var
            elementCasesList = $('#cases-list'),
            casesId = $('#chapter-cases').attr('data-cases-id'),
            sortVal = $('#cases_form_sort'),
            sort = JSON.parse(sortVal.val());

        elementCasesList.html('');

        $.ajax({
            url: url + '/' + casesId,
            method: "GET",
            data: data,
            statusCode: {
                200: function (data) {
                    var html = "";
                    for (var key in data['list_skins']) {
                        var isSkinsCase = '';
                        var item = data['list_skins'][key];

                        if (item['is_skins_case']) {
                            isSkinsCase = 'check-skins';
                        } else if (sort[item['rarity_id']]['skins'][item['skins_id']] !== undefined) {
                            isSkinsCase = 'check-skins';
                        }

                        html += '<div id="skins-check-'+item['skins_id']+'" class="col-lg-2">';
                        html += '<div ' +
                            'class="thumbnail skins ' + isSkinsCase + '" ' +
                            'data-skins-id="'+item['skins_id']+'" ' +
                            'data-rarity-id='+item['rarity_id']+' ' +
                            'data-skins-name="'+item['name']+'">';

                        html += '<img src="/'+item['icon_url']+'" alt="'+item['name']+'" />';
                        html += '<div class="caption"><h5>'+item['name']+'</h5>';
                        html += '</div></div></div>';
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
                    filter = {
                        'cases_form_filter[name]': $('#cases_form_filter_name').val(),
                        'cases_form_filter[decor]': $('#cases_form_filter_decor').val(),
                        'cases_form_filter[quality]': $('#cases_form_filter_quality').val(),
                        'cases_form_filter[itemSet]': $('#cases_form_filter_itemSet').val(),
                        'cases_form_filter[rarity]': $('#cases_form_filter_rarity').val(),
                        'cases_form_filter[weapon]': $('#cases_form_filter_weapon').val(),
                        'cases_form_filter[typeSkins]': $('#cases_form_filter_typeSkins').val(),
                        'offset': $(this).attr("data-offset"),
                        'limit': $(this).attr("data-limit")
                    };

                if (!element.hasClass('disabled')) {
                    element.addClass('disabled');
                    elementLoader.removeClass('hidden');

                    showSkins(element, elementLoader, filter);
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

    $('#cases-list').on('click', '.skins', function (e) {
        var
            sort = {},
            element = $(this),
            elementSort = $('#cases_form_sort'),
            sortVal = elementSort.val(),
            skinsId = element.attr('data-skins-id'),
            rarityId = element.attr('data-rarity-id');

        if (sortVal.length > 0) {
            sort = JSON.parse(sortVal);
        }

        if (sort.length > 0) {
            if (sort[rarityId].rarity === undefined) {
                sort = {};
            }
        }

        if (element.hasClass('check-skins')) {
            if (e.target.nodeName === 'INPUT') {
                return;
            }

            element.removeClass('check-skins');
            $('#skins-' + skinsId).remove();
            delete sort[rarityId]['skins'][skinsId];

            if (sort[rarityId]['skins'].length == 0) {
                delete sort[rarityId];
            }
        } else {
            element.addClass('check-skins');
            var skins = {};
            skins[skinsId] = 0;

            if (sort[rarityId] !== undefined) {
                if (sort[rarityId]['skins'] !== undefined) {
                    sort[rarityId]['skins'][skinsId] = 0;
                } else {
                    sort[rarityId]['skins'] = skins;
                }
            } else {
                sort[rarityId] = {rarity: 0, skins: skins};
            }

            var html = '<div id="skins-'+skinsId+'" class="form-group">';
            html += '<div class="input-group"><span class="input-group-addon">'+element.attr('data-skins-name')+'</span>';
            html += '<input ' +
                'class="form-control skins-procent" value="" placeholder="Процент %" ' +
                'data-skins-id="'+skinsId+'" ' +
                'data-rarity-id="'+rarityId+'" />';

            html += '<span class="input-group-btn">';
            html += '<button ' +
                'class="btn btn-danger remove-skins-rarity" ' +
                'type="button" ' +
                'data-skins-id="'+skinsId+'" ' +
                'data-rarity-id="'+rarityId+'">';

            html += 'Удалить</button></span></div></div>';

            $('#add-skins-rarity-' + element.attr('data-rarity-id')).append(html);
        }

        elementSort.val(JSON.stringify(sort));
    });

    var elemSkinsCheckCases = $('.skins-check-cases');
    elemSkinsCheckCases.on('click', '.remove-skins-rarity', function () {
        var
            skinsId = $(this).attr('data-skins-id'),
            rarityId = $(this).attr('data-rarity-id'),
            elementSort = $('#cases_form_sort'),
            sort = JSON.parse(elementSort.val());

        console.log(skinsId, rarityId);
        delete sort[rarityId]['skins'][skinsId];
        if (sort[rarityId]['skins'].length == 0) {
            delete sort[rarityId];
        }

        elementSort.val(JSON.stringify(sort));

        $('#skins-' + skinsId).remove();
        $('#skins-check-' + skinsId + ' .skins').removeClass('check-skins');
    });

    elemSkinsCheckCases.on('keyup', '.skins-procent', function () {
        var
            sort = {},
            skins = {},
            element = $(this),
            elementSort = $('#cases_form_sort'),
            skinsId = element.attr('data-skins-id'),
            rarityId = element.attr('data-rarity-id'),
            sortVal = elementSort.val();

        if (sortVal.length > 0) {
            sort = JSON.parse(elementSort.val())
        }

        if (sort[rarityId] !== undefined) {
            if (sort[rarityId].skins === undefined && sort[rarityId].rarity === undefined) {
                delete sort[rarityId];
            }
        }

        if (sort[rarityId] !== undefined) {
            if (sort[rarityId]['skins'] !== undefined) {
                sort[rarityId]['skins'][skinsId] = element.val();
            } else {
                skins[skinsId] = element.val();
                sort[rarityId].skins = skins;
            }
        } else {
            skins[skinsId] = element.val();
            sort[rarityId] = {skins: skins};
        }

        elementSort.val(JSON.stringify(sort));
    });

    elemSkinsCheckCases.on('keydown', '.skins-procent', function () {
        var
            sort = {},
            skins = {},
            element = $(this),
            elementSort = $('#cases_form_sort'),
            skinsId = element.attr('data-skins-id'),
            rarityId = element.attr('data-rarity-id'),
            sortVal = elementSort.val();

        if (sortVal.length > 0) {
            sort = JSON.parse(elementSort.val())
        }

        if (sort[rarityId] !== undefined) {
            if (sort[rarityId].skins === undefined && sort[rarityId].rarity === undefined) {
                delete sort[rarityId];
            }
        }

        if (sort[rarityId] !== undefined) {
            if (sort[rarityId]['skins'] !== undefined) {
                sort[rarityId]['skins'][skinsId] = element.val();
            } else {
                skins[skinsId] = element.val();
                sort[rarityId].skins = skins;
            }
        } else {
            skins[skinsId] = element.val();
            sort[rarityId] = {skins: skins};
        }

        elementSort.val(JSON.stringify(sort));
    });

    var elemRarityProcentValue = $('.rarity-procent-value');
    elemRarityProcentValue.keyup(function () {
        var
            sort = {},
            element = $(this),
            rarityId = element.attr('data-rarity-id'),
            elementSort = $('#cases_form_sort'),
            sortVal = elementSort.val();

        if (sortVal.length > 0) {
            sort = JSON.parse(elementSort.val())
        }

        if (sort[rarityId] !== undefined) {
            if (sort[rarityId].skins === undefined && sort[rarityId].rarity === undefined) {
                delete sort[rarityId];
            }
        }

        if (sort[rarityId] !== undefined) {
            sort[rarityId].rarity = element.val();
        } else {
            sort[rarityId] = {rarity: element.val()};
        }

        elementSort.val(JSON.stringify(sort));
    });

    elemRarityProcentValue.keydown(function () {
        var
            sort = {},
            element = $(this),
            elementSort = $('#cases_form_sort'),
            rarityId = element.attr('data-rarity-id'),
            sortVal = elementSort.val();

        if (sortVal.length > 0) {
            sort = JSON.parse(elementSort.val())
        }

        if (sort[rarityId] !== undefined) {
            if (sort[rarityId].skins === undefined && sort[rarityId].rarity === undefined) {
                delete sort[rarityId];
            }
        }

        if (sort[rarityId] !== undefined) {
            sort[rarityId].rarity = element.val();
        } else {
            sort[rarityId] = {rarity: element.val()};
        }

        elementSort.val(JSON.stringify(sort));
    });
});