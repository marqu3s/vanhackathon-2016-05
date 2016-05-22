/**
 * Created by joao on 21/05/16.
 */
/* global token */

$(document).ready(function () {

    // If there is no token set, show the login window.
    if (token === '') {
        $('#divLogin').removeClass('hidden').addClass('animated fadeIn');
    }

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
