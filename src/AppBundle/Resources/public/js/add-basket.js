$(document).ready(function () {
    $("#add-basket").click(function () {
        var productID = $(this).attr("data-product"), productPriceID = $(this).attr("data-product-price");

        $.ajax({
            url: "/app_dev.php/api/order/add/" + productID + "/" + productPriceID,
            method: "POST",
            data: {},
            statusCode: {
                200: function () {
                    console.log("is all ok");
                },
                401: function () {
                    console.log("not authorization");
                }
            }
        })
    });

    $("#buy").click(function () {
        var productID = $(this).attr("data-product"), productPriceID = $(this).attr("data-product-price");

        $.ajax({
            url: "/api/order/add/" + productID + "/" + productPriceID,
            method: "POST",
            data: {},
            statusCode: {
                200: function () {
                    console.log("is all ok");
                },
                401: function () {
                    console.log("not authorization");
                }
            }
        })
    });

    $(".add-basket").click(function () {
        var element = $(this);
        var
            productID = element.attr("data-product"),
            productPriceID = element.attr("data-product-price");

        if (element.addClass("disabled")) {
            element.addClass("disabled");
            $.ajax({
                url: "/app_dev.php/api/order/add/" + productID + "/" + productPriceID,
                method: "POST",
                data: {},
                statusCode: {
                    200: function () {
                        element.removeClass("disabled");
                        console.log("is all ok");
                    },
                    400: function () {
                        element.removeClass("disabled");
                        console.log("Bad Request");
                    }
                }
            })
        }
    });
});
