var Order = function () {
    var action = function (elementBtn, action) {
        $.ajax({
            url: "/api/order/" + action,
            method: "POST",
            data: { skins_price_id: elementBtn.attr('data-product-price') },
            statusCode: {
                200: function (data) {
                    $('.count_skins_trade').html(data);

                    var element = $('.basket-btn');
                    if (element.hasClass('add-basket')) {
                        element.html('<span class="glyphicon glyphicon-remove"></span>');
                        element.addClass('remove-basket');
                        element.removeClass('add-basket');
                    } else {
                        element.html('<span class="glyphicon glyphicon-shopping-cart"></span>');
                        element.addClass('add-basket');
                        element.removeClass('remove-basket');
                    }

                    elementBtn.removeClass('disabled');
                },
                400: function () {
                    elementBtn.removeClass('disabled');
                },
                401: function () {
                    console.log("not authorization");
                    elementBtn.removeClass('disabled');
                }
            }
        })
    };

    return {
        add: function () {
            $(".card-btn").on("click", ".add-basket", function () {
                var elementBtn = $(this);
                if (!elementBtn.hasClass('disabled')) {
                    action(elementBtn, "add");
                }
            });
        },
        remove: function () {
            $(".card-btn").on("click", ".remove-basket", function () {
                var elementBtn = $(this);
                if (!elementBtn.hasClass('disabled')) {
                    action(elementBtn, "remove");
                }
            });
        }
    };
};

$(document).ready(function () {
    var order = Order();
    order.add();
    order.remove();
});
