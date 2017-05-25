/*
	JavaScript Document for Quantum Resources
	URI: http://www.quantumresources.com/
	Company: Stocks Digital <http://stocksdigital.com/>
	Author: Aakash Bhatia <aakash@stocksdigital.com>
*/

var $ = jQuery.noConflict();

$(document).ready(function() {
	"use strict";
	
	if ($('body').width() < 639) {
		$('nav').addClass('res-nav');	
	}
	
	$('.slider').slick({
		arrows: false,
		autoplay: true,
		pauseOnFocus: true,
		autoplaySpeed: 4500,
		fade: true,
		speed: 1500
	});
	
	$('.slider').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
    	// then let's do this before changing slides
		var ident = $('.slick-current').data('rel');
		$('.'+ident).removeClass('show');
    });
	
	$('.slider').on('afterChange', function(event, slick, currentSlide, nextSlide) {
    	// then let's do this before changing slides
		var ident = $('.slick-current').data('rel');
		$('.'+ident).addClass('show');
    });
	
	$('.toggle-close a').click(function() {
		$('#main-menu').toggleClass('open');
		$('html, body').toggleClass( 'no-scroll' );
		
		return false;
	});
	
	// Controls the responsive menu
	$('.res-nav ul li.menu-item-has-children a').click(function() {
		$(this).parent().find('.sub-menu').slideToggle( 'fast' );
		
		if( $(this).parent().hasClass('menu-item-has-children') ) {
			return false;
		}
	});
});