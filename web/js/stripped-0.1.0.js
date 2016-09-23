/**
 * Injects ARIA roles into HTML
 *
 * @url      https://www.w3.org/TR/html-aria/
 * @author   Matthias Morin <matthias.morin@gmail.com>
 * @version  0.1.0
 */

/**
 * Initializes ARIA
 * @type  {Object}
 */
window.onload = setupARIA();

/**
 * Adds ARIA role to dom element
 *
 * @param  {String}  strId    id of the target element
 * @param  {String}  strRole  Desired role
 */
function setARIARoleById(strId, strRole) {
	// Find the element to add a role property to
	var objElement = document.getElementById(strId);

	if (objElement)	{
		// Add the role property to the element
		objElement.setAttribute('role', strRole);
	}
}

/**
 * Add role attribute to dom element
 *
 * @param  {Object}  objElement  Target element
 * @param  {String}  strRole     Role to be added to element
 */
function setARIARole(objElement, strRole) {
	if (objElement)	{
		// Add the role property to the element
		objElement.setAttribute('role', strRole);
	}
}

/**
 * Defines ARIA role to be added in the dom
 */
function setupARIA() {

	// First `header` is considered to have role `banner`
	// Site-orientated content, such as the title of the page and the logo.
	var objBanner = document.querySelector('body > header:first-of-type');
	if (objBanner) {
		setARIARole(objBanner, 'banner');
	}

	// All `nav` are considered to have role `navigation`
	// Content that contains the links to navigate this document and/or related documents.
	var objNavs = document.getElementsByTagName('nav');
	for ( i = 0; i < objNavs.length; i++ ) {
		setARIARole(objNavs[i], 'navigation');
	}

	// All `a` in `nav` are considered to have role `menuitem`
	// An option in a group of choices contained by a menu or menubar.
	var objAnchors = document.querySelectorAll('nav a');
	for ( i = 0; i < objAnchors.length; i++ ) {
		setARIARole(objAnchors[i], 'menuitem');
	}

	// All `section > header > h1` and `article > header > h1` are considered to have role `heading`
	// A heading for a section of the page.
	var objHeadings = document.querySelectorAll('section > header > h1, article > header > h1');
	for ( i = 0; i < objHeadings.length; i++ ) {
		setARIARole(objHeadings[i], 'heading');
	}

	// All `main` are considered to have role `main`
	// Content that is directly related to or expands on the central content of the document.
	var objMains = document.getElementsByTagName('main');
	for ( i = 0; i < objMains.length; i++ ) {
		setARIARole(objMains[i], 'main');
	}

	// All `article` are considered to have role `article`
	// Content that makes sense in its own right, such as a complete blog post, a comment on a blog, a post in a forum, and so on.
	var objArticles = document.getElementsByTagName('article');
	for ( i = 0; i < objArticles.length; i++ ) {
		setARIARole(objArticles[i], 'article');
	}

	// All `Aside` are considered to have role `complementary`
	// Supporting content for the main content, but meaningful in its own right when separated from the main content. For example, the weather listed on a portal.
	var objAsides = document.getElementsByTagName('aside');
	for ( i = 0; i < objAsides.length; i++ ) {
		setARIARole(objAsides[i], 'complementary');
	}

	// All `hr` are considered to have role `separator`
	// A divider that separates and distinguishes sections of content or groups of menuitems.
	var objHorizontalRules = document.getElementsByTagName('hr');
	for ( i = 0; i < objHorizontalRules.length; i++ ) {
		setARIARole(objHorizontalRules[i], 'separator');
	}

	// All `Form` are considered to have role `form`
	// A landmark region that contains a collection of items and objects that, as a whole, combine to create a form.
	var objForms = document.getElementsByTagName('form');
	for ( i = 0; i < objForms.length; i++ ) {
		setARIARole(objForms[i], 'form');
	}

	// All `input[type=search]` are considered to have role `search`
	// This section contains a search form to search the site.
	var objInputs = document.querySelectorAll('input[type=search]');
	for ( i = 0; i < objInputs.length; i++ ) {
		setARIARole(objInputs[i], 'search');
	}

	// Last `footer` is considered to have role `contentinfo`
	// Child content, such as footnotes, copyrights, links to privacy statement, links to preferences, and so on.
	var objFooter = document.querySelector('body > footer:last-of-type');
	if (objFooter) {
		setARIARole(objFooter, 'contentinfo');
	}
}

var $articles = $('article');
var $hrs = $('hr');

$articles.css({
	"visibility": "hidden",
});

$hrs.css({
	"width": "0%",
});

/**
 * Animates each article and each hr elements scrolled into view
 */
$(window).on("scroll", function(){
	for( i = 0; i < $articles.length; i++ ) {
		if (isScrolledIntoView($articles[i])) {
			$($articles[i]).addClass("intoview");
			$($articles[i]).css({
				"visibility": "visible",
			});
		}
	}
	for( i = 0; i < $hrs.length; i++ ) {
		if (isScrolledIntoView($hrs[i])) {
			$($hrs[i]).addClass("widen");
		}
	}
});


/**
 * Defines if an element is into view on the page.
 * 
 * @author    "Matthias Morin" <matthias.morin@gmail.com>
 * @requires  jQuery
 * @note      Improved version found on this page
 * @link      http://stackoverflow.com/questions/487073/check-if-element-is-visible-after-scrolling
 * @param     {Object}   objElement  Target dom element
 * @return    {Boolean}              When element is visible on the page
 */
function isScrolledIntoView(objElement) {
	var $element   = $(objElement);
	var $window = $(window);

	var viewportTop    = $window.scrollTop();
	var viewportHeight = $window.height();
	var viewportBottom = viewportTop + viewportHeight;
	var viewportCenter = viewportTop + (viewportHeight /2);

	var elementTop    = $element.offset().top;
	var elementHeight = $element.height();
	var elementBottom = elementTop + elementHeight;

	// Returns true when element is found between viewport's top and bottom. 
	// Or if element is taller than viewport, returns true when element's top is found past the middle of the page. 
	return (
		(elementBottom <= viewportBottom) && (elementTop >= viewportTop) || 
		((elementHeight > viewportHeight /2) && (elementTop < viewportCenter))
	);
}

/**
 * Parallax animation
 *
 * @author         Matthias Morin <matthias.morin@gmail.com>
 * @last-modified  14:00 06/09/2016
 * @requires       jQuery
 */
$(document).ready(function(){
	$('.parallax').each(function(){
		var $element = $(this);
		$(window).scroll(function() {

			// Gets speed parameter from html element
			var intSpeed = $element.data('speed');
			if ( !intSpeed ) {

				// Sets default speed
				intSpeed = 6;
			}

			// Negative value because we're scrolling upwards
			var intY = -( $(window).scrollTop() / intSpeed );

			// Background position
			var strCoords = '50% '+ intY + 'px';

			// Moves background
			$element.css({
				backgroundPosition: strCoords
			});
		});
	});
});

/**
 * Smooth Scrolling
 * @requires  jQuery
 */
$(document).ready(function () {
	$('a').on('click', function(e) {
		e.preventDefault();
		var hash = this.hash;
		$('html, body').animate({
			scrollTop: $(this.hash).offset().top
		}, 1000, function() {
			window.location.hash = hash;
		});
	});
});
