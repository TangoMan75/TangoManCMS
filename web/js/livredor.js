// Bootstrap tooltips initialisation
$(function() {
    $('[data-toggle="tooltip"]').tooltip()
});

/**
 * Floating back to top button.
 *
 * @version   0.1.0
 * @author    Matthias Morin <matthias.morin@gmail.com>
 * @requires  jQuery & Bootstap
 * @requires  smooth scrolling
 */
$(function() {
    /* Injects element. */
    $("body").prepend('<a href="#page-top" id="scroll-top"><span class="glyphicon glyphicon-chevron-up"></span></a>');
    $("body").attr("id", "page-top");

    /* Applies CSS to injected element. */
    $("#scroll-top").css({
        "display": "none",
        "bottom": "5em",
        "right": "5em",
        "font-size": "2em",
        "line-height": "1.5em",
        "text-align": "center",
        "text-indent": "1px",
        "height": "1.5em",
        "width": "1.5em",
        "position": "fixed",
        "z-index": "999",
        "border-radius": "50%",
        "background": "#5cb85c",
        "color": "white"
    });

    /* Detects user srolls */
    $(window).scroll(function() {
        if ( $(window).scrollTop() > 300 ) {
            $('#scroll-top').fadeIn('slow');
        } else {
            $('#scroll-top').fadeOut('slow');
        }
    });
});

// Smooth scrolling
$(function() {
    $("a[href*='#']").on("click", function(e) {
        e.preventDefault();
        var hash = this.hash;
        $("html, body").animate({
            scrollTop: $(this.hash).offset().top
        }, 700, function() {
            window.location.hash = hash;
        })
    })
});
