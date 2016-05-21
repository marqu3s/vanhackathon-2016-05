/**
 * Created by joao on 21/05/16.
 */

/* global websocketAddress */

$(document).ready(function() {
    var socket = io.connect('http://vanhackathon.dev:49160');

    socket.on('notification', function (data) {
        var message = JSON.parse(data);
        $( "#notifications" ).prepend( "<p><strong>" + message.name + "</strong>: " + message.message + "</p>" );
    });
});
