/**
 * Created by joao on 21/05/16.
 */
/* global token, websocketAddress, io */

var socket = io.connect(websocketAddress);

function checkPlayer() {
    if (token === '') {
        $('#divLogin').removeClass('hidden').addClass('animated fadeIn');
    } else {
        $('#divNewGameJoinGame').removeClass('hidden').addClass('animated fadeIn');
    }
}

function updateGameRoom(id) {
    $('#divGameRoom').load('/site/ajax-update-game-room', {id: id}, function() {
        $('#divGamesList').removeClass('animated slideInLeft fadeIn').addClass('hidden');
        $(this).removeClass('hidden').addClass('animated slideInLeft');
    });
}

function updateGameBoard(id) {
    updateGameRoom(id);
    $('#divGameBoard').load('/site/ajax-get-game-board', {idGame: id}, function() {
        $(this).removeClass('hidden').addClass('animated fadeIn');
    });
}

function startGame(id) {
    console.log('Starting game ' + id);
    $('#divGameRoom').addClass('hidden').removeClass('animated slideInLeft');
    updateGameBoard(id);
}

function subscribePlayer(id) {
    // Join player on the game room
    socket.emit('subscribe', 'game' + id);
    socket.on('game' + id, function (data) {
        var message;
        try {
            message = JSON.parse(data);
        } catch (e) {
            message = data;
        }
        console.log(message);
        if (message.task === 'startgame') {
            startGame(message.idGame);
        } else if (message.task === 'update-room') {
            updateGameRoom(message.idGame);
        } else if (message.task === 'update-board') {
            updateGameBoard(message.idGame);
        } else if (message.task === 'all-players-ready') {
            updateGameBoard(message.idGame);
            $('#btnSubmitGuess').show();
            $('#btnSubmitGuessMsg').hide();
        } else if (message.task === 'player-joinned-game') {
            updateGameRoom(message.idGame);
        }
    });
    console.log('Player subscribed to channel: game' + id);
}

function createNewGame() {
    $.ajax({
        url: '/site/ajax-host-game',
        type: 'post',
        dataType: 'json',
        data: {secret_size: $('#secret_size').val()},
        success: function (data) {
            $('#divGameSettings').addClass('hidden').removeClass('animated fadeIn');
            subscribePlayer(data.id_game);
            updateGameRoom(data.id_game);
            console.log('Player ' + token + ' joinned game ' + data.id_game);
        }
    });
}

function joinExistingGame() {
    // requestApi('game', 'get', null);
    $('#divGamesList').load('/site/ajax-games-list', function() {
        $(this).removeClass('slideInLeft fadeIn').removeClass('hidden').addClass('animated slideInLeft');
    });
    $('#divGameRoom').addClass('hidden');
}

function joinThisGame(id) {
    subscribePlayer(id);

    $.post('/site/ajax-join-game', {idGame: id}, function (result) {
        // Update the game room table
        // updateGameRoom(id);

        // Let other players know
        // socket.emit('player-joinned-game', id);
    });

    // console.log('Player ' + token + ' joinned game ' + id);
}

function setPlayerStatus(idGame, idPlayer, status) {
    $.ajax({
        type: 'post',
        url: '/site/ajax-set-player-status',
        data: {idGame: idGame, idPlayer: idPlayer, status: status},
        complete: function (result) {
            // Update the game room table
            updateGameRoom(idGame);
        }
    });
}

function submitGuess(idGame, idPlayer) {
    var guess = [];
    $('.color-picker').each(function(i, c) {
        if ($(c).val() !== '') {
            guess.push($(c).val());
        }
    });

    if (guess.length === 0) {
        alert('Choose the colors and their positions first.'); //   <--- I know, super ugly... outta time.
        return;
    }

    $.ajax({
        type: 'post',
        url: '/site/ajax-submit-guess',
        data: {idGame: idGame, idPlayer: idPlayer, guess: guess},
        complete: function (result) {
            // updateGameBoard(idGame);
            $('#btnSubmitGuess').hide();
            $('#btnSubmitGuessMsg').show();
        }
    });
}

$(document).ready(function () {

    // If there is no token set, show the login window.
    checkPlayer();

    // Host game
    $('#btnHostGame').click(function() {
        $('#divGameSettings').removeClass('hidden').addClass('animated fadeIn');
    });
    $('#btnCreateGame').click(function() {
        createNewGame();
    });

    // Join game
    $('#btnJoinGame').click(function() {
        joinExistingGame();
    });
    $('#divGamesList').on('click', '.btn-join-game', function() {
            var id = $(this).data('id');
            joinThisGame(id);
        });

    // Player ready!
    $('#divGameRoom').on('click', '.btn-player-ready', function() {
        var idGame = $(this).data('idgame');
        var idPlayer = $(this).data('idplayer');
        setPlayerStatus(idGame, idPlayer, 'ready');
    });
    
    // Submit guess
    $('#divGameBoard').on('click', '#btnSubmitGuess', function () {
        var idGame = $(this).data('idgame');
        var idPlayer = $(this).data('idplayer');
        submitGuess(idGame, idPlayer);
    });

    // Login form submission via ajax
    $('#login-form').submit(function() {
        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function () {
                document.location.reload(); // to reload the cookie
            }
        });

        return false;
    });

});
