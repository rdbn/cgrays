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
            html += '<div class="col-lg-12 bottom20 text-left"><button class="btn btn-warning btn-back">Назад</button></div>';
        }

        html +=
            '<div class="col-lg-6"><img src="'+item['icon_url_large']+'" class="img-thumbnail" /></div>' +
            '<div class="col-lg-6 text-left"><h4>'+item['name']+'</h4><p>' +
            '<span class="label label-default">'+item['quality']+'</span> ' +
            '<span class="label label-default">'+item['rarity']+'</span> ' +
            '<span class="label label-default">'+item['type_product']+'</span> ' +
            '<span class="label label-default">'+item['heroes']+'</span>' +
            '</p><p class="alert alert-danger error-price hidden" role="alert"></p>' +
            '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-rub"></span></span>' +
            '<input type="number" class="form-control input-lg price-sell-product" placeholder="Цена" value="' + (price > 0 ? price : '') + '" />' +
            '<span class="input-group-addon">.00</span></div>';

        if (id instanceof Object) {
            html += '<button class="top10 btn btn-warning update-product-sell" data-toggle="'+item['id']+'">Обновить стоимость</button></div>';
        } else {
            html += '<button class="top10 btn btn-danger add-product-sell" data-toggle="'+id+'">Выставить на продажу</button></div>';
        }

        $('#user-inventory').html(html);
        $('#menu-pagination').addClass('hidden');
    },
    addItemSell: function (item) {
        var html =
            '<div class="col-xs-4 col-sm-3 col-md-2"><div class="thumbnail">' +
            '<a class="view-sell-item" href="#" data-product="'+item['product_id']+'" data-product-price="'+item['product_price_id']+'" ' +
            'data-toggle="modal" data-target="#add-products-steam-user"><img src="/'+item['icon_url']+'" alt="'+item['name']+'" /></a>' +
            '<div class="caption"><p><strong><span class="text-warning price-item-sell">'+item['price'] +
            '.00 <span class="glyphicon glyphicon-rub"></span></span></strong></p><p class="text-center">' +
            '<button class="btn btn-danger btn-sm remove-sell-item" type="button" data-toggle="'+item['product_price_id']+'">Снять с продажи</button>' +
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
        }

        $('#user-inventory').html('<p>'+str+'</p>');
    },
    errorPrice: function (text) {
        $('#user-inventory').find('.error-price').html(text);
    }
};
var Inventory = function () {
    var
        urlMain = "/app_dev.php/api",
        urlUser = urlMain + '/user',
        urlProducts = urlMain + '/products';

    var Pagination = function (page) {
        $.ajax({
            url: urlUser + "/steam/inventory?page="+page,
            method: "GET",
            statusCode: {
                200: function (data) {
                    allItems = data;
                    View.inventory();
                },
                400: function (data) {
                    if (data['price'] === undefined) {
                        View.error(400);
                    } else {
                        View.errorPrice(data['price']);
                    }
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
                    $('#add-products-steam-user').modal('hide');
                },
                400: function () {
                    View.error(400);
                }
            }
        });
    };
    var AddSellProduct = function (id, price) {
        id = allItems[id]['classid'] + '-' + allItems[id]['instanceid'];

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
        var
            productId = element.attr('data-product'),
            productPriceId = element.attr('data-product-price');

        $.ajax({
            url: urlProducts + '/' + productId + '/' + productPriceId,
            statusCode: {
                200: function (data) {
                    var item = {
                        icon_url_large: '/' + data['icon_url_large'],
                        heroes: data['heroes']['title'],
                        type_product: data['type_product']['title'],
                        quality: data['quality']['title'],
                        name: data['name'],
                        price: data['product_price'][0]['price'],
                        id: data['product_price'][0]['id']
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
                    element.parents('.user-inventory-product-sell').remove()
                },
                400: function () {
                    element.removeClass('disabled')
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
            Inventory().isSell($(this), true);
        });
    },
    stopSell: function () {
        $('#stop-sell').click(function () {
            Inventory().isSell($(this), false);
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
        var priceElement = userInventory.find('.price-sell-product');

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
            userInventory.on('click', '.add-product-sell', function () {
                AddOrUpdateProductPrice($(this), function (id, priceElement) {
                    inventory.addSellProduct(id, priceElement.val());
                });
            });
        },
        updateProductPrice: function () {
            userInventory.on('click', '.update-product-sell', function () {
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
});
