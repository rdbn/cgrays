$(document).ready(function () {

    $('#remove-modal').on('show.bs.modal', function (event) {
        var targetButton = $(event.relatedTarget);
        var modal = $(this);
        var elementRemoveOrder = modal.find("#remove-order");

        elementRemoveOrder.attr("data-order", targetButton.data("order"));
        elementRemoveOrder.on("click", function () {
            var buttonRemove = $(this);
            var orderID = buttonRemove.attr("data-order");

            console.log(orderID);
            if (!buttonRemove.hasClass("disabled")) {
                buttonRemove.addClass("disabled");
                $.ajax({
                    url: "/app_dev.php/api/order/remove/" + orderID,
                    method: "POST",
                    statusCode: {
                        200: function () {
                            $("#order-" + orderID).remove();
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
