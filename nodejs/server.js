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
    // redisClient.subscribe('startgame');

    redisClient.on("message", function(channel, message) {
        console.log("New message: " + message + ". In channel: " + channel);
        var messageJson = JSON.parse(message);
        // console.log(messageJson);

        socket.emit('game' + messageJson.idGame, message);
        /*if (messageJson.task === 'startgame') {
            socket.emit('game' + messageJson.idGame, message);
        } else if (messageJson.task === 'update-room') {
            socket.emit('game' + messageJson.idGame, message);
        } else if (messageJson.task === 'update-board') {
            socket.emit('game' + messageJson.idGame, message);
        } else if (messageJson.task === 'player-joinned-game') {
            socket.emit('game' + messageJson.idGame, message);
        } else if (messageJson.task === 'all-players-ready') {
            socket.emit('game' + messageJson.idGame, message);
        }*/

    });

    socket.on('disconnect', function() {
        redisClient.quit();
    });

    socket.on('subscribe', function(room) {
        console.log('joining room', room);
        redisClient.subscribe(room);
        socket.join(room);
    });

    socket.on('player-joinned-game', function(idGame) {
        console.log('A player has joinned the game ', idGame);
        socket.emit('game' + idGame, { task: 'update-room', idGame: idGame });
    });

    /*socket.on('unsubscribe', function(room) {
        console.log('leaving room', room);
        redisClient.unsubscribe(room);
        socket.leave(room);
    });*/

    /*socket.on('start-game', function(room) {
        console.log('Server: Starting game - ' + room);
        socket.emit(room, 'start-game');
    });*/

    /*redisClient.on("startgame", function(channel, message) {
        console.log("New message: " + message + ". In channel: " + channel);
        socket.emit('game' + message.idGame, message);
        // socket.emit('startgame', message);
    });*/

});
