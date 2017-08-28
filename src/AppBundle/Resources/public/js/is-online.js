$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    var
        socket = io.connect('http://'+window.location.hostname+':88/isOnline'),
        username = $('#username').attr('data-username');

    var isOnline = function () {
        if (username) {
            socket.emit('isOnline', {username: username});
        }
    };

    isOnline();
    setInterval(isOnline, (60 * 1000));
});