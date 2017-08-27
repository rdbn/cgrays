var Chat = function () {
    var
        socket = io.connect('http://localhost:8080/chat'),
        username = $('#message_type_form_username').val();

    return {
        run: function () {
            socket.on('message', function (data) {
                $('#message-view').append(
                    '<p class="col-lg-12"><strong class="'+(username === data.username ? 'text-action': '')+'">' + data.username+ ':</strong> '+data.message+'</p>'
                );
            });
        },
        sendMessage: function () {
            $('#message_type_form_submit').click(function () {
                var message = $('#message_type_form_text_message');

                if (message.val().length > 0) {
                    socket.emit('message', {username: username, message: message.val()});
                    message.val('');
                }

                return false;
            });
        }
    };
};

$(document).ready(function () {
    var chat = new Chat();
    chat.run();
    chat.sendMessage();
});