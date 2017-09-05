var News = function () {
    var url = '/app_dev.php/api/news';

    var AddComment = function (elementBtn) {
        var
            comment = $('#news_comment_type_form_comment'),
            commentText = comment.val();

        comment.val('');

        $.ajax({
            url: url + "/add/comment/" + elementBtn.attr('data-news-id'),
            method: "POST",
            data: {
                'news_comment_type_form[comment]': commentText,
                'news_comment_type_form[_token]': $('#news_comment_type_form__token').val()
            },
            statusCode: {
                200: function (data) {
                    var html =
                        '<div class="col-lg-12"><div class="media"><div class="media-left col-lg-1 col-md-1 col-sm-2 col-xs-2">' +
                        '<img class="media-object" src="/'+data.avatar+'" alt="'+data.username+'">' +
                        '</div><div class="media-body"><h4 class="media-heading">'+data.username+'</h4>' +
                        '<p>'+data.comment+'</p><p class="text-muted text-sm">'+data.created_at+'</p></div></div></div>';

                    $('#comments').append(html);

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

    var AddLike = function (elementBtn) {
        var newsId = elementBtn.attr('data-news-id');

        $.ajax({
            url: url + "/add/like",
            method: "POST",
            data: { news_id: newsId },
            statusCode: {
                200: function (data) {
                    elementBtn.find('.badge').html(data['count_like']);
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
        addComment: function () {
            $('form[name="news_comment_type_form"]').submit(function (e) {
                e.preventDefault();

                var elementBtn = $('#news_comment_type_form_submit');
                if (!elementBtn.hasClass('disabled')) {
                    elementBtn.addClass('disabled');
                    AddComment(elementBtn);
                }

                return false;
            });
        },
        addLike: function () {
            $("#like-news").click(function () {
                var elementBtn = $(this);
                if (!elementBtn.hasClass('disabled')) {
                    elementBtn.addClass('disabled');
                    AddLike(elementBtn);
                }
            });
        }
    };
};

$(document).ready(function () {
    var order = News();
    order.addComment();
    order.addLike();
});
