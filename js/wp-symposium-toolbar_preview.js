/*  Copyright 2013  Guillaume Assire aka AlphaGolf (alphagolf@rocketmail.com)
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

// References:
// http://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
// http://melchoyce.github.io/dashicons/
// http://hofmannsven.com/2013/laboratory/wordpress-admin-ui/
	
jQuery(document).ready(function($){

	// HELPERS
	
	// Reference: http://www.javascripter.net/faq/hextorgb.htm
	// R = hexToR("#FFFFFF");
	// G = hexToG("#FFFFFF");
	// B = hexToB("#FFFFFF");
	function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
	function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
	function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
	function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}
	
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
	
	var wpstRgbColors = ( $("#wpadminbar").css("background-color").search("rgb") >= 0 );
	
	// Toolbar default values for WordPress up to 3.7.x
	if ( wpVersion < 380 ) {
		var wpstDefaultHeight = "28";
		var wpstResponsiveHeight = "28"; // Unused
		var wpstFontEmpty = "sans-serif";
		var wpstEmptyColor = "#464646";
		var wpstEmptyColorRgb = "rgb( 70, 70, 70)";
		var wpstBottomEmptyColor = "#373737";
		var wpstBottomEmptyColorRgb = "rgb( 55, 55, 55)";
		var wpstBottomEmptyHeight = "5px";
		var wpstHoverEmptyColor = "#FFFFFF";
		var wpstHoverEmptyColorRgb = "rgb( 255, 255, 255 )";
		var wpstFontEmptyColor = "#CCCCCC";
		var wpstFontEmptyColorRgb = "rgb( 204, 204, 204 )";
		var wpstFontHoverEmptyColor = "#CCCCCC";
		var wpstFontHoverEmptyColorRgb = "rgb( 204, 204, 204 )";
		
		var wpstMenuEmptyColor = "#FFFFFF";
		var wpstMenuEmptyColorRgb = "rgb( 255, 255, 255 )";
		var wpstMenuHoverEmptyColor = "#EAF2FA";
		var wpstMenuHoverEmptyColorRgb = "rgb( 234, 242, 250 )";
		var wpstMenuExtEmptyColor = "#EEEEEE";
		var wpstMenuExtEmptyColorRgb = "rgb( 238, 238, 238 )";
		var wpstMenuExtHoverEmptyColor = "#DFDFDF";
		var wpstMenuExtHoverEmptyColorRgb = "rgb( 223, 223, 223 )";
		var wpstMenuFontEmptyColor = "#21759B";
		var wpstMenuFontEmptyColorRgb = "rgb( 33, 117, 155 )";
		
	// Toolbar default values for WordPress 3.8+
	} else {
		var wpstDefaultHeight = "32";
		var wpstResponsiveHeight = "46";
		var wpstFontEmpty = '"Open Sans",sans-serif';
		var wpstEmptyColor = "#222";
		var wpstEmptyColorRgb = "rgb( 34, 34, 34)";
		var wpstBottomEmptyColor = "#222";
		var wpstBottomEmptyColorRgb = "rgb( 34, 34, 34)";
		var wpstBottomEmptyHeight = "0px";
		var wpstHoverEmptyColor = "#333";
		var wpstHoverEmptyColorRgb = "rgb( 51, 51, 51 )";
		var wpstFontEmptyColor = "#eee";
		var wpstFontEmptyColorRgb = "rgb( 238, 238, 238 )";
		var wpstIconEmptyColor = "#999";
		var wpstIconEmptyColorRgb = "rgb( 153, 153, 153 )";
		var wpstFontHoverEmptyColor = "#2ea2cc";
		var wpstFontHoverEmptyColorRgb = "rgb( 46, 162, 204 )";
		
		var wpstMenuEmptyColor = "#333";
		var wpstMenuEmptyColorRgb = "rgb( 51, 51, 51 )";
		var wpstMenuHoverEmptyColor = "#333";
		var wpstMenuHoverEmptyColorRgb = "rgb( 51, 51, 51 )";
		var wpstMenuExtEmptyColor = "#4b4b4b";
		var wpstMenuExtEmptyColorRgb = "rgb( 75, 75, 75 )";
		var wpstMenuExtHoverEmptyColor = "#4b4b4b";
		var wpstMenuExtHoverEmptyColorRgb = "rgb( 75, 75, 75 )";
		var wpstMenuFontEmptyColor = "#eee";
		var wpstMenuFontEmptyColorRgb = "rgb( 238, 238, 238 )";
	}
	
	var wpstFontSizeEmpty = "13px";
	var wpstIconSizeEmpty = "20px";
	var wpstFontSizeSmallEmpty = "11px";
	var wpstFontNormal = "normal";
	var wpstFontNone = "none";
	
	// Determine gradient string from browser type
	var gradient = "linear-gradient(";
	if ( isChrome )  gradient = "-webkit-linear-gradient(top, ";
	if ( isSafari )  gradient = "-webkit-linear-gradient(top, ";
	if ( isFirefox ) gradient = "-moz-linear-gradient(top, ";
	if ( isIE )      gradient = "-ms-linear-gradient(top, ";
	if ( isOpera )   gradient = "-o-linear-gradient(top, ";
	
	// Determine window width
	document.body.style.overflow = "hidden";
	var wpadminbarWidth = $( "#wpadminbar" ).width();
	document.body.style.overflow = "";
	
	
	// Load Default CSS file while loading this page...
	update_tb_background();
	update_tb_borders();
	update_tb_font_colour();
	update_tb_font_shadow();
	update_menu_background();
	update_menu_font_colour();
	update_menu_font_shadow();
	
	
	// ENABLERS
	
	function tb_background_image(tbMainColor) {
	
		// Determine Toolbar height
		if ( ( wpadminbarWidth < 783 ) && ( wpVersion >= 380 ) )
			var tbHeight = wpstResponsiveHeight;
		else
			var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		
		// Determine colours and lengths of the gradient
		if ( tbMainColor !== "" ) {
			if ( ( $('#wpst_top_colour').val() !== "" ) && ( $('#wpst_top_gradient').val() !== "" ) ) {
				var tbTopColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_top_colour').val() )+", "+hexToG( $('#wpst_top_colour').val() )+", "+hexToB( $('#wpst_top_colour').val() )+")" : $('#wpst_top_colour').val() ;
				var tbTopLength = $('#wpst_top_gradient').val();
			} else {
				var tbTopColor = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
				var tbTopLength = "0";
			}
			if ( ( $('#wpst_bottom_colour').val() !== "" ) && ( $('#wpst_bottom_gradient').val() !== "" ) ) {
				var tbBottomColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_bottom_colour').val() )+", "+hexToG( $('#wpst_bottom_colour').val() )+", "+hexToB( $('#wpst_bottom_colour').val() )+")" : $('#wpst_bottom_colour').val() ;
				var tbBottomLength = $('#wpst_bottom_gradient').val();
			} else {
				var tbBottomColor = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
				var tbBottomLength = "0";
			}
		
		} else {
			var tbMainColor = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
			var tbTopColor = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
			var tbTopLength = "0";
			var tbBottomColor = wpstRgbColors ? wpstBottomEmptyColorRgb : wpstBottomEmptyColor;
			var tbBottomLength = wpstBottomEmptyHeight;
		}
		
		// Determine the new "background-image" string from the values above
		return gradient + tbTopColor + " 0px, " + tbMainColor + " " + tbTopLength + "px, " + tbMainColor + " " + (parseInt(tbHeight) - parseInt(tbBottomLength))  + "px, " + tbBottomColor + " " + tbHeight + "px)";
	}
	
	function tb_hover_background_image(tbHoverColor) {
	
		// Determine Toolbar height
		if ( ( wpadminbarWidth < 783 ) && ( wpVersion >= 380 ) )
			var tbHeight = wpstResponsiveHeight;
		else
			var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		
		// Determine colours and lengths of the gradient
		if ( tbHoverColor !== "" ) {
			if ( ( $('#wpst_hover_top_colour').val() !== "" ) && ( $('#wpst_hover_top_gradient').val() !== "" ) ) {
				var tbTopColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_top_colour').val() )+", "+hexToG( $('#wpst_hover_top_colour').val() )+", "+hexToB( $('#wpst_hover_top_colour').val() )+")" : $('#wpst_hover_top_colour').val() ;
				var tbTopLength = $('#wpst_hover_top_gradient').val();
			} else {
				var tbTopColor = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
				var tbTopLength = "0";
			}
			if ( ( $('#wpst_hover_bottom_colour').val() !== "" ) && ( $('#wpst_hover_bottom_gradient').val() !== "" ) ) {
				var tbBottomColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_bottom_colour').val() )+", "+hexToG( $('#wpst_hover_bottom_colour').val() )+", "+hexToB( $('#wpst_hover_bottom_colour').val() )+")" : $('#wpst_hover_bottom_colour').val() ;
				var tbBottomLength = $('#wpst_hover_bottom_gradient').val();
			} else {
				var tbBottomColor = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
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
		
		if ( ( wpadminbarWidth < 783 ) && ( wpVersion >= 380 ) ) {
			var tbHeight = wpstResponsiveHeight;
			var tbPaddingTop = 0;
			var tbMarginTop = tbHeight - wpstDefaultHeight;
			var wpstSubwrapperTop = wpstDefaultHeight - 4; // TODO check this
		} else {
			var tbHeight = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
			var tbPaddingTop = Math.round( ( tbHeight - wpstDefaultHeight )/2 );
			if ( tbPaddingTop < 0 ) tbPaddingTop = 0;
			var tbMarginTop = tbHeight - wpstDefaultHeight;
			// if ( tbMarginTop < 0 ) tbMarginTop = 0;
			var wpstSubwrapperTop = wpstDefaultHeight - 2;
		}
		
		$("#wpadminbar").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-secondary").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "height", ( tbHeight - tbPaddingTop ) + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "padding-top", tbPaddingTop  + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li:visited").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item:visited").css( "height", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css( "top", tbHeight + "px" );
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper .ab-sub-wrapper").css( "top", wpstSubwrapperTop + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li > a").css( "height", ( tbHeight - tbPaddingTop ) + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li > a").css( "padding-top", tbPaddingTop  + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "padding-top", tbPaddingTop  + "px" );
		$('body').css( "margin-top", tbMarginTop + "px" );
		$("#wpbody").css( "margin-top", tbMarginTop + "px" );
		
		// JetPack Notes
		var notes = document.getElementById("wp-admin-bar-notes");
		if ( notes ) {
			var divs = document.getElementById("wp-admin-bar-notes").getElementsByTagName("div");
			for (var i in divs) { if ( typeof divs[i].style !== "undefined" ) divs[i].style.cssText = divs[i].style.cssText + "padding-top: " + tbPaddingTop  + "px !important;"; }
		}
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
		if ( wpVersion < 380 ) $("#wpadminbar .ab-top-secondary").css( "background-image", tbNormalImage );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "background-image", tbNormalImage );
		$("#wpadminbar").css( "background-color", tbMainColor );
		$("#wpadminbar .quicklinks").css( "background-color", tbMainColor );
		if ( wpVersion < 380 ) $("#wpadminbar .ab-top-secondary").css( "background-color", tbMainColor );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "background-color", tbMainColor );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "background-color", tbHoverColor );
			$(this).find("> a").css( "background-image", tbHoverImage );
			$(this).find("> .ab-item").css( "background-color", tbHoverColor );
			$(this).find("> .ab-item").css( "background-image", tbHoverImage );
			$(this).find("> .ab-item > .ab-label").css( "background", "transparent" );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "background-color", tbMainColor );
			$(this).find("> a").css( "background-image", tbNormalImage );
			$(this).find("> .ab-item").css( "background-color", tbMainColor );
			$(this).find("> .ab-item").css( "background-image", tbNormalImage );
		} });
	}
	
	function update_tb_borders() {
		
		if ( $('#wpst_border_style').val() != '' ) {
			
			// Width / Style / Colours
			var tbBorderWidth =  ( ( parseInt($('#wpst_border_width').val()) == '' ) || ( parseInt($('#wpst_border_width').val()) < 0 ) || ( parseInt($('#wpst_border_width').val()) != $('#wpst_border_width').val() ) ) ? '1px ' : $('#wpst_border_width').val() + 'px ';
			
			var tbBorderStyle = $('#wpst_border_style').val();
			
			if ( $('#wpst_border_left_colour').val() != '' )
				var tbBorderLeftColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_border_left_colour').val() )+", "+hexToG($('#wpst_border_left_colour').val())+", "+hexToB($('#wpst_border_left_colour').val())+")" : $('#wpst_border_left_colour').val();
			else
				var tbBorderLeftColor = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
				// var tbBorderLeftColor = wpstRgbColors ? wpstBorderLeftColorRgb : wpstBorderLeftColor;
			
			if ( $('#wpst_border_right_colour').val() != '' )
				var tbBorderRightColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_border_right_colour').val() )+", "+hexToG($('#wpst_border_right_colour').val())+", "+hexToB($('#wpst_border_right_colour').val())+")" : $('#wpst_border_right_colour').val();
			else
				var tbBorderRightColor = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
				// var tbBorderRightColor = wpstRgbColors ? wpstBorderRightColorRgb : wpstBorderRightColor;
			
			// Gather all values together
			if ( $('#wpst_border_right_colour').val() != '' ) {
				var tbBorderLeft = ( $('#wpst_border_style').val() == 'none' ) ? 'none' : tbBorderWidth + tbBorderStyle + ' ' + tbBorderLeftColor;
				var tbBorderRight = ( $('#wpst_border_style').val() == 'none' ) ? 'none' : tbBorderWidth + tbBorderStyle + ' ' + tbBorderRightColor;
				var divider = "none";
			} else {
				var tbBorderLeft = "none";
				var tbBorderRight = "none";
				var divider = ( $('#wpst_border_style').val() == 'none' ) ? 'none' : tbBorderWidth + tbBorderStyle + ' ' + tbBorderLeftColor;
			}
		
		} else {
			if ( wpVersion < 380 ) {
				var tbBorderLeft = "";
				var tbBorderRight = "";
				var divider = "";
			
			} else {
				var tbBorderLeft = "none";
				var tbBorderRight = "none";
				var divider = "none";
			}
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
	
	function update_tb_font_colour() {
	
		if ( $('#wpst_font_colour').val() !== "" ) {
			var tbFontColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_font_colour').val() )+", "+hexToG( $('#wpst_font_colour').val() )+", "+hexToB( $('#wpst_font_colour').val() )+")" : $('#wpst_font_colour').val() ;
			var tbIconColor = tbFontColor;
		} else {
			var tbFontColor = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
			var tbIconColor = wpstRgbColors ? wpstIconEmptyColorRgb : wpstIconEmptyColor;
		}
		if ( $('#wpst_hover_font_colour').val() !== "" )
			var tbHoverFontColor = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_font_colour').val() )+", "+hexToG( $('#wpst_hover_font_colour').val() )+", "+hexToB( $('#wpst_hover_font_colour').val() )+")" : $('#wpst_hover_font_colour').val() ;
		else
			var tbHoverFontColor = wpstRgbColors ? wpstFontHoverEmptyColorRgb : wpstFontHoverEmptyColor;
		
		if ( wpVersion < 380 ) {
			$("#wpadminbar").find(".ab-top-menu > li > a").css( "color", tbFontColor );
			$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "color", tbFontColor );
			$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "color", tbFontColor );
			
			$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
				$(this).find("> a").css( "color", tbHoverFontColor );
				$(this).find("> .ab-item").css( "color", tbHoverFontColor );
				$(this).find("> .ab-item .ab-label").css( "color", tbHoverFontColor );
			},function(){
				$(this).find("> a").css( "color", tbFontColor );
				$(this).find("> .ab-item").css( "color", tbFontColor );
				$(this).find("> .ab-item .ab-label").css( "color", tbFontColor );
			});
			
		} else {
			if ( document.getElementById("wpstFontColour") )
				var style = document.getElementById("wpstFontColour");
			else {
				var style = document.createElement('style');
				style.id = 'wpstFontColour';
			}
			style.innerHTML = '#wpadminbar .ab-item, #wpadminbar .ab-label, #wpadminbar li > .ab-item, #wpadminbar li > .ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default span.ab-label, #wpadminbar > #wp-toolbar > #wp-admin-bar-top-secondary span.ab-label{ color: '+tbFontColor+' } ';
			style.innerHTML += '#wpadminbar .ab-item span:before, #wpadminbar .ab-top-menu > li.menupop > .ab-item:before, #wpadminbar li #adminbarsearch:before { color: '+tbIconColor+' } ';
			style.innerHTML += '#wpadminbar .ab-top-menu>li>.ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus, #wpadminbar .ab-top-menu>li:hover>.ab-item, #wpadminbar .ab-top-menu>li.hover>.ab-item { color: '+tbHoverFontColor+' } ';
			
			document.head.appendChild(style);
		}
	}
	
	function update_tb_font_shadow() {
		
		// Normal font shadow
		if ( ( $('#wpst_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_font_h_shadow').val()) == $('#wpst_font_h_shadow').val() )
			&& ( $('#wpst_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_font_v_shadow').val()) == $('#wpst_font_v_shadow').val() ) ) {
			
			var tbFontShadowBlur = ( $('#wpst_font_shadow_blur').val() !== "" ) ? $('#wpst_font_shadow_blur').val()+"px " : "";
			var tbFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_font_shadow_colour').val())+", "+hexToG($('#wpst_font_shadow_colour').val())+", "+hexToB($('#wpst_font_shadow_colour').val())+")" : $('#wpst_font_shadow_colour').val();
			var tbNormalFontShadow = $('#wpst_font_h_shadow').val() + "px " + $('#wpst_font_v_shadow').val() + "px " + tbFontShadowBlur + tbFontShadowColor;
		
		} else
			var tbNormalFontShadow = "none";
		
		// Hover font shadow
		if ( ( $('#wpst_hover_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_hover_font_h_shadow').val()) == $('#wpst_hover_font_h_shadow').val() )
			&& ( $('#wpst_hover_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_hover_font_v_shadow').val()) == $('#wpst_hover_font_v_shadow').val() ) ) {
			
			var tbHoverFontShadowBlur = ( $('#wpst_hover_font_shadow_blur').val() !== "" ) ? $('#wpst_hover_font_shadow_blur').val()+"px " : "";
			var tbHoverFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_hover_font_shadow_colour').val())+", "+hexToG($('#wpst_hover_font_shadow_colour').val())+", "+hexToB($('#wpst_hover_font_shadow_colour').val())+")" : $('#wpst_hover_font_shadow_colour').val();
			var tbHoverFontShadow = $('#wpst_hover_font_h_shadow').val() + "px " + $('#wpst_hover_font_v_shadow').val() + "px " + tbHoverFontShadowBlur + tbHoverFontShadowColor;
		
		} else
			var tbHoverFontShadow = "none";
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > a").css("text-shadow", tbNormalFontShadow);
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css("text-shadow", tbNormalFontShadow);
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css("text-shadow", tbNormalFontShadow);
	
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li").hover(function(){
			$(this).find("> a").css( "text-shadow", tbHoverFontShadow );
			$(this).find("> .ab-item").css( "text-shadow", tbHoverFontShadow );
			$(this).find("> .ab-item > .ab-label").css( "text-shadow", tbHoverFontShadow );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> a").css( "text-shadow", tbNormalFontShadow );
			$(this).find("> .ab-item").css( "text-shadow", tbNormalFontShadow );
			$(this).find("> .ab-item > .ab-label").css( "text-shadow", tbNormalFontShadow );
		} });
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
			var menuHoverColor = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
		
		if ( $('#wpst_menu_ext_font_colour').val() !== "" )
			var menuMainColorExt = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_ext_font_colour').val())+", "+hexToG($('#wpst_menu_ext_font_colour').val())+", "+hexToB($('#wpst_menu_ext_font_colour').val())+")" : $('#wpst_menu_ext_font_colour').val();
		else
			var menuMainColorExt = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
			
		if ( $('#wpst_menu_hover_ext_font_colour').val() !== "" )
			var menuHoverColorExt = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_ext_font_colour").val())+", "+hexToG($("#wpst_menu_hover_ext_font_colour").val())+", "+hexToB($("#wpst_menu_hover_ext_font_colour").val())+")" : $("#wpst_menu_hover_ext_font_colour").val();
		else
			var menuHoverColorExt = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
		
		// Put it where it should go
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "color", menuMainColor );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item span").css( "color", menuMainColor );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li > .ab-item").css( "color", menuMainColorExt );
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li > .ab-item").css( "color", menuMainColorExt );
		
		// Hover / Focus
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){
			$(this).find("> .ab-item").css( "color", menuHoverColor );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> .ab-item").css( "color", menuMainColor );
		} });
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item").hover(function(){
			$(this).find("span").css( "color", menuHoverColor );
		},function(){
			$(this).find("span").css( "color", menuMainColor );
		});
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li").hover(function(){
			$(this).find("> .ab-item").css( "color", menuHoverColorExt );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> .ab-item").css( "color", menuMainColorExt );
		} });
		$("#wpadminbar").find(".menupop > .ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li").hover(function(){
			$(this).find("> .ab-item").css( "color", menuHoverColorExt );
		},function(){ if ( !$(this).is("focus") ) {
			$(this).find("> .ab-item").css( "color", menuMainColorExt );
		} });
	}
	
	function update_menu_font_shadow() {
		
		// Normal font shadow
		if ( ( $('#wpst_menu_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_font_h_shadow').val()) == $('#wpst_menu_font_h_shadow').val() )
			&& ( $('#wpst_menu_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_font_v_shadow').val()) == $('#wpst_menu_font_v_shadow').val() ) ) {
			
			var menuFontShadowBlur = ( $('#wpst_menu_font_shadow_blur').val() !== "" ) ? $('#wpst_menu_font_shadow_blur').val()+"px " : "";
			var menuFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_font_shadow_colour').val())+", "+hexToG($('#wpst_menu_font_shadow_colour').val())+", "+hexToB($('#wpst_menu_font_shadow_colour').val())+")" : $('#wpst_menu_font_shadow_colour').val();
			var menuNormalFontShadow = $('#wpst_menu_font_h_shadow').val() + "px " + $('#wpst_menu_font_v_shadow').val() + "px " + menuFontShadowBlur + menuFontShadowColor;
		
		} else
			var menuNormalFontShadow = "none"; // TODO need to force back to the default shadow
		
		// Hover font shadow
		if ( ( $('#wpst_menu_hover_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_hover_font_h_shadow').val()) == $('#wpst_menu_hover_font_h_shadow').val() )
			&& ( $('#wpst_menu_hover_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_hover_font_v_shadow').val()) == $('#wpst_menu_hover_font_v_shadow').val() ) ) {
			
			var menuHoverFontShadowBlur = ( $('#wpst_menu_hover_font_shadow_blur').val() !== "" ) ? $('#wpst_menu_hover_font_shadow_blur').val()+"px " : "";
			var menuHoverFontShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_hover_font_shadow_colour').val())+", "+hexToG($('#wpst_menu_hover_font_shadow_colour').val())+", "+hexToB($('#wpst_menu_hover_font_shadow_colour').val())+")" : $('#wpst_menu_hover_font_shadow_colour').val();
			var menuHoverFontShadow = $('#wpst_menu_hover_font_h_shadow').val() + "px " + $('#wpst_menu_hover_font_v_shadow').val() + "px " + menuHoverFontShadowBlur + menuHoverFontShadowColor;
		
		} else
			var menuHoverFontShadow = "none"; // TODO need to force back to the default shadow
		
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
	}
	
	function update_tb_shadow() {
		
		var shadow = ($("#wpadminbar").css("box-shadow")) ? "box-shadow" : "-webkit-box-shadow";
		
		var tbHorizontalShadow = $('#wpst_h_shadow').val() + "px ";
		var tbVerticalShadow = $('#wpst_v_shadow').val() + "px ";
		var tbShadowBlur = ( $('#wpst_shadow_blur').val() !== "" ) ? $('#wpst_shadow_blur').val() + "px " : "0px ";
		var tbShadowSpread = ( $('#wpst_shadow_spread').val() !== "" ) ? $('#wpst_shadow_spread').val() + "px " : "0px ";
		if ( $('#wpst_shadow_colour').val() !== '' )
			var tbShadowColor = wpstRgbColors ? "rgb("+hexToR($('#wpst_shadow_colour').val())+", "+hexToG($('#wpst_shadow_colour').val())+", "+hexToB($('#wpst_shadow_colour').val())+")" : $('#wpst_shadow_colour').val();
		else
			var tbShadowColor = "";
		
		if ( ( $('#wpst_h_shadow').val() !== '' ) && ( $('#wpst_v_shadow').val() !== '' ) ) {
			$("#wpadminbar").css(shadow, tbHorizontalShadow + tbVerticalShadow + tbShadowBlur + tbShadowSpread + tbShadowColor);
			$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css(shadow, tbHorizontalShadow + tbVerticalShadow + tbShadowBlur + tbShadowSpread + tbShadowColor);
			$("#wpadminbar .menupop .ab-sub-wrapper").css(shadow, tbHorizontalShadow + tbVerticalShadow + tbShadowBlur + tbShadowSpread + tbShadowColor);
		
		} else {
			$("#wpadminbar").css(shadow, "");
			$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css(shadow, "");
			$("#wpadminbar .menupop .ab-sub-wrapper").css(shadow, "");
		}
	}
	
	
	// TOOLBAR
	
	// Toolbar Height
	$('#wpst_height').change(function() {
		update_tb_height();
		update_tb_background();
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
	
	// Toolbar Font Attributes
	$('.wpst_font').change(function() {
		
		var tbFontFamily = ( $('#wpst_font').val() !== "" ) ? $('#wpst_font').val() : wpstFontEmpty;
		var menuFontFamily = ( $('#wpst_menu_font').val() !== "" ) ? $('#wpst_menu_font').val() : tbFontFamily;
		
		$("#wpadminbar").find(".ab-item").css( "font-family", tbFontFamily );
		$("#wpadminbar").find(".ab-submenu .ab-item").css( "font-family", menuFontFamily );
	});
	
	$('.wpst_font_size').change(function() {
		
		var tbFontSize = ( $('#wpst_font_size').val() !== "" ) ? $('#wpst_font_size').val() + "px" : wpstFontSizeEmpty;
		var tbIconSize = ( $('#wpst_font_size').val() !== "" ) ? ( parseInt( $('#wpst_font_size').val() ) + 7 ) + "px" : wpstIconSizeEmpty;
		var tbFontSizeSmall = ( $('#wpst_font_size').val() !== "" ) ? ( parseInt( $('#wpst_font_size').val() ) - 2 ) + "px" : wpstFontSizeSmallEmpty;
		var menuFontSize = ( $('#wpst_menu_font_size').val() !== "" ) ? $('#wpst_menu_font_size').val() + "px" : tbFontSize;
		var menuFontSizeSmall = ( $('#wpst_menu_font_size').val() !== "" ) ? ( parseInt( $('#wpst_menu_font_size').val() ) - 2 ) + "px" : tbFontSizeSmall;
		
		$("#wpadminbar").find("*").css( "font-size", tbFontSize );
		$("#wpadminbar").find(".ab-submenu *").css( "font-size", menuFontSize );
		$("#wpadminbar").find("#wp-admin-bar-user-info .ab-item .username").css( "font-size", menuFontSizeSmall );
		
		$("#wpadminbar").find("wp-toolbar > #wp-admin-bar-root-default .ab-icon").css( "font-size", tbIconSize );
		$("#wpadminbar").find(".ab-icon").css( "font-size", tbIconSize );
		$("#wpadminbar").find(".ab-item:before").css( "font-size", tbIconSize );
		
		if ( wpVersion >= 380 ) {
			if ( document.getElementById("wpstFontSize") )
				var style = document.getElementById("wpstFontSize");
			else {
				var style = document.createElement('style');
				style.id = 'wpstFontSize';
			}
			style.innerHTML = '#wpadminbar .ab-item span:before, #wpadminbar .ab-top-menu > li.menupop > .ab-item:before, #wpadminbar li #adminbarsearch:before { font-size: '+tbIconSize+'; ';
			document.head.appendChild(style);
		}
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
