$(document).ready(function () {
    $('#remove-modal').on('show.bs.modal', function (event) {
        var targetButton = $(event.relatedTarget);
        var modal = $(this);
        var elementRemoveOrder = modal.find("#remove-order");

        elementRemoveOrder.attr("data-price-id", targetButton.attr("data-price-id"));
        elementRemoveOrder.on("click", function () {
            var buttonRemove = $(this);
            var dataPriceId = buttonRemove.attr("data-price-id");

            console.log(dataPriceId);
            if (!buttonRemove.hasClass("disabled")) {
                buttonRemove.addClass("disabled");
                $.ajax({
                    url: "/app_dev.php/api/order/remove",
                    method: "POST",
                    data: { skins_price_id: dataPriceId },
                    statusCode: {
                        200: function () {
                            $("#order-" + dataPriceId).remove();
                            buttonRemove.removeClass("disabled");
                            modal.modal("hide");
                        },
                        400: function () {
                            buttonRemove.removeClass("disabled");
                            modal.modal("hide");
                        }
                    }
                })
            }
        });
    });

});
