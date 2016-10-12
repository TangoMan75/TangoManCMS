// Bootstrap tooltips initialisation
$(function() {
    $('[data-toggle="tooltip"]').tooltip()
});

// Smooth scrolling
$(function() {
    $("a.smooth").on("click", function(e) {
        e.preventDefault();
        var hash = this.hash;
        $("html, body").animate({
            scrollTop: $(this.hash).offset().top
        }, 700, function() {
            window.location.hash = hash;
        })
    })
});

/**
 * Floating back to top button.
 * 
 * @author    Matthias Morin <matthias.morin@gmail.com>
 * @requires  jQuery & Bootstap
 */
$(function() {
    /* Injects element. */
    $("body").prepend('<a href="#" id="scroll-top" class="smooth"><span class="glyphicon glyphicon-chevron-up"></span></a>');

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

    /* Smooth scrolls windows */
    $('#scroll-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 700);
        return false;
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
