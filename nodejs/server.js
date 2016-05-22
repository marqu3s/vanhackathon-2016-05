/**
 * Created by joao on 21/05/16.
 */
var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

// Address and ports of the Nodejs Server and Redis Server
var nodeServerPort = 8890;
var redisServerPort = 6379;
var redisServerHost= '172.17.0.4';

server.listen(nodeServerPort);

io.on('connection', function (socket) {

    console.log("new client connected");

    var redisClient = redis.createClient(redisServerPort, redisServerHost);

    redisClient.subscribe('notification');

    redisClient.on("message", function(channel, message) {
        console.log("New message: " + message + ". In channel: " + channel);
        socket.emit(channel, message);
    });

    socket.on('disconnect', function() {
        redisClient.quit();
    });

    socket.on('subscribe', function(room) {
        console.log('joining room', room);
        socket.join(room);
    });

    socket.on('unsubscribe', function(room) {
        console.log('leaving room', room);
        socket.leave(room);
    });

});
