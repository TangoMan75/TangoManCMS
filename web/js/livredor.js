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
        }, 1000, function() {
            window.location.hash = hash;
        })
    })
});

// Back to top button
// Inspired from http://html-tuts.com/back-to-top-button-jquery/
$(function() {
    $("body").prepend('<a href="#" id="scroll-top" class="smooth"><span class="glyphicon glyphicon-chevron-up"></span></a>');

    $("#scroll-top").css({
        "display": "none",
        "position": "fixed",
        "z-index": "999",
        "right": "5em",
        "bottom": "5em",
        "width": "1.5em",
        "height": "1.5em",
        "font-size": "2em",
        "text-indent": "1px",
        "text-align": "center",
        "line-height": "1.5em",
        "background": "#27AE61",
        "border-radius": "50%",
        "color": "white"
    });

    $('#scroll-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 1000);
        return false;
    });

    $(window).scroll(function() {
        if ( $(window).scrollTop() > 300 ) {
            $('#scroll-top').fadeIn('slow');
        } else {
            $('#scroll-top').fadeOut('slow');
        }
    });
});
