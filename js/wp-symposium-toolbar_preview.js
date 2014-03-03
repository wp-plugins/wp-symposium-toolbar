/*  Copyright 2013-2014 Guillaume Assire aka AlphaGolf (alphagolf@rocketmail.com)
 *	
 *	This program is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License, version 2, as 
 *	published by the Free Software Foundation.
 *	
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *	
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

jQuery(document).ready(function($){

	// HELPERS
	
	// Reference: http://www.javascripter.net/faq/hextorgb.htm
	// R = hexToR("#FFFFFF");
	// G = hexToG("#FFFFFF");
	// B = hexToB("#FFFFFF");
	function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
	function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
	function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
	function cutHex(h) {if (h.charAt(0)=="#") h = h.substring(1,7); return (h.length == 3) ? h+h :h}
	
	// Reference: http://www.javascripter.net/faq/rgbtohex.htm
	// H = rgbToHex(R,G,B);
	function rgbToHex(R,G,B) {return toHex(R)+toHex(G)+toHex(B)}
	function toHex(n) {
		n = parseInt(n,10);
		if (isNaN(n)) return "00";
		n = Math.max(0,Math.min(n,255));
		return "0123456789ABCDEF".charAt((n-n%16)/16) + "0123456789ABCDEF".charAt(n%16);
	}
	
	// Reference: http://stackoverflow.com/questions/9847580/how-to-detect-safari-chrome-ie-firefox-and-opera-browser
	var isOpera = !!window.opera || navigator.userAgent.indexOf('Opera') >= 0;						// Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
	var isFirefox = typeof InstallTrigger !== 'undefined';   										// Firefox 1.0+
	var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;	// At least Safari 3+: "[object HTMLElementConstructor]"
	var isChrome = !!window.chrome;																	// Chrome 1+
	var isIE = /*@cc_on!@*/false;																	// At least IE6
	
	
	// KEEPERS
	
	var wpstDefaultHeight = "32";
	var wpstResponsiveHeight = "46";
	var $wpstBorderWidth = "0";
	var wpstSubwrapperTop = "26";
	var wpstFontEmpty = '"Open Sans",sans-serif';
	var wpstEmptyColor = "#222222";
	var wpstEmptyColorRgb = "rgb( 34, 34, 34)";
	var wpstHoverEmptyColor = "#333333";
	var wpstHoverEmptyColorRgb = "rgb( 51, 51, 51 )";
	var wpstFontEmptyColor = "#eeeeee";
	var wpstFontEmptyColorRgb = "rgb( 238, 238, 238 )";
	var wpstFontHoverEmptyColor = "#2ea2cc";
	var wpstFontHoverEmptyColorRgb = "rgb( 46, 162, 204 )";
	var wpstIconEmptyColor = "#999999";
	var wpstIconEmptyColorRgb = "rgb( 153, 153, 153 )";
	var wpstIconHoverEmptyColor = "#2ea2cc";
	var wpstIconHoverEmptyColorRgb = "rgb( 46, 162, 204 )";
	var wpstFontShadowHoriz = "0";
	var wpstFontShadowVert = "0";
	
	var wpstMenuEmptyColor = "#333333";
	var wpstMenuEmptyColorRgb = "rgb( 51, 51, 51 )";
	var wpstMenuHoverEmptyColor = "#333333";
	var wpstMenuHoverEmptyColorRgb = "rgb( 51, 51, 51 )";
	var wpstMenuExtEmptyColor = "#4b4b4b";
	var wpstMenuExtEmptyColorRgb = "rgb( 75, 75, 75 )";
	var wpstMenuExtHoverEmptyColor = "#4b4b4b";
	var wpstMenuExtHoverEmptyColorRgb = "rgb( 75, 75, 75 )";
	var wpstMenuFontEmptyColor = "#eeeeee";
	var wpstMenuFontEmptyColorRgb = "rgb( 238, 238, 238 )";
	var wpstMenuFontHoverEmptyColor = "#2ea2cc";
	var wpstMenuFontHoverEmptyColorRgb = "rgb( 46, 162, 204 )";
	var wpstMenuExtFontEmptyColor = "#eeeeee";
	var wpstMenuExtFontEmptyColorRgb = "rgb( 238, 238, 238 )";
	var wpstMenuExtFontHoverEmptyColor = "#2ea2cc";
	var wpstMenuExtFontHoverEmptyColorRgb = "rgb( 46, 162, 204 )";
	
	var wpstFontSizeEmpty = "13px";
	var wpstIconSizeEmpty = 20;
	var wpstFontSizeSmallEmpty = "11px";
	var wpstFontNormal = "normal";
	var wpstFontNone = "none";
	
	var wpstRgbColors = ( $("#wpadminbar").css("background-color").search("rgb") >= 0 );
	
	// Determine gradient string from browser type
	var gradient = "linear-gradient(";
	if ( isChrome )  gradient = "-webkit-linear-gradient(top, ";
	if ( isSafari )  gradient = "-webkit-linear-gradient(top, ";
	if ( isFirefox ) gradient = "-moz-linear-gradient(top, ";
	if ( isIE )      gradient = "-ms-linear-gradient(top, ";
	if ( isOpera )   gradient = "-o-linear-gradient(top, ";
	
	// Determine window width
	document.body.style.overflow = "hidden";
	var wpadminbarHeight = $( "#wpadminbar" ).height();
	var wpadminbarWidth = $( "#wpadminbar" ).width();
	document.body.style.overflow = "";
	
	
	// ENABLERS
	
	$(window).resize(function(){
		if($(this).width() != wpadminbarWidth){
			wpadminbarWidth = $(this).width();
			update_tb_height();
			update_tb_background();
			update_tb_icon_margin();
		}
	});


	function tb_background_image(tbMainColor) {
	
		// Determine Toolbar height
		if ( wpadminbarWidth < 783 )
			var tbHeight = wpstResponsiveHeight;
		else
			var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		
		// Determine colours and lengths of the gradient
		if ( tbMainColor !== "" ) {
			if ( ( $('#wpst_top_colour').val() !== "" ) && ( $('#wpst_top_gradient').val() !== "" ) ) {
				var tbTopColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_top_colour').val() )+", "+hexToG( $('#wpst_top_colour').val() )+", "+hexToB( $('#wpst_top_colour').val() )+")" : $('#wpst_top_colour').val() ;
				var tbTopLength = $('#wpst_top_gradient').val();
			} else {
				var tbTopColor = tbMainColor;
				var tbTopLength = "0";
			}
			if ( ( $('#wpst_bottom_colour').val() !== "" ) && ( $('#wpst_bottom_gradient').val() !== "" ) ) {
				var tbBottomColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_bottom_colour').val() )+", "+hexToG( $('#wpst_bottom_colour').val() )+", "+hexToB( $('#wpst_bottom_colour').val() )+")" : $('#wpst_bottom_colour').val() ;
				var tbBottomLength = $('#wpst_bottom_gradient').val();
			} else {
				var tbBottomColor = tbMainColor;
				var tbBottomLength = "0";
			}
		
		} else {
			var tbMainColor = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
			var tbTopColor = tbMainColor;
			var tbTopLength = "0";
			var tbBottomColor = tbMainColor;
			var tbBottomLength = "0";
		}
		
		// Determine the new "background-image" string from the values above
		return gradient + tbTopColor + " 0px, " + tbMainColor + " " + tbTopLength + "px, " + tbMainColor + " " + (parseInt(tbHeight) - parseInt(tbBottomLength))  + "px, " + tbBottomColor + " " + tbHeight + "px)";
	}
	
	function tb_hover_background_image(tbHoverColor) {
	
		// Determine Toolbar height
		if ( wpadminbarWidth < 783 )
			var tbHeight = wpstResponsiveHeight;
		else
			var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		
		// Determine colours and lengths of the gradient
		if ( tbHoverColor !== "" ) {
			if ( ( $('#wpst_hover_top_colour').val() !== "" ) && ( $('#wpst_hover_top_gradient').val() !== "" ) ) {
				var tbTopColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_top_colour').val() )+", "+hexToG( $('#wpst_hover_top_colour').val() )+", "+hexToB( $('#wpst_hover_top_colour').val() )+")" : $('#wpst_hover_top_colour').val() ;
				var tbTopLength = $('#wpst_hover_top_gradient').val();
			} else {
				var tbTopColor = tbHoverColor;
				var tbTopLength = "0";
			}
			if ( ( $('#wpst_hover_bottom_colour').val() !== "" ) && ( $('#wpst_hover_bottom_gradient').val() !== "" ) ) {
				var tbBottomColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_bottom_colour').val() )+", "+hexToG( $('#wpst_hover_bottom_colour').val() )+", "+hexToB( $('#wpst_hover_bottom_colour').val() )+")" : $('#wpst_hover_bottom_colour').val() ;
				var tbBottomLength = $('#wpst_hover_bottom_gradient').val();
			} else {
				var tbBottomColor = tbHoverColor;
				var tbBottomLength = "0";
			}
			
		} else {
			var tbHoverColor = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
			var tbTopColor = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
			var tbTopLength = "0";
			var tbBottomColor = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
			var tbBottomLength = "0";
		}
		
		// Determine the new "background-image" string from the values above
		return gradient + tbTopColor + " 0px, " + tbHoverColor + " " + tbTopLength + "px, " + tbHoverColor + " " + (parseInt(tbHeight) - parseInt(tbBottomLength))  + "px, " + tbBottomColor + " " + tbHeight + "px)";
	}
	
	
	// UPDATERS
	
	function update_tb_height() {
		
		// Non-responsive only
		if ( wpadminbarWidth < 783 ) return;
		
		// vars
		var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		var tbTop = Math.round( ( tbHeight - wpadminbarHeight )/2 );
		var tbBodyMarginTop = tbHeight - wpstDefaultHeight;
		
		// Put it where it should go
		$("#wpadminbar").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-secondary").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".shortlink-input").css( "height", tbHeight + "px" );
		$("#wpadminbar.ie7").find(".shortlink-input").css( "top", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css( "top", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper .ab-sub-wrapper").css( "top", wpstSubwrapperTop + "px" );
		$('body').css( "margin-top", tbBodyMarginTop + "px" );
		$("#wpbody").css( "margin-top", tbBodyMarginTop + "px" );
		
		$("#wpadminbar").find(".quicklinks > ul > li > a").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li > .ab-item").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li > a span").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li > .ab-item span").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "line-height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item span.ab-label").css( "line-height", tbHeight + "px" );
		
		// JetPack Notes
		var notes = document.getElementById("wp-admin-bar-notes");
		if ( notes ) {
			var divs = document.getElementById("wp-admin-bar-notes").getElementsByTagName("div");
			for (var i in divs) { if ( typeof divs[i].style !== "undefined" ) divs[i].style.cssText = divs[i].style.cssText + "padding-top: " + tbTop  + "px !important;"; }
		}
		
		if ( document.getElementById("wpstMarginTop") )
			var style = document.getElementById("wpstMarginTop");
		else {
			var style = document.createElement('style');
			style.id = 'wpstMarginTop';
		}
		
		style.innerHTML += '#wpadminbar #wp-toolbar > ul > li > .ab-item, #wpadminbar #wp-toolbar > ul > li > .ab-item span, #wpadminbar #wp-toolbar > ul > li > .ab-item:before, #wpadminbar #wp-toolbar > ul > li > .ab-item span.ab-icon:before, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default .ab-icon, #wpadminbar .ab-icon { line-height: '+tbHeight+'px; } ';
		style.innerHTML += '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img { line-height: '+tbHeight+'px; } ';
		
		document.head.appendChild(style);
	}
	
	function update_tb_background() {
		
		// Background
		if ( $('#wpst_background_colour').val() !== "" )
			var tbMainColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_background_colour').val())+", "+hexToG($('#wpst_background_colour').val())+", "+hexToB($('#wpst_background_colour').val())+")" : $('#wpst_background_colour').val();
		else
			var tbMainColor = "";
		
		if ( $('#wpst_hover_background_colour').val() !== "" )
			var tbHoverColor = wpstRgbColors ? "rgb("+hexToR($("#wpst_hover_background_colour").val())+", "+hexToG($("#wpst_hover_background_colour").val())+", "+hexToB($("#wpst_hover_background_colour").val())+")" : $("#wpst_hover_background_colour").val();
		else
			var tbHoverColor = "";
		
		// Gradient
		if ( gradient !== "" ) {
			var tbNormalImage = tb_background_image(tbMainColor);
			var tbHoverImage = tb_hover_background_image(tbHoverColor);
		
		} else {
			var tbNormalImage = "";
			var tbHoverImage = "";
		}
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").css( "background-image", tbNormalImage );
		$("#wpadminbar .quicklinks").css( "background-image", tbNormalImage );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "background-image", tbNormalImage );
		$("#wpadminbar").css( "background-color", tbMainColor );
		$("#wpadminbar .quicklinks").css( "background-color", tbMainColor );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "background-color", tbMainColor );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "background-color", tbHoverColor );
			$(this).find("> a").css( "background-image", tbHoverImage );
			$(this).find("> .ab-item").css( "background-color", tbHoverColor );
			$(this).find("> .ab-item").css( "background-image", tbHoverImage );
			$(this).find("> .ab-item > .ab-label").css( "background", "transparent" );
		},function(){
			$(this).find("> a").css( "background-color", tbMainColor );
			$(this).find("> a").css( "background-image", tbNormalImage );
			$(this).find("> .ab-item").css( "background-color", tbMainColor );
			$(this).find("> .ab-item").css( "background-image", tbNormalImage );
		} );
	}
	
	function update_tb_borders() {
		
		// Width / Style / Colours
		var tbBorderWidth =  ( ( parseInt($('#wpst_border_width').val()) == '' ) || ( parseInt($('#wpst_border_width').val()) < 0 ) || ( parseInt($('#wpst_border_width').val()) != $('#wpst_border_width').val() ) ) ? $wpstBorderWidth : $('#wpst_border_width').val();
		var tbBorderStyle = $('#wpst_border_style').val();
		
		if ( ( tbBorderWidth != $wpstBorderWidth ) && ( tbBorderStyle != '' ) && ( $('#wpst_border_left_colour').val() != '' ) ) {
			
			var tbBorderLeftColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_border_left_colour').val() )+", "+hexToG($('#wpst_border_left_colour').val())+", "+hexToB($('#wpst_border_left_colour').val())+")" : $('#wpst_border_left_colour').val();
			
			// Gather all values together
			if ( $('#wpst_border_right_colour').val() != '' ) {
				var tbBorderRightColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_border_right_colour').val() )+", "+hexToG($('#wpst_border_right_colour').val())+", "+hexToB($('#wpst_border_right_colour').val())+")" : $('#wpst_border_right_colour').val();
				
				var tbBorderLeft = ( $('#wpst_border_style').val() == 'none' ) ? 'none' : tbBorderWidth + 'px ' + tbBorderStyle + ' ' + tbBorderLeftColor;
				var tbBorderRight = ( $('#wpst_border_style').val() == 'none' ) ? 'none' : tbBorderWidth + 'px ' + tbBorderStyle + ' ' + tbBorderRightColor;
				var divider = "none";
			
			} else {
				var tbBorderLeft = "none";
				var tbBorderRight = "none";
				var divider = ( $('#wpst_border_style').val() == 'none' ) ? 'none' : tbBorderWidth + 'px ' + tbBorderStyle + ' ' + tbBorderLeftColor;
			}
		
		} else {
			var tbBorderLeft = "none";
			var tbBorderRight = "none";
			var divider = "none";
		}
		
		// Put it where it should go
		// Two-colour Borders
		$("#wpadminbar").find(".quicklinks > ul > li > a").css("border-left", tbBorderLeft);
		$("#wpadminbar").find(".quicklinks > ul > li > a").css("border-right", tbBorderRight);
		$("#wpadminbar").find(".quicklinks > ul > li > .ab-empty-item").css("border-left", tbBorderLeft);
		$("#wpadminbar").find(".quicklinks > ul > li > .ab-empty-item").css("border-right", tbBorderRight);
		$("#wpadminbar").find(".quicklinks > .ab-top-secondary > li > a").css("border-left", tbBorderLeft);
		$("#wpadminbar").find(".quicklinks > .ab-top-secondary > li > a").css("border-right", tbBorderRight);
		$("#wpadminbar").find(".quicklinks > .ab-top-secondary > li > .ab-empty-item").css("border-left", tbBorderLeft);
		$("#wpadminbar").find(".quicklinks > .ab-top-secondary > li > .ab-empty-item").css("border-right", tbBorderRight);
		$("#wpadminbar").find(".quicklinks > ul > li:last-child > a").css("border-left", tbBorderLeft);
		$("#wpadminbar").find(".quicklinks > ul > li:last-child > a").css("border-right", tbBorderRight);
		$("#wpadminbar").find(".quicklinks > ul > li:last-child > .ab-empty-item").css("border-left", tbBorderLeft);
		$("#wpadminbar").find(".quicklinks > ul > li:last-child > .ab-empty-item").css("border-right", tbBorderRight);
		
		// Monochrom dividers
		$("#wpadminbar").find(".quicklinks > ul > li").css("border-left", divider);
		$("#wpadminbar").find(".quicklinks > ul > li").css("border-right", "none");
		
		// In case only one color was selected, each end should have a nicely bordered item  :)
		if ( $('#wpst_border_right_colour').val() == '' ) {
			$("#wpadminbar").find(".quicklinks > ul > li:last-child").css("border-right", divider);
			$("#wpadminbar").find(".quicklinks .ab-top-secondary > li:last-child").css("border-right", "none");
			$("#wpadminbar").find(".quicklinks .ab-top-secondary > li:first-child").css("border-right", divider);
		}
	}
	
	function update_tb_icon_margin() {
		
		// Non-responsive only
		if ( wpadminbarWidth < 783 ) return;
		
		// vars
		var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		var tbIconSize = ( $('#wpst_icon_size').val() !== "" ) ? ( parseInt( $('#wpst_icon_size').val() ) ) : wpstIconSizeEmpty;
		
		var sitesMarginTop = -4;
		
		// WP Logo and Updates icons
		if ( tbIconSize < wpstIconSizeEmpty ) {
			var wMarginTop = Math.round( ( ( tbIconSize - wpstIconSizeEmpty ) /2 ) - 4 );
		} else {
			var wMarginTop = -4;
		}
		
		// WP Symposium icons
		if ( tbIconSize < wpstIconSizeEmpty - 2 ) {
			var sMarginTop = Math.round( ( ( tbIconSize - wpstIconSizeEmpty ) /2 ) - 5 );
		} else {
			var sMarginTop = -5;
		}
		
		// New Content icon
		var newMarginTop = wMarginTop + 2;
		
		// Put it where it should go
		if ( document.getElementById("wpstIconMarginTop") )
			var style = document.getElementById("wpstIconMarginTop");
		else {
			var style = document.createElement('style');
			style.id = 'wpstIconMarginTop';
		}
		
		style.innerHTML += '#wpadminbar #wp-admin-bar-my-sites > .ab-item:before, #wpadminbar #wp-admin-bar-site-name > .ab-item:before, #wpadminbar #wp-admin-bar-my-wpms-admin > .ab-item:before { top: '+sitesMarginTop+'px; } ';
		style.innerHTML += '#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before, #wpadminbar #wp-admin-bar-updates .ab-icon:before { top: '+wMarginTop+'px; } ';
		style.innerHTML += '#wpadminbar #wp-admin-bar-new-content .ab-icon:before, #wpadminbar #wp-admin-bar-comments .ab-icon:before { top: '+newMarginTop+'px; } ';
		style.innerHTML += '#wpadminbar #wp-admin-bar-my-symposium-admin > .ab-item > span.ab-icon:before, #wpadminbar li.symposium-toolbar-notifications-mail > .ab-item > .ab-icon:before, #wpadminbar li.symposium-toolbar-notifications-friendship > .ab-item > .ab-icon:before { top: '+sMarginTop+'px; } ';
		style.innerHTML += '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default > #wp-admin-bar-search #adminbarsearch input.adminbar-input { top: '+Math.round( ( tbHeight - wpstDefaultHeight ) /2 )+'px; } ';
		
		document.head.appendChild(style);
	}
	
	function update_tb_icon_size() {
		
		// Non-responsive only
		if ( wpadminbarWidth < 783 ) return;
		
		var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		var tbIconSize = ( $('#wpst_icon_size').val() !== "" ) ? ( parseInt( $('#wpst_icon_size').val() ) ) : wpstIconSizeEmpty;
		var tbHoverIconSize = ( $('#wpst_hover_icon_size').val() !== "" ) ? ( parseInt( $('#wpst_hover_icon_size').val() ) ) : tbIconSize;
		if ( ( tbIconSize > 0 ) && ( tbHoverIconSize > 0 ) ) {
			var tbScale = Math.round( ( ( tbHoverIconSize * 100 ) / tbIconSize ) / 100 );
			var tbTransform = 'transform:scale('+tbScale+'); -ms-transform:scale('+tbScale+'); -webkit-transform:scale('+tbScale+'); transition: all 0.25s; ';
		} else
			var tbTransform = 'transform:scale(1); -ms-transform:scale(1); -webkit-transform:scale(1); transition: all 0.25s; ';
		
		// Put it where it should go
		if ( document.getElementById("wpstIconSize") )
			var style = document.getElementById("wpstIconSize");
		else {
			var style = document.createElement('style');
			style.id = 'wpstIconSize';
		}
		
		style.innerHTML = '#wpadminbar .ab-item span:before, #wpadminbar .ab-top-menu > li.menupop > .ab-item:before, #wpadminbar li #adminbarsearch:before, #wpadminbar #wp-admin-bar-my-symposium-admin > .ab-item > span.ab-icon:before, #wpadminbar li.symposium-toolbar-notifications-mail > .ab-item > .ab-icon:before, #wpadminbar li.symposium-toolbar-notifications-friendship > .ab-item > .ab-icon:before, #wpadminbar #wp-toolbar > ul > li > .ab-item span.ab-icon, #wpadminbar #wp-toolbar > ul > li > .ab-item:before, #wpadminbar #wp-toolbar > ul > li > .ab-item span.ab-icon:before { font-size: '+tbIconSize+'px !Important; } ';
		
		style.innerHTML += '#wp-admin-bar-wp-logo > a { width: '+tbIconSize+'px; } ';
		style.innerHTML += '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img { width: '+ (tbIconSize - 4) +'px; height: '+ (tbIconSize - 4) +'px; margin-top: -3px; } ';
		style.innerHTML += '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default > #wp-admin-bar-search #adminbarsearch input.adminbar-input { height: '+tbIconSize+'px; padding-left: '+tbIconSize+'px; } ';
		
		style.innerHTML += '#wpadminbar li:hover .ab-item span:before, #wpadminbar li.hover .ab-item span:before, #wpadminbar li.menupop:hover .ab-item span:before, #wpadminbar li.menupop.hover .ab-item span:before, #wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, #wpadminbar li:hover #adminbarsearch:before, #wpadminbar #wp-admin-bar-wp-logo:hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.menupop:hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.menupop.hover > .ab-item .ab-icon, #wpadminbar li:hover > .ab-item > .ab-icon, #wpadminbar li.hover > .ab-item > .ab-icon, #wpadminbar li.menupop:hover > .ab-item > .ab-icon, #wpadminbar li.menupop.hover > .ab-item > .ab-icon, #wpadminbar li:hover > .ab-item > .ab-icon:before, #wpadminbar li.hover > .ab-item > .ab-icon:before, #wpadminbar li.menupop:hover > .ab-item > .ab-icon:before, #wpadminbar li.menupop.hover > .ab-item > .ab-icon:before, #wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar:hover > a img { '+tbTransform+' } ';
		
		document.head.appendChild(style);
	}
	
	function update_tb_font_size() {
		
		// Non-responsive only
		if ( wpadminbarWidth < 783 ) return;
		
		var tbFontSize = ( $('#wpst_font_size').val() !== "" ) ? $('#wpst_font_size').val() : wpstFontSizeEmpty;
		var tbHoverFontSize = ( $('#wpst_hover_font_size').val() !== "" ) ? $('#wpst_hover_font_size').val() : tbFontSize;
		var tbFontSizeSmall = ( $('#wpst_font_size').val() !== "" ) ? ( parseInt( $('#wpst_font_size').val() ) - 2 ) : wpstFontSizeSmallEmpty;
		var menuFontSize = ( $('#wpst_menu_font_size').val() !== "" ) ? $('#wpst_menu_font_size').val() : tbFontSize;
		var menuFontSizeSmall = ( $('#wpst_menu_font_size').val() !== "" ) ? ( parseInt( $('#wpst_menu_font_size').val() ) - 2 ) : tbFontSizeSmall;
		var tbTransition = "all 0.25s" ;
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-item").css( "font-size", tbFontSize + "px" );
		$("#wpadminbar").find(".ab-item > span.ab-label").css( "font-size", tbFontSize + "px" );
		$("#wpadminbar").find(".ab-submenu *").css( "font-size", menuFontSize + "px" );
		$("#wpadminbar").find("#wp-admin-bar-user-info > .ab-item > span").css( "font-size", menuFontSizeSmall + "px" );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> .ab-item").css( "font-size", tbHoverFontSize + "px" );
			$(this).find("> .ab-item > span.ab-label").css( "font-size", tbHoverFontSize + "px" );
			$(this).find("> .ab-item").css( "transition", tbTransition );
			$(this).find("> .ab-item > span.ab-label").css( "transition", tbTransition );
		},function(){
			$(this).find("> .ab-item").css( "font-size", tbFontSize + "px" );
			$(this).find("> .ab-item > span.ab-label").css( "font-size", tbFontSize + "px" );
		} );
	}
	
	function update_tb_font_colour() {
	
		if ( $('#wpst_font_colour').val() !== "" )
			var tbFontColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_font_colour').val() )+", "+hexToG( $('#wpst_font_colour').val() )+", "+hexToB( $('#wpst_font_colour').val() )+")" : $('#wpst_font_colour').val() ;
		else
			var tbFontColor = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
		
		if ( $('#wpst_icon_colour').val() !== "" )
			var tbIconColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_icon_colour').val() )+", "+hexToG( $('#wpst_icon_colour').val() )+", "+hexToB( $('#wpst_icon_colour').val() )+")" : $('#wpst_icon_colour').val() ;
		else
			var tbIconColor = wpstRgbColors ? wpstIconEmptyColorRgb : wpstIconEmptyColor;
		
		if ( $('#wpst_hover_font_colour').val() !== "" )
			var tbHoverFontColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_font_colour').val() )+", "+hexToG( $('#wpst_hover_font_colour').val() )+", "+hexToB( $('#wpst_hover_font_colour').val() )+")" : $('#wpst_hover_font_colour').val() ;
		else
			var tbHoverFontColor = wpstRgbColors ? wpstFontHoverEmptyColorRgb : wpstFontHoverEmptyColor;
		
		if ( $('#wpst_hover_icon_colour').val() !== "" )
			var tbHoverIconColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_icon_colour').val() )+", "+hexToG( $('#wpst_hover_icon_colour').val() )+", "+hexToB( $('#wpst_hover_icon_colour').val() )+")" : $('#wpst_hover_icon_colour').val() ;
		else
			var tbHoverIconColor = wpstRgbColors ? wpstFontHoverEmptyColorRgb : wpstFontHoverEmptyColor;
		
		// Put it where it should go
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "color", tbFontColor );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > span.ab-label").css( "color", tbFontColor );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > span.ab-icon").css( "color", tbIconColor );
		
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> .ab-item").css( "color", tbHoverFontColor );
			$(this).find("> .ab-item .ab-label").css( "color", tbHoverFontColor );
			$(this).find("> .ab-item .ab-icon").css( "color", tbHoverIconColor );
		},function(){
			$(this).find("> .ab-item").css( "color", tbFontColor );
			$(this).find("> .ab-item .ab-label").css( "color", tbFontColor );
			$(this).find("> .ab-item .ab-icon").css( "color", tbIconColor );
		});
		
		if ( document.getElementById("wpstFontColour") )
			var style = document.getElementById("wpstFontColour");
		else {
			var style = document.createElement('style');
			style.id = 'wpstFontColour';
		}
		
		// Fonticons
		style.innerHTML = '#wpadminbar #wp-admin-bar-root-default .ab-icon, #wpadminbar .ab-item span:before, #wpadminbar .ab-top-menu > li.menupop > .ab-item:before, #wpadminbar li #adminbarsearch:before, #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon, #wpadminbar li > .ab-item > .ab-icon:before { color: '+tbIconColor+'; } ';
		style.innerHTML += '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img { border-color: '+tbIconColor+'; background-color: '+tbIconColor+'; } ';
		
		// Fonticons Hover
		style.innerHTML += '#wpadminbar li:hover .ab-item span:before, #wpadminbar li.hover .ab-item span:before, #wpadminbar li.menupop:hover .ab-item span:before, #wpadminbar li.menupop.hover .ab-item span:before, #wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, #wpadminbar li:hover #adminbarsearch:before, #wpadminbar li:hover > .ab-item > .ab-icon:before, #wpadminbar li.hover > .ab-item > .ab-icon:before, #wpadminbar li.menupop:hover > .ab-item > .ab-icon:before, #wpadminbar li.menupop.hover > .ab-item > .ab-icon:before { color: '+tbHoverIconColor+'; } ';
		style.innerHTML += '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar:hover > a img { border-color: '+tbHoverIconColor+'; background-color: '+tbHoverIconColor+'; } ';
		
		document.head.appendChild(style);
	}
	
	function update_tb_font_shadow() {
		
		// Normal font shadow
		var tbFontShadowHoriz = ( ( $('#wpst_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_font_h_shadow').val()) == $('#wpst_font_h_shadow').val() ) ) ? $('#wpst_font_h_shadow').val()+"px " : "0px ";
		var tbFontShadowVert = ( ( $('#wpst_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_font_v_shadow').val()) == $('#wpst_font_v_shadow').val() ) ) ? $('#wpst_font_v_shadow').val()+"px " : "0px ";
		var tbFontShadowBlur = ( ( $('#wpst_font_shadow_blur').val() !== "" ) && ( parseInt($('#wpst_font_shadow_blur').val()) == $('#wpst_font_shadow_blur').val() ) ) ? $('#wpst_font_shadow_blur').val()+"px " : "0px ";
		
		if ( $('#wpst_font_shadow_colour').val() !== '' )
			var tbFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_font_shadow_colour').val())+", "+hexToG($('#wpst_font_shadow_colour').val())+", "+hexToB($('#wpst_font_shadow_colour').val())+")" : $('#wpst_font_shadow_colour').val();
		else
			var tbFontShadowColor = "";
		
		if ( ( tbFontShadowHoriz == "0px " ) && ( tbFontShadowVert == "0px " ) && ( tbFontShadowBlur == "0px " ) )
			var tbNormalFontShadow = "none";
		else
			var tbNormalFontShadow = tbFontShadowHoriz + tbFontShadowVert + tbFontShadowBlur + tbFontShadowColor;
		
		// Hover font shadow
		var tbHoverFontShadowHoriz = ( ( $('#wpst_hover_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_hover_font_h_shadow').val()) == $('#wpst_hover_font_h_shadow').val() ) ) ? $('#wpst_hover_font_h_shadow').val()+"px " : "0px ";
		var tbHoverFontShadowVert = ( ( $('#wpst_hover_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_hover_font_v_shadow').val()) == $('#wpst_hover_font_v_shadow').val() ) ) ? $('#wpst_hover_font_v_shadow').val()+"px " : "0px ";
		var tbHoverFontShadowBlur = ( ( $('#wpst_hover_font_shadow_blur').val() !== "" ) && ( parseInt($('#wpst_hover_font_shadow_blur').val()) == $('#wpst_hover_font_shadow_blur').val() ) ) ? $('#wpst_hover_font_shadow_blur').val()+"px " : "0px ";
		
		if ( $('#wpst_hover_font_shadow_colour').val() !== '' )
			var tbHoverFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_hover_font_shadow_colour').val())+", "+hexToG($('#wpst_hover_font_shadow_colour').val())+", "+hexToB($('#wpst_hover_font_shadow_colour').val())+")" : $('#wpst_hover_font_shadow_colour').val();
		else
			var tbHoverFontShadowColor = "";
		
		if ( ( tbHoverFontShadowHoriz == "0px " ) && ( tbHoverFontShadowVert == "0px " ) && ( tbHoverFontShadowBlur == "0px " ) )
			var tbHoverFontShadow = "none";
		else
			var tbHoverFontShadow = tbHoverFontShadowHoriz + tbHoverFontShadowVert + tbHoverFontShadowBlur + tbHoverFontShadowColor;
		
		// Put it where it should go
		if ( document.getElementById("wpstFontShadowColour") )
			var style = document.getElementById("wpstFontShadowColour");
		else {
			var style = document.createElement('style');
			style.id = 'wpstFontShadowColour';
		}
		
		// Labels
		style.innerHTML = '#wpadminbar .ab-top-menu > li > .ab-item, #wpadminbar .ab-top-menu > li.menupop > .ab-item, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item > span, #wpadminbar > #wp-toolbar li span.ab-label,   #wpadminbar .ab-top-menu > li > .ab-item, #wpadminbar > #wp-toolbar span.ab-label, #wpadminbar > #wp-toolbar span.noticon { text-shadow: '+tbNormalFontShadow+'; } ';
		
		// Fonticons
		style.innerHTML += '#wpadminbar #wp-admin-bar-root-default .ab-icon, #wpadminbar .ab-top-menu > li > .ab-item:before, #wpadminbar .ab-top-menu > li > .ab-item:after, #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon, #wpadminbar .ab-top-menu > li > .ab-item > .ab-icon:before, #wpadminbar .ab-top-menu > li > .ab-item > span:before, #wpadminbar .ab-top-menu > li > .ab-item:before, #wpadminbar li #adminbarsearch:before { text-shadow: '+tbNormalFontShadow+'; } ';
		
		// Avatar
		style.innerHTML += '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar > a img { box-shadow: '+tbNormalFontShadow+'; -webkit-box-shadow: '+tbNormalFontShadow+'; } ';
		
		// Labels Hover
		// Labels once menupop is on
		style.innerHTML += '#wpadminbar .ab-top-menu > li.menupop:hover > .ab-item, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li.menupop:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li.menupop.hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.menupop:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li.menupop.hover span.ab-label, ';
		// Labels before menupop is on
		style.innerHTML += '#wpadminbar > #wp-toolbar > #wp-admin-bar-root-default li:hover span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary li:hover span.ab-label, ';
		// admin-bar.css:215
		style.innerHTML += '#wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar > #wp-toolbar li:hover span.ab-label, #wpadminbar > #wp-toolbar li.hover span.ab-label, #wpadminbar > #wp-toolbar a:focus span.ab-label { text-shadow: '+tbHoverFontShadow+'; } ';
		
		// Fonticons Hover
		style.innerHTML += '#wpadminbar li:hover #adminbarsearch:before, #wpadminbar li:hover .ab-item span:before, #wpadminbar li.hover .ab-item span:before, #wpadminbar li.menupop:hover .ab-item span:before, #wpadminbar li.menupop.hover .ab-item span:before, #wpadminbar .ab-top-menu > li:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop:hover > .ab-item:before, #wpadminbar .ab-top-menu > li.menupop.hover > .ab-item:before, #wpadminbar #wp-admin-bar-wp-logo:hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.menupop:hover > .ab-item .ab-icon, #wpadminbar #wp-admin-bar-wp-logo.menupop.hover > .ab-item .ab-icon, #wpadminbar li:hover > .ab-item > .ab-icon:before, #wpadminbar li.hover > .ab-item > .ab-icon:before, #wpadminbar li.menupop:hover > .ab-item > .ab-icon:before, #wpadminbar li.menupop.hover > .ab-item > .ab-icon:before { text-shadow: '+tbHoverFontShadow+'; } ';
		
		// Avatar Hover
		style.innerHTML += '#wpadminbar .quicklinks li#wp-admin-bar-my-account.with-avatar:hover > a img { box-shadow: '+tbHoverFontShadow+'; -webkit-box-shadow: '+tbHoverFontShadow+'; } ';
		
		document.getElementsByTagName('head')[0].appendChild(style);
	}
	
	function update_menu_background() {
		
		// Background
		if ( $('#wpst_menu_background_colour').val() !== "" )
			var menuMainColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_background_colour').val())+", "+hexToG($('#wpst_menu_background_colour').val())+", "+hexToB($('#wpst_menu_background_colour').val())+")" : $('#wpst_menu_background_colour').val();
		else
			var menuMainColor = wpstRgbColors ? wpstMenuEmptyColorRgb : wpstMenuEmptyColor;
		
		if ( $('#wpst_menu_hover_background_colour').val() !== "" )
			var menuHoverColor = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_background_colour").val())+", "+hexToG($("#wpst_menu_hover_background_colour").val())+", "+hexToB($("#wpst_menu_hover_background_colour").val())+")" : $("#wpst_menu_hover_background_colour").val();
		else
			var menuHoverColor = wpstRgbColors ? wpstMenuHoverEmptyColorRgb : wpstMenuHoverEmptyColor;
		
		if ( $('#wpst_menu_ext_background_colour').val() !== "" )
			var menuMainColorExt = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_ext_background_colour').val())+", "+hexToG($('#wpst_menu_ext_background_colour').val())+", "+hexToB($('#wpst_menu_ext_background_colour').val())+")" : $('#wpst_menu_ext_background_colour').val();
		else
			var menuMainColorExt = wpstRgbColors ? wpstMenuExtEmptyColorRgb : wpstMenuExtEmptyColor;
		
		if ( $('#wpst_menu_hover_ext_background_colour').val() !== "" )
			var menuHoverColorExt = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_ext_background_colour").val())+", "+hexToG($("#wpst_menu_hover_ext_background_colour").val())+", "+hexToB($("#wpst_menu_hover_ext_background_colour").val())+")" : $("#wpst_menu_hover_ext_background_colour").val();
		else
			var menuHoverColorExt = wpstRgbColors ? wpstMenuExtHoverEmptyColorRgb : wpstMenuExtHoverEmptyColor;
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-sub-wrapper > ul").css( "background-color", menuMainColor );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").css( "background-color", menuMainColor );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").css( "background-color", menuMainColor );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "background-color", menuMainColor );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary").css( "background-color", menuMainColorExt );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li").css( "background-color", menuMainColorExt );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper ul").css( "background-color", menuMainColorExt );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li").css( "background-color", menuMainColorExt );
		
		// Hover
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).css( "background-color", menuHoverColor );
		},function(){
			$(this).css( "background-color", menuMainColor );
		});
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).css( "background-color", menuHoverColor );
			$(this).find("span").css( "background-color", "transparent" );
		},function(){
			$(this).css( "background-color", menuMainColor );
			$(this).find("span").css( "background-color", menuMainColor );
		});
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li").hover(function(){
			$(this).css( "background-color", menuHoverColorExt );
		},function(){
			$(this).css( "background-color", menuMainColorExt );
		});
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li").hover(function(){
			$(this).css( "background-color", menuHoverColorExt );
		},function(){
			$(this).css( "background-color", menuMainColorExt );
		});
	}
	
	function update_menu_font_colour() {
	
		if ( $('#wpst_menu_font_colour').val() !== "" )
			var menuMainColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_font_colour').val())+", "+hexToG($('#wpst_menu_font_colour').val())+", "+hexToB($('#wpst_menu_font_colour').val())+")" : $('#wpst_menu_font_colour').val();
		else
			var menuMainColor = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
			
		if ( $('#wpst_menu_hover_font_colour').val() !== "" )
			var menuHoverColor = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_font_colour").val())+", "+hexToG($("#wpst_menu_hover_font_colour").val())+", "+hexToB($("#wpst_menu_hover_font_colour").val())+")" : $("#wpst_menu_hover_font_colour").val();
		else
			var menuHoverColor = wpstRgbColors ? wpstMenuFontHoverEmptyColorRgb : wpstMenuFontHoverEmptyColor;
		
		if ( $('#wpst_menu_ext_font_colour').val() !== "" )
			var menuMainColorExt = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_ext_font_colour').val())+", "+hexToG($('#wpst_menu_ext_font_colour').val())+", "+hexToB($('#wpst_menu_ext_font_colour').val())+")" : $('#wpst_menu_ext_font_colour').val();
		else
			var menuMainColorExt = wpstRgbColors ? wpstMenuExtFontEmptyColorRgb : wpstMenuExtFontEmptyColor;
			
		if ( $('#wpst_menu_hover_ext_font_colour').val() !== "" )
			var menuHoverColorExt = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_ext_font_colour").val())+", "+hexToG($("#wpst_menu_hover_ext_font_colour").val())+", "+hexToB($("#wpst_menu_hover_ext_font_colour").val())+")" : $("#wpst_menu_hover_ext_font_colour").val();
		else
			var menuHoverColorExt = wpstRgbColors ? wpstMenuExtFontHoverEmptyColorRgb : wpstMenuExtFontHoverEmptyColor;
		
		// Put it where it should go
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "color", menuMainColor );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "color", menuMainColor );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li > .ab-item").css( "color", menuMainColorExt );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li > .ab-item").css( "color", menuMainColorExt );
		$("#wpadminbar").find(".quicklinks li .blavatar").css( "color", menuMainColorExt );
		
		// Hover / Focus
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> .ab-item").css( "color", menuHoverColor );
		},function(){
			$(this).find("> .ab-item").css( "color", menuMainColor );
		});
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "color", menuHoverColor );
		},function(){
			$(this).find("span").css( "color", menuMainColor );
		});
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li").hover(function(){
			$(this).find("> .ab-item").css( "color", menuHoverColorExt );
		},function(){
			$(this).find("> .ab-item").css( "color", menuMainColorExt );
		});
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li").hover(function(){
			$(this).find("> .ab-item").css( "color", menuHoverColorExt );
		},function(){
			$(this).find("> .ab-item").css( "color", menuMainColorExt );
		});
		$("#wpadminbar").find(".quicklinks li a").hover(function(){
			$(this).find(".blavatar").css( "color", menuHoverColorExt );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find(".blavatar").css( "color", menuMainColorExt );
		}});
		
		// Arrows & Blavatar
		if ( document.getElementById("wpstMenuFontColour") )
			var style = document.getElementById("wpstMenuFontColour");
		else {
			var style = document.createElement('style');
			style.id = 'wpstMenuFontColour';
		}
		
		style.innerHTML = '#wpadminbar .menupop .menupop > .ab-item:before { color: '+menuMainColor+'; } #wpadminbar .menupop .menupop > .ab-item { color: '+menuMainColor+'; } ';
		style.innerHTML += '#wpadminbar .menupop .ab-sub-secondary .menupop > .ab-item:before { color: '+menuMainColorExt+'; } #wpadminbar .menupop .ab-sub-secondary .menupop > .ab-item { color: '+menuMainColorExt+'; } ';
		style.innerHTML += '#wpadminbar .quicklinks .ab-sub-secondary > li > a > .blavatar { color: '+menuMainColorExt+'; } #wpadminbar .quicklinks .ab-sub-secondary > li > a > .blavatar:before { color: '+menuMainColorExt+'; } ';
		
		style.innerHTML += '#wpadminbar .menupop .menupop > .ab-item:hover:before { color: '+menuHoverColor+'; } #wpadminbar .menupop li.menupop:hover > .ab-item:before { color: '+menuHoverColor+'; } ';
		style.innerHTML += '#wpadminbar .quicklinks .ab-sub-secondary > li.hover > a > .blavatar { color: '+menuHoverColorExt+'; } #wpadminbar .quicklinks .ab-sub-secondary > li.hover > a > .blavatar:before { color: '+menuHoverColorExt+'; }  #wpadminbar .quicklinks li a:hover .blavatar:before { color: '+menuHoverColorExt+'; } ';
		style.innerHTML += '#wpadminbar .menupop .ab-sub-secondary > .menupop > .ab-item:hover:before, #wpadminbar .menupop .ab-sub-secondary > li.menupop:hover > .ab-item:before, #wpadminbar .menupop .ab-sub-secondary > li.menupop.hover > .ab-item:before { color: '+menuHoverColorExt+'; } ';
		
		document.head.appendChild(style);
	}
	
	function update_menu_font_shadow() {
		
		// Normal font shadow
		var menuFontShadowHoriz = ( ( $('#wpst_menu_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_font_h_shadow').val()) == $('#wpst_menu_font_h_shadow').val() ) ) ? $('#wpst_menu_font_h_shadow').val()+"px " : "0px ";
		var menuFontShadowVert = ( ( $('#wpst_menu_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_font_v_shadow').val()) == $('#wpst_menu_font_v_shadow').val() ) ) ? $('#wpst_menu_font_v_shadow').val()+"px " : "0px ";
		var menuFontShadowBlur = ( ( $('#wpst_menu_font_shadow_blur').val() !== "" ) && ( parseInt($('#wpst_menu_font_shadow_blur').val()) == $('#wpst_menu_font_shadow_blur').val() ) ) ? $('#wpst_menu_font_shadow_blur').val()+"px " : "0px ";
		
		if ( $('#wpst_menu_font_shadow_colour').val() !== '' )
			var menuFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_font_shadow_colour').val())+", "+hexToG($('#wpst_menu_font_shadow_colour').val())+", "+hexToB($('#wpst_menu_font_shadow_colour').val())+")" : $('#wpst_menu_font_shadow_colour').val();
		else
			var menuFontShadowColor = "";
		
		if ( ( menuFontShadowHoriz == "0px " ) && ( menuFontShadowVert == "0px " ) && ( menuFontShadowBlur == "0px " ) )
			var menuNormalFontShadow = "none";
		else
			var menuNormalFontShadow = menuFontShadowHoriz + menuFontShadowVert + menuFontShadowBlur + menuFontShadowColor;
		
		// Hover font shadow
		var menuHoverFontShadowHoriz = ( ( $('#wpst_menu_hover_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_hover_font_h_shadow').val()) == $('#wpst_menu_hover_font_h_shadow').val() ) ) ? $('#wpst_menu_hover_font_h_shadow').val()+"px " : "0px ";
		var menuHoverFontShadowVert = ( ( $('#wpst_menu_hover_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_hover_font_v_shadow').val()) == $('#wpst_menu_hover_font_v_shadow').val() ) ) ? $('#wpst_menu_hover_font_v_shadow').val()+"px " : "0px ";
		var menuHoverFontShadowBlur = ( ( $('#wpst_menu_hover_font_shadow_blur').val() !== "" ) && ( parseInt($('#wpst_menu_hover_font_shadow_blur').val()) == $('#wpst_menu_hover_font_shadow_blur').val() ) ) ? $('#wpst_menu_hover_font_shadow_blur').val()+"px " : "0px ";
		
		if ( $('#wpst_menu_hover_font_shadow_colour').val() !== '' )
			var menuHoverFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_hover_font_shadow_colour').val())+", "+hexToG($('#wpst_menu_hover_font_shadow_colour').val())+", "+hexToB($('#wpst_menu_hover_font_shadow_colour').val())+")" : $('#wpst_menu_hover_font_shadow_colour').val();
		else
			var menuHoverFontShadowColor = "";
		
		if ( ( menuHoverFontShadowHoriz == "0px " ) && ( menuHoverFontShadowVert == "0px " ) && ( menuHoverFontShadowBlur == "0px " ) )
			var menuHoverFontShadow = "none";
		else
			var menuHoverFontShadow = menuHoverFontShadowHoriz + menuHoverFontShadowVert + menuHoverFontShadowBlur + menuHoverFontShadowColor;
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "text-shadow", menuNormalFontShadow );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "text-shadow", menuNormalFontShadow );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li > .ab-item").css( "text-shadow", menuNormalFontShadow );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li > .ab-item").css( "text-shadow", menuNormalFontShadow );
	
		// Hover
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> .ab-item").css( "text-shadow", menuHoverFontShadow );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> .ab-item").css( "text-shadow", menuNormalFontShadow );
		} });
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "text-shadow", menuHoverFontShadow );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("span").css( "text-shadow", menuNormalFontShadow );
		} });
		
		// Blavatar
		if ( document.getElementById("wpstMenuFontShadow") )
			var style = document.getElementById("wpstMenuFontShadow");
		else {
			var style = document.createElement('style');
			style.id = 'wpstMenuFontShadow';
		}
		
		style.innerHTML = '#wpadminbar .quicklinks .ab-sub-secondary > li > a > .blavatar { text-shadow: '+menuNormalFontShadow+'; } #wpadminbar .quicklinks .ab-sub-secondary > li > a > .blavatar:before { text-shadow: '+menuNormalFontShadow+'; } ';
		
		style.innerHTML += '#wpadminbar .quicklinks .ab-sub-secondary > li.hover > a > .blavatar { text-shadow: '+menuHoverFontShadow+'; } #wpadminbar .quicklinks .ab-sub-secondary > li.hover > a > .blavatar:before { text-shadow: '+menuHoverFontShadow+'; }  #wpadminbar .quicklinks li a:hover .blavatar:before { text-shadow: '+menuHoverFontShadow+'; } ';
		
		document.head.appendChild(style);
	}
	
	function update_tb_shadow() {
		
		var shadow = ($("#wpadminbar").css("box-shadow")) ? "box-shadow" : "-webkit-box-shadow";
		
		var tbHorizontalShadow = ( ( $('#wpst_h_shadow').val() !== "" ) && ( parseInt($('#wpst_h_shadow').val()) == $('#wpst_h_shadow').val() ) ) ? $('#wpst_h_shadow').val()+"px " : "0px ";
		var tbVerticalShadow = ( ( $('#wpst_v_shadow').val() !== "" ) && ( parseInt($('#wpst_v_shadow').val()) == $('#wpst_v_shadow').val() ) ) ? $('#wpst_v_shadow').val()+"px " : "0px ";
		var tbShadowBlur = ( ( $('#wpst_shadow_blur').val() !== "" ) && ( parseInt($('#wpst_shadow_blur').val()) == $('#wpst_shadow_blur').val() ) ) ? $('#wpst_shadow_blur').val()+"px " : "0px ";
		var tbShadowSpread = ( ( $('#wpst_shadow_spread').val() !== "" ) && ( parseInt($('#wpst_shadow_spread').val()) == $('#wpst_shadow_spread').val() ) ) ? $('#wpst_shadow_spread').val()+"px " : "0px ";
		
		if ( $('#wpst_shadow_colour').val() !== '' )
			var tbShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_shadow_colour').val())+", "+hexToG($('#wpst_shadow_colour').val())+", "+hexToB($('#wpst_shadow_colour').val())+")" : $('#wpst_shadow_colour').val();
		else
			var tbShadowColor = "";
		
		if ( ( tbHorizontalShadow == "0px " ) && ( tbVerticalShadow == "0px " ) && ( tbShadowBlur == "0px " ) )
			var tbShadow = "none";
		else
			var tbShadow = tbHorizontalShadow + tbVerticalShadow + tbShadowBlur + tbShadowSpread + tbShadowColor;
		
		$("#wpadminbar").css(shadow, tbShadow);
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css(shadow, tbShadow);
	}
	
	
	// TOOLBAR
	
	// Toolbar Height
	$('#wpst_height').change(function() {
		
		// Non-responsive only
		if ( wpadminbarWidth < 783 ) return;
		
		update_tb_height();
		update_tb_background();
		update_tb_icon_margin();
	});
	
	// Toolbar background colour & gradient
	$('.wpst_background_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
		
			update_tb_background();
			needToConfirm = true;
		},
		
		clear: function() {
			
			update_tb_background();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	$('.wpst_background').change(function() {
		
		update_tb_background();
	});
	
	// Toolbar Borders
	$('.wpst_border').change(function() {
	
		update_tb_borders();
	});
	
	$('.wpst_border_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_tb_borders();
			needToConfirm = true;
		},
		clear: function() {
		
			update_tb_borders();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	$('.wpst_icon_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_tb_font_colour();
			needToConfirm = true;
		},
		clear: function() {
		
			update_tb_font_colour();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	// Toolbar Font Attributes
	$('.wpst_font').change(function() {
		
		var tbFontFamily = ( $('#wpst_font').val() !== "" ) ? $('#wpst_font').val() : wpstFontEmpty;
		var menuFontFamily = ( $('#wpst_menu_font').val() !== "" ) ? $('#wpst_menu_font').val() : tbFontFamily;
		
		$("#wpadminbar").find(".ab-item").css( "font-family", tbFontFamily );
		$("#wpadminbar").find(".ab-item .ab-label").css( "font-family", tbFontFamily );
		$("#wpadminbar").find(".ab-submenu .ab-item").css( "font-family", menuFontFamily );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "font-family", menuFontFamily );
	});
	
	$('.wpst_font_size').change(function() {
		
		update_tb_font_size();
	});
	
	$("#wpst_font_size").blur(function() {
		
		if ( $( "#wpst_hover_font_size" ).hasClass( "wpst-has-default" ) ) $("#wpst_hover_font_size").val( $(this).val() );
		$("#wpst_hover_font_size_default").val( $(this).val() );
		if ( $( "#wpst_menu_font_size" ).hasClass( "wpst-has-default" ) ) $("#wpst_menu_font_size").val( $(this).val() );
		$("#wpst_menu_font_size_default").val( $(this).val() );
		update_tb_font_size();
	});
	
	$('.wpst_icon_size').change(function() {
		
		update_tb_height();
		update_tb_font_size();
		update_tb_icon_margin();
		update_tb_icon_size();
	});
	
	$("#wpst_icon_size").blur(function() {
		
		if ( $( "#wpst_hover_icon_size" ).hasClass( "wpst-has-default" ) ) $("#wpst_hover_icon_size").val( $(this).val() );
		$("#wpst_hover_icon_size_default").val( $(this).val() );
		update_tb_icon_size();
	});
	
	$('.wpst_font_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_tb_font_colour();
			needToConfirm = true;
		},
		clear: function() {
		
			update_tb_font_colour();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	$('.wpst_font_style').change(function() {
	
		// Propagate values from Toolbar to Dropdown Menus and from Normal style to Hover
		var tbFontStyle = ( $('#wpst_font_style').val() != '' ) ? $('#wpst_font_style').val() : wpstFontNormal;
		var tbHoverFontStyle = ( $('#wpst_hover_font_style').val() != '' ) ? $('#wpst_hover_font_style').val() : tbFontStyle;
		var menuFontStyle = ( $('#wpst_menu_font_style').val() != '' ) ? $('#wpst_menu_font_style').val() : tbFontStyle;
		var menuHoverFontStyle = ( $('#wpst_menu_hover_font_style').val() != '' ) ? $('#wpst_menu_hover_font_style').val() : menuFontStyle;
		
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > a").css( "font-style", tbFontStyle );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "font-style", tbFontStyle );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "font-style", tbFontStyle );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").css( "font-style", menuFontStyle );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "font-style", menuFontStyle );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "font-style", menuFontStyle );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "font-style", tbHoverFontStyle );
			$(this).find( "> .ab-item" ).css( "font-style", tbHoverFontStyle );
			$(this).find( "> .ab-item > .ab-label" ).css( "font-style", tbHoverFontStyle );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "font-style", tbFontStyle );
			$(this).find( "> .ab-item" ).css( "font-style", tbFontStyle );
			$(this).find( "> .ab-item > .ab-label" ).css( "font-style", tbFontStyle );
		} });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> a").css( "font-style", menuHoverFontStyle );
			$(this).find("> .ab-item").css( "font-style", menuHoverFontStyle );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "font-style", menuFontStyle );
			$(this).find("> .ab-item").css( "font-style", menuFontStyle );
		} });
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "font-style", menuHoverFontStyle );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("span").css( "font-style", menuFontStyle );
		} });
	});
	
	$('.wpst_font_weight').change(function() {
		
		// Propagate values from Toolbar to Dropdown Menus and from Normal style to Hover
		var tbFontWeight = ( $('#wpst_font_weight').val() != '' ) ? $('#wpst_font_weight').val() : wpstFontNormal;
		var tbHoverFontWeight = ( $('#wpst_hover_font_weight').val() != '' ) ? $('#wpst_hover_font_weight').val() : tbFontWeight;
		var menuFontWeight = ( $('#wpst_menu_font_weight').val() != '' ) ? $('#wpst_menu_font_weight').val() : tbFontWeight;
		var menuHoverFontWeight = ( $('#wpst_menu_hover_font_weight').val() != '' ) ? $('#wpst_menu_hover_font_weight').val() : menuFontWeight;
		
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > a").css( "font-weight", tbFontWeight );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "font-weight", tbFontWeight );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "font-weight", tbFontWeight );
		$("#wpadminbar").find(".quicklinks .menupop ul li a strong").css( "font-weight", "bold" );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").css( "font-weight", menuFontWeight );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "font-weight", menuFontWeight );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "font-weight", menuFontWeight );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "font-weight", tbHoverFontWeight );
			$(this).find("> .ab-item").css( "font-weight", tbHoverFontWeight );
			$(this).find("> .ab-item > .ab-label").css( "font-weight", tbHoverFontWeight );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css("font-weight", tbFontWeight );
			$(this).find("> .ab-item").css( "font-weight", tbFontWeight );
			$(this).find("> .ab-item > .ab-label").css( "font-weight", tbFontWeight );
		} });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> a").css( "font-weight", menuHoverFontWeight );
			$(this).find("> .ab-item").css( "font-weight", menuHoverFontWeight );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "font-weight", menuFontWeight );
			$(this).find("> .ab-item").css( "font-weight", menuFontWeight );
		} });
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "font-weight", menuHoverFontWeight );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("span").css( "font-weight", menuFontWeight );
		} });
	});
	
	$('.wpst_font_line').change(function() {
		
		// Propagate values from Toolbar to Dropdown Menus and from Normal style to Hover
		var tbFontLine = ( $('#wpst_font_line').val() != '' ) ? $('#wpst_font_line').val() : wpstFontNone;
		var tbHoverFontLine = ( $('#wpst_hover_font_line').val() != '' ) ? $('#wpst_hover_font_line').val() : tbFontLine;
		var menuFontLine = ( $('#wpst_menu_font_line').val() != '' ) ? $('#wpst_menu_font_line').val() : tbFontLine;
		var menuHoverFontLine = ( $('#wpst_menu_hover_font_line').val() != '' ) ? $('#wpst_menu_hover_font_line').val() : menuFontLine;
		
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > a").css( "text-decoration", tbFontLine );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "text-decoration", tbFontLine );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "text-decoration", tbFontLine );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").css( "text-decoration", menuFontLine );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "text-decoration", menuFontLine );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "text-decoration", menuFontLine );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "text-decoration", tbHoverFontLine );
			$(this).find("> .ab-item").css( "text-decoration", tbHoverFontLine );
			$(this).find("> .ab-item > .ab-label").css( "text-decoration", tbHoverFontLine );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "text-decoration", tbFontLine );
			$(this).find("> .ab-item").css( "text-decoration", tbFontLine );
			$(this).find("> .ab-item > .ab-label").css( "text-decoration", tbFontLine );
		} });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> a").css( "text-decoration", menuHoverFontLine );
			$(this).find("> .ab-item").css( "text-decoration", menuHoverFontLine );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "text-decoration", menuFontLine );
			$(this).find("> .ab-item").css( "text-decoration", menuFontLine );
		} });
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "text-decoration", menuHoverFontLine );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("span").css( "text-decoration", menuFontLine );
		} });
	});
	
	$('.wpst_font_case').change(function() {
		
		// Toolbar normal styling
		switch($('#wpst_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var tbTextTransform = $('#wpst_font_case').val();
				var tbFontVariant = wpstFontNormal;
				break;
			case "small-caps":
				var tbTextTransform = wpstFontNone;
				var tbFontVariant = "small-caps";
				break;
			case "normal":
			default:
				var tbTextTransform = wpstFontNone;
				var tbFontVariant = wpstFontNormal;
		}
		
		// Toolbar hover styling
		switch($('#wpst_hover_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var tbHoverTextTransform = $('#wpst_hover_font_case').val();
				var tbHoverFontVariant = wpstFontNormal;
				break;
			case "small-caps":
				var tbHoverTextTransform = wpstFontNone;
				var tbHoverFontVariant = "small-caps";
				break;
			case "normal":
				var tbHoverTextTransform = wpstFontNone;
				var tbHoverFontVariant = wpstFontNormal;
				break;
			default:
				var tbHoverTextTransform = tbTextTransform;
				var tbHoverFontVariant = tbFontVariant;
		}
		
		// Dropdown menu styling
		switch($('#wpst_menu_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var menuTextTransform = $('#wpst_menu_font_case').val();
				var menuFontVariant = wpstFontNormal;
				break;
			case "small-caps":
				var menuTextTransform = wpstFontNone;
				var menuFontVariant = "small-caps";
				break;
			case "normal":
				var menuTextTransform = wpstFontNone;
				var menuFontVariant = wpstFontNormal;
				break;
			default:
				var menuTextTransform = tbTextTransform;
				var menuFontVariant = tbFontVariant;
		}
		
		// Dropdown menu hover styling
		switch($('#wpst_menu_hover_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var menuHoverTextTransform = $('#wpst_menu_hover_font_case').val();
				var menuHoverFontVariant = wpstFontNormal;
				break;
			case "small-caps":
				var menuHoverTextTransform = wpstFontNone;
				var menuHoverFontVariant = "small-caps";
				break;
			case "normal":
				var menuHoverTextTransform = wpstFontNone;
				var menuHoverFontVariant = wpstFontNormal;
				break;
			default:
				var menuHoverTextTransform = menuTextTransform;
				var menuHoverFontVariant = menuFontVariant;
		}
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > a").css( "text-transform", tbTextTransform );
		$("#wpadminbar").find(".ab-top-menu > li > a").css( "font-variant", tbFontVariant );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "text-transform", tbTextTransform );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "font-variant", tbFontVariant );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "text-transform", tbTextTransform );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "font-variant", tbFontVariant );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").css( "text-transform", menuTextTransform );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").css( "font-variant", menuFontVariant );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "text-transform", menuTextTransform );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "font-variant", menuFontVariant );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "text-transform", menuTextTransform );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "font-variant", menuFontVariant );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "text-transform", tbHoverTextTransform );
			$(this).find("> .ab-item").css( "text-transform", tbHoverTextTransform );
			$(this).find("> .ab-item > .ab-label").css( "text-transform", tbHoverTextTransform );
			$(this).find("> a").css( "font-variant", tbHoverFontVariant );
			$(this).find("> .ab-item").css( "font-variant", tbHoverFontVariant );
			$(this).find("> .ab-item > .ab-label").css( "font-variant", tbHoverFontVariant );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "text-transform", tbTextTransform );
			$(this).find("> .ab-item").css( "text-transform", tbTextTransform );
			$(this).find("> .ab-item > .ab-label").css( "text-transform", tbTextTransform );
			$(this).find("> a").css( "font-variant", tbFontVariant );
			$(this).find("> .ab-item").css( "font-variant", tbFontVariant );
			$(this).find("> .ab-item > .ab-label").css( "font-variant", tbFontVariant );
		} });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> a").css( "text-transform", menuHoverTextTransform );
			$(this).find("> .ab-item").css( "text-transform", menuHoverTextTransform );
			$(this).find("> a").css( "font-variant", menuHoverFontVariant );
			$(this).find("> .ab-item").css( "font-variant", menuHoverFontVariant );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "text-transform", menuTextTransform );
			$(this).find("> .ab-item").css( "text-transform", menuTextTransform );
			$(this).find("> a").css( "font-variant", menuFontVariant );
			$(this).find("> .ab-item").css( "font-variant", menuFontVariant );
		} });
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "text-transform", menuHoverTextTransform );
			$(this).find("span").css( "font-variant", menuHoverFontVariant );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("span").css( "text-transform", menuTextTransform );
			$(this).find("span").css( "font-variant", menuFontVariant );
		} });
	});
	
	// Toolbar Font Shadow
	$('.wpst_font_shadow').change(function() {
		
		update_tb_font_shadow();
	});
	
	$('.wpst_font_shadow_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_tb_font_shadow();
			needToConfirm = true;
		},
		clear: function() {
		
			update_tb_font_shadow()
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	
	// MENUS
	
	// Menus background colour
	$('.wpst_menu_background').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
		
			update_menu_background();
			needToConfirm = true;
		},
		
		clear: function() {
		
			update_menu_background();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	// Menus Font Colour
	$('.wpst_menu_font_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_menu_font_colour();
			needToConfirm = true;
		},
		clear: function() {
		
			update_menu_font_colour();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	// Menus Font Shadow
	$('.wpst_menu_font_shadow').change(function() {
		
		update_menu_font_shadow();
	});
	
	$('.wpst_menu_font_shadow_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_menu_font_shadow();
			needToConfirm = true;
		},
		clear: function() {
		
			update_menu_font_shadow();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
	
	// TOOLBAR AND MENUS SHARED STYLE
	
	// Toolbar Transparency
	$('#wpst_transparency').change(function() {
		
		if ( ( $('#wpst_transparency').val() == "" ) || ( parseInt($('#wpst_transparency').val()) < 0 ) || ( parseInt($('#wpst_transparency').val()) > 100 ) || ( parseInt($('#wpst_transparency').val()) != $('#wpst_transparency').val() ) ) {
			var tbNewOpacity = "1";
		} else {
			var tbNewOpacity = $('#wpst_transparency').val()/100;
		}
		
		$("#wpadminbar").css("opacity", tbNewOpacity);
		$("#wpadminbar .quicklinks").css("opacity", tbNewOpacity);
		$("#wpadminbar .ab-top-secondary").css("opacity", tbNewOpacity);
	});
	
	$('.wpst_shadow').change(function() {

		update_tb_shadow();
	});
	
	$('#wpst_shadow_colour').wpColorPicker({
		defaultColor: false,
		change: function(event, ui){
			
			update_tb_shadow();
			needToConfirm = true;
		},
		clear: function() {
		
			update_tb_shadow();
			needToConfirm = true;
		},
		hide: true,
		palettes: false
	});
	
});
