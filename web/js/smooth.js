/**
 * Smooth Scrolling
 * @requires  jQuery
 */
$(document).ready(function () {
	$('.smooth').on('click', function(e) {
		e.preventDefault();
		var hash = this.hash;
		$('html, body').animate({
			scrollTop: $(this.hash).offset().top
		}, 1000, function() {
			window.location.hash = hash;
		});
	});
});
