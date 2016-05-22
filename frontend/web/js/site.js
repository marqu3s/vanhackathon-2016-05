/**
 * Created by joao on 21/05/16.
 */
/* global token, socket */

function checkPlayer() {
    if (token === '') {
        $('#divLogin').removeClass('hidden').addClass('animated fadeIn');
    } else {
        $('#divNewGameJoinGame').removeClass('hidden').addClass('animated fadeIn');
    }
}

function updateGameRoom(id) {
    $('#divGameRoom').load('/site/ajax-join-game', {id: id}, function() {
        $('#divGamesList').removeClass('animated slideInLeft fadeIn').addClass('hidden');
        $(this).removeClass('hidden').addClass('animated slideInLeft');
    });
}

function createNewGame() {
    $.ajax({
        url: '/site/ajax-host-game',
        type: 'post',
        data: {secret_size: $('#secret_size').val()},
        success: function (result) {
            $('#divGameSettings').adddClass('hidden').removeClass('animated fadeIn');
            $('#divGameRoom').load('/site/ajax-join-game', {id: result.id_game}, function() {
                $('#divGamesList').removeClass('animated slideInLeft fadeIn').addClass('hidden');
                $('#divGameRoom').removeClass('hidden').addClass('animated slideInLeft');
            });
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
    // Join player on the game room
    socket.emit('subscribe', 'game-' + id);

    // Update the game room table
    updateGameRoom(id);

    console.log('Player ' + token + ' joinned game ' + id);
}

function setPlayerStatus(idGame, idPlayer, status) {
    $.post('/site/ajax-set-player-status', {idGame: idGame, idPlayer: idPlayer, status: status}, function (result) {
        // Update the game room table
        updateGameRoom(idGame);

        var allPlayersReady = true;
        $.each(result, function(i, match) {
            if (match.player_status != 'ready') allPlayersReady = false;
        });

        if (allPlayersReady) {
            console.log('All players ready! Starting game...');
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
    $('#divGamesList')
        .on('click', '.btn-join-game', function() {
            var id = $(this).data('id');
            joinThisGame(id);
        });

    // Player ready!
    $('#divGameRoom')
        .on('click', '.btn-player-ready', function() {
            var idGame = $(this).data('idgame');
            var idPlayer = $(this).data('idplayer');
            setPlayerStatus(idGame, idPlayer, 'ready');
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
