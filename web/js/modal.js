/**
 * Modal setup
 *
 * Listens to all buttons with data-toggle="modal"
 * @author    Matthias Morin <tangoman@free.fr>
 * @version   0.1.0
 * @requires  jQuery
 */
$(function () {
    // Gets all buttons with attribute data-toggle="modal"
    var objButtons = document.querySelectorAll('button[data-toggle="modal"]');
    if (objButtons) {
        for (i = 0; i < objButtons.length; i++) {
            // Listens to mousedown events on each button
            $(objButtons[i]).on("mousedown", function () {
                var $this   = $(this);
                var strItem = $this.data('item');
                var strPath  = $this.data('path');
                $('.modal .modal-body strong').text(strItem);
                $('.modal .modal-footer a').attr("href", strPath);
            });
        }
    }
});
