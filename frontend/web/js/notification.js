/**
 * Created by joao on 21/05/16.
 */

/* global websocketAddress, io */

var socket = io.connect(websocketAddress);

$(document).ready(function() {
    socket.on('message', function (data) {
        console.log(data);
    });

    
    // socket.emit('subscribe', 'roomTwo');

    socket.on('notification', function (data) {
        var message = JSON.parse(data);
        $( "#notifications" ).prepend( "<p><strong>" + message.name + "</strong>: " + message.message + "</p>" );
    });

    socket.on('game', function (data) {
        var message = JSON.parse(data);
        $( "#notifications" ).prepend( "<p><strong>" + message.name + "</strong>: " + message.message + "</p>" );
    });
});
