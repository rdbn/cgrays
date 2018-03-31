var FilterCases = function () {
    var url = '/api/admin/cases/show-list-skins-pubg';

    var showSkins = function (element, elementLoader, data) {
        var
            elementCasesList = $('#cases-list'),
            casesId = $('#chapter-cases').attr('data-cases-id');

        elementCasesList.html('');

        $.ajax({
            url: url + (casesId ? '/' + casesId : ''),
            method: "GET",
            data: data,
            statusCode: {
                200: function (data) {
                    console.log(data);
                    var html = "";
                    for (var key in data['list_skins']) {
                        var isSkinsCase = '';
                        var item = data['list_skins'][key];

                        if (item['is_skins_case']) {
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

    var eventFilterValue = function () {
        var
            elementLoader = $('#load-skins-img'),
            element = $(this),
            filter = {
                'cases_form_pubg_filter[name]': $('#cases_form_pubg_filter_name').val(),
                'cases_form_pubg_filter[decor]': $('#cases_form_pubg_filter_decor').val(),
                'cases_form_pubg_filter[quality]': $('#cases_form_pubg_filter_quality').val(),
                'cases_form_pubg_filter[itemSet]': $('#cases_form_pubg_filter_itemSet').val(),
                'cases_form_pubg_filter[rarity]': $('#cases_form_pubg_filter_rarity').val(),
                'cases_form_pubg_filter[weapon]': $('#cases_form_pubg_filter_weapon').val(),
                'cases_form_pubg_filter[typeSkins]': $('#cases_form_pubg_filter_typeSkins').val(),
                'offset': $(this).attr("data-offset"),
                'limit': $(this).attr("data-limit")
            };

        if (!element.hasClass('disabled')) {
            element.addClass('disabled');
            elementLoader.removeClass('hidden');

            showSkins(element, elementLoader, filter);
        }
    };

    return {
        formFilter: function () {
            $('#send-filter').click(function () {
                eventFilterValue();

                return false;
            });

            var elementInput = $('#cases_form_filter_name');
            elementInput.keyup(function () {
                if (event.keyCode === 13) {
                    eventFilterValue();

                    return false;
                }
            });

            elementInput.keydown(function () {
                if (event.keyCode === 13) {
                    eventFilterValue();

                    return false;
                }
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
    $(window).keydown(function(event){
        if(event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });

    var filterCases = new FilterCases();
    filterCases.pagination();
    filterCases.formFilter();

    $('#cases-list').on('click', '.skins', function (e) {
        var
            element = $(this),
            img = element.find('img').attr('src'),
            skinsId = element.attr('data-skins-id'),
            rarityId = element.attr('data-rarity-id');

        if (element.hasClass('check-skins')) {
            if (e.target.nodeName === 'INPUT') {
                return;
            }

            element.removeClass('check-skins');
            $('#skins-' + skinsId).remove();
        } else {
            element.addClass('check-skins');

            var html = '<div id="skins-'+skinsId+'" class="form-inline add-skins">';
            html += '<img src="'+img+'" class="img-responsive img-preview" />';
            html += '<div class="form-group">' +
                '<a href="/admin/skins/'+skinsId+'/edit" target="_blank" class="control-label">'+element.attr('data-skins-name')+'</a>' +
                '<input id="cases_skins_'+skinsId+'"' +
                'class="form-control skins-procent" value="" placeholder="Процент %" ' +
                'name="cases_skins_skins['+skinsId+'][procent]" /></div>';

            html += '<div class="form-group"><input ' +
                'class="form-control skins-price" ' +
                'name="cases_skins_skins['+skinsId+'][price]" value="" placeholder="Цена скина" /></div>'

            html += '<button ' +
                'class="btn btn-danger remove-skins-rarity" ' +
                'type="button" ' +
                'data-skins-id="'+skinsId+'" ' +
                'data-rarity-id="'+rarityId+'">';

            html += 'Удалить</button></div>';

            $('#add-skins-rarity-' + element.attr('data-rarity-id')).append(html);
        }
    });

    var elemSkinsCheckCases = $('.skins-check-cases');
    elemSkinsCheckCases.on('click', '.remove-skins-rarity', function () {
        var skinsId = $(this).attr('data-skins-id');

        $('#skins-' + skinsId).remove();
        $('#skins-check-' + skinsId + ' .skins').removeClass('check-skins');
    });
});
