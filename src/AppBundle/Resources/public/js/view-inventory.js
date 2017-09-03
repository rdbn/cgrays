var page = 1;
var allItems = [];

var View = {
    inventory: function () {
        var html = "";
        for (var item in allItems) {
            html += '<div class="col-xs-6 col-sm-2 col-md-2"><div class="thumbnail">';

            if (allItems[item]['is_sell'] === 1) {
                html += '<a class="view-item-inventory" data-toggle="'+item+'" href="#"><img src="'+allItems[item]['icon_url']+'" /></a>'
            } else {
                html += '<img src="'+allItems[item]['icon_url']+'" />'
            }

            html += '</div></div>';
        }
        $('#user-inventory').html(html);
        $('#menu-pagination').removeClass('hidden');
    },
    item: function (id) {
        var item, html = '', price = 0;
        if (id instanceof Object) {
            item = id;
            price = id['price'];
        } else {
            item = allItems[id];
            html += '<div class="col-lg-12 bottom20 text-left"><button class="btn btn-action btn-back">Назад</button></div>';
        }

        html +=
            '<div class="col-lg-6"><img src="'+item['icon_url_large']+'" class="img-thumbnail" /></div>' +
            '<div class="col-lg-6 text-left"><h4>'+item['name']+'</h4><p>' +
            '<span class="label label-default">'+item['quality']['localized_tag_name']+'</span> ' +
            '<span class="label label-default">'+item['item_set']['localized_tag_name']+'</span> ' +
            '<span class="label label-default">'+item['rarity']['localized_tag_name']+'</span> ' +
            '<span class="label label-default">'+item['type_skins']['localized_tag_name']+'</span> ' +
            '<span class="label label-default">'+item['weapon']['localized_tag_name']+'</span> ' +
            '<span class="label label-default">'+item['decor']['localized_tag_name']+'</span>' +
            '</p><p class="alert alert-danger error-price hidden" role="alert"></p>' +
            '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-rub"></span></span>' +
            '<input type="number" class="form-control input-lg price-sell-skins" placeholder="Цена" value="' + (price > 0 ? price : '') + '" />' +
            '<span class="input-group-addon">.00</span></div>';

        if (id instanceof Object) {
            html += '<button class="top10 btn btn-action update-skins-sell" data-toggle="'+item['id']+'">Обновить стоимость</button></div>';
        } else {
            html += '<button class="top10 btn btn-action add-skins-sell" data-toggle="'+id+'">Выставить на продажу</button></div>';
        }

        $('#user-inventory').html(html);
        $('#menu-pagination').addClass('hidden');
    },
    addItemSell: function (item) {
        var html =
            '<div class="col-xs-4 col-sm-3 col-md-2"><div class="thumbnail">' +
            '<a class="view-sell-item" href="#" data-skins-price="'+item['skins_price_id']+'" ' +
            'data-toggle="modal" data-target="#add-skins-steam-user"><img src="/'+item['icon_url']+'" alt="'+item['name']+'" /></a>' +
            '<div class="caption"><p><strong><span class="price-item-sell">'+item['price'] +
            '.00 <span class="glyphicon glyphicon-rub text-danger"></span></span></strong></p><p class="text-center">' +
            '<button class="btn btn-action btn-sm remove-sell-item" type="button" data-toggle="'+item['skins_price_id']+'">Снять с продажи</button>' +
            '</p></div></div></div>';

        $('.scroll-view').append(html);
    },
    loader: function () {
        $('#user-inventory').html('<img src="/bundles/app/image/ajax-loader.gif" />');
    },
    updatePriceItem: function (element, price) {
        element.parents('.thumbnail').find('.price-item-sell').html(price + '.00 <span class="glyphicon glyphicon-rub"></span>');
    },
    error: function (code) {
        var str;
        switch(code) {
            case 400:
                str = 'Что то пошло не так, пожалуйста повторите попытку.';
                break;
            case 403:
                str = 'На сданный момент ваш инветарь заблокирован для просмотра.';
                break;
        }

        $('#user-inventory').html('<p>'+str+'</p>');
    },
    errorPrice: function (text) {
        $('#user-inventory').find('.error-price').html(text);
    }
};
var Inventory = function () {
    var
        urlMain = "/api",
        urlUser = urlMain + '/user',
        urlProducts = urlMain + '/skins';

    var Pagination = function (page) {
        $.ajax({
            url: urlUser + "/steam/inventory?page="+page,
            method: "GET",
            statusCode: {
                200: function (data) {
                    allItems = data;
                    if (allItems.length > 0) {
                        View.inventory();
                    } else {
                        $('#user-inventory').html('Ваш инветнтарь пуст.');
                    }
                },
                400: function (data) {
                    if (data['price'] === undefined) {
                        View.error(400);
                    } else {
                        View.errorPrice(data['price']);
                    }
                },
                403: function (data) {
                    View.error(400);
                }
            }
        });
    };
    var Refresh = function (element) {
        $.ajax({
            url: urlUser + "/refresh/inventory",
            method: "POST",
            statusCode: {
                200: function () {
                    element.removeClass('disabled');
                }
            }
        });
    };
    var Sell = function (element, isSell) {
        $.ajax({
            url: urlUser + "/isSell",
            method: "POST",
            data: {isSell: isSell},
            statusCode: {
                200: function () {
                    element.removeClass('disabled')
                },
                400: function () {
                    element.removeClass('disabled')
                }
            }
        });
    };
    var UpdatePriceProduct = function (id, priceElement, elementSellItem) {
        var price = priceElement.val();

        $.ajax({
            url: urlProducts + "/update/price",
            method: "POST",
            data: {item: {id: id, price: price}},
            statusCode: {
                200: function () {
                    View.updatePriceItem(elementSellItem, price);
                    $('#add-skins-steam-user').modal('hide');
                },
                400: function () {
                    View.error(400);
                }
            }
        });
    };
    var AddSellProduct = function (id, price) {
        $.ajax({
            url: urlProducts + "/add",
            method: "POST",
            data: {item: {page: page, price: price, id: id}},
            statusCode: {
                200: function (data) {
                    delete allItems[id];
                    View.addItemSell(data);
                    View.inventory();
                },
                400: function () {
                    View.error(400);
                }
            }
        });
    };
    var ViewSellProduct = function (element) {
        var skinsPriceId = element.attr('data-skins-price');

        $.ajax({
            url: urlProducts + '/' + skinsPriceId,
            statusCode: {
                200: function (data) {
                    var item = {
                        icon_url_large: '/' + data['skins']['icon_url_large'],
                        type_skins: data['skins']['type_skins'],
                        decor: data['skins']['decor'],
                        item_set: data['skins']['item_set'],
                        weapon: data['skins']['weapon'],
                        rarity: data['skins']['rarity'],
                        quality: data['skins']['quality'],
                        name: data['skins']['name'],
                        price: data['price'],
                        id: data['id']
                    };

                    View.item(item)
                }
            }
        });
    };
    var Remove = function (element, id) {
        $.ajax({
            url: urlProducts + "/remove",
            method: "POST",
            data: {id: id},
            statusCode: {
                200: function () {
                    element.parents('.user-inventory-skins-sell').remove()
                },
                400: function () {
                    element.removeClass('disabled')
                }
            }
        });
    };
    var HrefTrade = function (element, hrefTrade) {
        $.ajax({
            url: urlUser + "/hrefTrade",
            method: "POST",
            data: {href_trade: hrefTrade},
            statusCode: {
                200: function () {
                    element.removeClass('disabled');
                }
            }
        });
    };

    return {
        addSellProduct: function (id, price) {
            AddSellProduct(id, price);
        },
        updatePriceProduct: function (id, priceElement, elementSellItem) {
            UpdatePriceProduct(id, priceElement, elementSellItem);
        },
        pagination: function (isPage) {
            if (isPage === true) {
                page += 1;
            } else if (page > 1){
                page -= 1;
            }

            View.loader();
            Pagination(page)
        },
        refresh: function (element) {
            if (!element.hasClass('disabled')) {
                element.addClass('disabled');
                Refresh(element);
            }
        },
        viewSellProduct: function (element) {
            View.loader();
            ViewSellProduct(element)
        },
        isSell: function (element, isSell) {
            if (!element.hasClass('disabled')) {
                element.addClass('disabled');
                Sell(element, isSell);
            }
        },
        remove: function (element, id) {
            Remove(element, id);
        },
        hrefTrade: function (element, hrefTrade) {
            HrefTrade(element, hrefTrade);
        }
    }
};
var Action = {
    refreshCache: function () {
        $('#refresh-cache').click(function () {
            Inventory().refresh($(this));
        });
    },
    startSell: function () {
        $('#start-sell').click(function () {
            Inventory().isSell($(this), 1);
        });
    },
    stopSell: function () {
        $('#stop-sell').click(function () {
            Inventory().isSell($(this), 0);
        });
    },
    paginationLeft: function () {
        $('#arrow-left').click(function () {
            Inventory().pagination(false);
        });
    },
    paginationRight: function () {
        $('#arrow-right').click(function () {
            Inventory().pagination(true);
        });
    },
    showListInventory: function () {
        $('#showListInventory').click(function () {
            var hrefTrade = $('#value-href-trade');
            if (hrefTrade.val().length === 0) {
                hrefTrade.focus();

                $('#add-skins-steam-user').modal({
                    show: false
                });
                return false;
            }

            Inventory().pagination();
        });
    }
};
var ActionUserInventory = function () {
    var
        userInventory = $('#user-inventory'),
        userInventorySell = $('#user-inventory-sell'),
        inventory = Inventory(),
        elementSellItem;

    var validationPrice = function (priceElement) {
        var
            price = priceElement.val(),
            errorPrice = userInventory.find('.error-price');

        if (price > 0 && price.length < 9) {
            if (!errorPrice.hasClass('hidden')) {
                errorPrice.addClass('hidden');
            }

            return true;
        }

        if (price.length === 0) {
            View.errorPrice('Ввидите стоимость предмета.');
        }

        if (price.length >= 9) {
            View.errorPrice('Кол-во цифыр не больше 9.');
        }

        userInventory.find('.error-price').removeClass('hidden');

        return false;
    };

    var AddOrUpdateProductPrice = function (btnAddProductSell, closure) {
        var priceElement = userInventory.find('.price-sell-skins');

        if (validationPrice(priceElement)) {
            if (!btnAddProductSell.hasClass('disabled')) {
                btnAddProductSell.addClass('disabled');
                userInventory.find('.btn-back').addClass('disabled');
                $('#close-modal').addClass('disabled');

                var id = btnAddProductSell.attr('data-toggle');
                closure(id, priceElement);
            }
        }
    };

    return {
        viewSellProduct: function () {
            userInventorySell.on('click', '.view-sell-item', function () {
                elementSellItem = $(this);
                inventory.viewSellProduct(elementSellItem);
            });
        },
        addProductSell: function () {
            userInventory.on('click', '.add-skins-sell', function () {
                AddOrUpdateProductPrice($(this), function (id, priceElement) {
                    inventory.addSellProduct(id, priceElement.val());
                });
            });
        },
        updateProductPrice: function () {
            userInventory.on('click', '.update-skins-sell', function () {
                AddOrUpdateProductPrice($(this), function (id, priceElement) {
                    Inventory().updatePriceProduct(id, priceElement, elementSellItem);
                })
            });
        },
        viewInventory: function () {
            userInventory.on('click', '.btn-back', function () {
                if (!$(this).hasClass('disabled')) {
                    View.inventory();
                }
            });
        },
        viewInventoryItem: function () {
            userInventory.on('click', '.view-item-inventory', function () {
                var id = $(this).attr('data-toggle');
                View.item(id);
            });
        },
        removeProduct: function () {
            userInventorySell.on('click', '.remove-sell-item', function () {
                var elementBtn = $(this);
                if (!elementBtn.hasClass('disabled')) {
                    elementBtn.addClass('disabled');
                    inventory.remove(elementBtn, elementBtn.attr('data-toggle'));
                }
            })
        },
        hrefTrade: function () {
            $('#add-href-trade').click(function () {
                var
                    elementBtn = $(this),
                    hrefTrade = $('#value-href-trade').val();

                if (!elementBtn.hasClass('disabled')) {
                    elementBtn.addClass('disabled');
                    inventory.hrefTrade(elementBtn, hrefTrade);
                }
            })
        }
    };
};

$(document).ready(function () {
    Action.refreshCache();
    Action.startSell();
    Action.stopSell();
    Action.paginationLeft();
    Action.paginationRight();
    Action.showListInventory();

    var actionUserInventory = ActionUserInventory();
    actionUserInventory.viewSellProduct();
    actionUserInventory.viewInventory();
    actionUserInventory.viewInventoryItem();
    actionUserInventory.addProductSell();
    actionUserInventory.updateProductPrice();
    actionUserInventory.removeProduct();
    actionUserInventory.hrefTrade();
});
