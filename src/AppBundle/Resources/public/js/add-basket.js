var Order = function () {
    var add = function (elementBtn) {
        var productPriceId = elementBtn.attr('data-product-price');

        $.ajax({
            url: "/api/order/add",
            method: "POST",
            data: { productPriceId: productPriceId },
            statusCode: {
                200: function () {
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
            $(".add-basket").click(function () {
                var elementBtn = $(this);
                if (!elementBtn.hasClass('disabled')) {
                    add(elementBtn);
                }
            });
        }
    };
};

$(document).ready(function () {
    var order = Order();
    order.add();
});
