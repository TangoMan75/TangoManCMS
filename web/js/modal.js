/**
 * Bootstrap modal setup
 *
 * Listens to all buttons and hyperlinks with data-toggle="modal"
 *
 * @url            http://getbootstrap.com/javascript/#modals
 * @author         Matthias Morin <tangoman@free.fr>
 * @version        0.2.0
 * @last-modified  17:00 23/12/2016
 * @requires       jQuery
 * @todo JSONParse / stringify
 */
$(function () {
    // Gets all buttons with attribute data-toggle="modal"
    var objButtons = document.querySelectorAll('button[data-toggle="modal",a[data-toggle="modal"]');
    if (objButtons) {
        for (i = 0; i < objButtons.length; i++) {
            // Listens to mousedown events on each button
            $(objButtons[i]).on("mousedown", function () {
                var $this   = $(this);
                var strText = $this.data('text');
                var strPath = $this.data('href');
                $('.modal .modal-body strong').text(strText);
                $('.modal .modal-footer a').attr('href', strPath);
            });
        }
    }
});
