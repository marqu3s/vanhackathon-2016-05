/**
 * Created by joao on 21/05/16.
 */
/* global token */

$(document).ready(function () {

    // If there is no token set, show the login window.
    if (token === '') {
        $('#divLogin').removeClass('hidden').addClass('animated fadeIn');
    } else {
        $('#divNewGameJoinGame').removeClass('hidden').addClass('animated fadeIn');
    }

    // Host game
    $('#btnHostGame').click(function() {
        $('#divGameSettings').removeClass('hidden').addClass('animated fadeIn');
    });
    $('#btnCreateGame').click(function() {
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
    });


    // Join game
    $('#btnJoinGame').click(function() {
        // requestApi('game', 'get', null);
        $('#divGamesList').load('/site/ajax-games-list', function() {
            $(this).removeClass('slideInLeft fadeIn').removeClass('hidden').addClass('animated slideInLeft');
        });
        $('#divGameRoom').addClass('hidden');
    });
    $('#divGamesList').on('click', '.btn-join-game', function() {
        var id = $(this).data('id');
        $('#divGameRoom').load('/site/ajax-join-game', {id: id}, function() {
            $('#divGamesList').removeClass('animated slideInLeft fadeIn').addClass('hidden');
            $(this).removeClass('hidden').addClass('animated slideInLeft');
        });
    });

    // Player ready
    $('#divGamesList').on('click', '.btn-player-ready', function() {
        // TODO
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
