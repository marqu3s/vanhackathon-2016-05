/**
 * Created by joao on 21/05/16.
 */

/* global websocketAddress, io */

$(document).ready(function() {
    var socket = io.connect(websocketAddress);

    socket.on('notification', function (data) {
        var message = JSON.parse(data);
        $( "#notifications" ).prepend( "<p><strong>" + message.name + "</strong>: " + message.message + "</p>" );
    });

    socket.on('game', function (data) {
        var message = JSON.parse(data);
        $( "#notifications" ).prepend( "<p><strong>" + message.name + "</strong>: " + message.message + "</p>" );
    });
});
