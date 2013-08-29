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
	
	
	// SAFERS
	
	// Reference: http://www.4guysfromrolla.com/demos/OnBeforeUnloadDemo2.htm
	var needToConfirm = false;
	window.onbeforeunload = confirmExit;
	function confirmExit() {
		if (needToConfirm)
			return "You have attempted to leave this page.  If you have made any changes to the fields without clicking the Save button, your changes will be lost.  Are you sure you want to exit this page?";
	}
	
	
	// KEEPERS
	
	var wpstRgbColors = ( $("#wpadminbar").css("background-color").search("rgb") >= 0 );
	var wpstDefaultHeight = "28";
	var subwrapper_top = "26px";
	
	var wpstBorderLeftColor = "#555555";
	var wpstBorderLeftColorRgb = "rgb( 85, 85, 85)";
	var wpstBorderRightColor = "#333333";
	var wpstBorderRightColorRgb = "rgb( 51, 51, 51)";
	
	var wpstEmptyColor = "#464646";
	var wpstEmptyColorRgb = "rgb( 70, 70, 70)";
	var wpstBottomEmptyColor = "#373737";
	var wpstBottomEmptyColorRgb = "rgb( 55, 55, 55)";
	var wpstBottomEmptyHeight = "5px";
	
	// var wpstHoverEmptyColor = "#333333";
	// var wpstHoverEmptyColorRgb = "rgb( 51, 51, 51)";
	var wpstHoverEmptyColor = "#FFFFFF";
	var wpstHoverEmptyColorRgb = "rgb( 255, 255, 255)";

	var wpstFontEmpty = "sans-serif";
	var wpstFontSizeEmpty = "13px";
	var wpstFontNormal = "normal";
	var wpstFontNone = "none";
	var wpstFontEmptyColor = "#CCCCCC";
	var wpstFontEmptyColorRgb = "rgb( 204, 204, 204)";
	var wpstFontHoverEmptyColor = "#CCCCCC";
	var wpstFontHoverEmptyColorRgb = "rgb( 204, 204, 204)";
	
	var wpstMenuEmptyColor = "#FFFFFF";
	var wpstMenuEmptyColorRgb = "rgb( 255, 255, 255 )";
	var wpstMenuExtEmptyColor = "#EEEEEE";
	var wpstMenuExtEmptyColorRgb = "rgb( 238, 238, 238 )";
	var wpstMenuHoverEmptyColor = "#EAF2FA";
	var wpstMenuHoverEmptyColorRgb = "rgb( 234, 242, 250)";
	var wpstMenuExtHoverEmptyColor = "#DFDFDF";
	var wpstMenuExtHoverEmptyColorRgb = "rgb( 223, 223, 223)";
	var wpstMenuFontEmptyColor = "#21759B";
	var wpstMenuFontEmptyColorRgb = "rgb( 33, 117, 155)";
	
	// Determine gradient string from browser type
	var gradient = "linear-gradient(";
	if ( isChrome )  gradient = "-webkit-linear-gradient(top, ";
	if ( isSafari )  gradient = "-webkit-linear-gradient(top, ";
	if ( isFirefox ) gradient = "-moz-linear-gradient(top, ";
	if ( isIE )      gradient = "-ms-linear-gradient(top, ";
	if ( isOpera )   gradient = "-o-linear-gradient(top, ";
	
	
	// ADMIN CHECKERS
	
	// Hide all tabs by default, and show only active
	// $(".wpst-nav-div").hide();
	// $(".wpst-nav-div-active").show();
	
	// Close all Style boxes by default
	$(".wpst-style-widefat").hide();
	
	// Switch from one tab to the other
	$(".wpst-nav-tab").click(function() {
		
		// Hide all tabs
		$(".wpst-nav-tab").removeClass("nav-tab-active wpst-nav-tab-active");
		$(".wpst-nav-div").removeClass("wpst-nav-div-active");
		
		// Show clicked tab
		$("a#"+this.id).addClass("nav-tab-active wpst-nav-tab-active");
		$("div#"+this.id).addClass("wpst-nav-div-active");
		
		// Send it to $_POST, to come back to the same tab after saving
		document.getElementById("symposium_toolbar_view").value = this.id;
	});
	
	// Data was edited, user needs to confirm when leaving page
	$(".wpst-admin").change(function() {
		
		needToConfirm = true;
	});
	
	// Data was saved
	$(".wpst-save").click(function() {
		
		needToConfirm = false;
	});
	
	// In such fields, User shall input an integer
	$(".wpst-int").keyup(function() {
	
		if ( ( $(this).val() != "" ) && ( $(this).val() != "-" ) && ( parseInt($(this).val()) != $(this).val() ) ) {
			$(this).css("background-color", "#FFEBE8");
			$(this).css("border", "1px solid #CC0000");
		} else {
			$(this).css("background-color", "#FFFFFF");
			$(this).css("border", "1px solid #DFDFDF");
		}
	});
	
	// In such fields, User shall input a positive integer
	$(".wpst-positive-int").keyup(function() {
	
		if ( ( $(this).val() != "" ) && ( ( parseInt($(this).val()) < 0 ) || ( parseInt($(this).val()) != $(this).val() ) ) ) {
			$(this).css("background-color", "#FFEBE8");
			$(this).css("border", "1px solid #CC0000");
		} else {
			$(this).css("background-color", "#FFFFFF");
			$(this).css("border", "1px solid #DFDFDF");
		}
	});
	
	// In such fields, User shall input a percentage, positive integer from 0 to 100
	$(".wpst-percent").keyup(function() {
	
		if ( ( $(this).val() != "" ) && ( ( parseInt($(this).val()) < 0 ) || ( parseInt($(this).val()) > 100 ) || ( parseInt($(this).val()) != $(this).val() ) ) ) {
			$(this).css("background-color", "#FFEBE8");
			$(this).css("border", "1px solid #CC0000");
		} else {
			$(this).css("background-color", "#FFFFFF");
			$(this).css("border", "1px solid #DFDFDF");
		}
	});
	
	
	// ENABLERS
	
	function tb_background_image(main_color) {
	
		// Determine colours and lengths of the gradient
		var tb_height = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		
		if ( main_color !== "" ) {
			if ( ( $('#wpst_top_colour').val() !== "" ) && ( $('#wpst_top_gradient').val() !== "" ) ) {
				var top_color = wpstRgbColors ? "rgb("+hexToR( $('#wpst_top_colour').val() )+", "+hexToG( $('#wpst_top_colour').val() )+", "+hexToB( $('#wpst_top_colour').val() )+")" : $('#wpst_top_colour').val() ;
				var top_length = $('#wpst_top_gradient').val();
			} else {
				var top_color = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
				var top_length = "0";
			}
			if ( ( $('#wpst_bottom_colour').val() !== "" ) && ( $('#wpst_bottom_gradient').val() !== "" ) ) {
				var bottom_color = wpstRgbColors ? "rgb("+hexToR( $('#wpst_bottom_colour').val() )+", "+hexToG( $('#wpst_bottom_colour').val() )+", "+hexToB( $('#wpst_bottom_colour').val() )+")" : $('#wpst_bottom_colour').val() ;
				var bottom_length = $('#wpst_bottom_gradient').val();
			} else {
				var bottom_color = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
				var bottom_length = "0";
			}
		
		} else {
			var main_color = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
			var top_color = wpstRgbColors ? wpstEmptyColorRgb : wpstEmptyColor;
			var top_length = "0";
			var bottom_color = wpstRgbColors ? wpstBottomEmptyColorRgb : wpstBottomEmptyColor;
			var bottom_length = wpstBottomEmptyHeight;
		}
		
		// Determine the new "background-image" string from the values above
		return gradient + top_color + " 0px, " + main_color + " " + top_length + "px, " + main_color + " " + (parseInt(tb_height) - parseInt(bottom_length))  + "px, " + bottom_color + " " + tb_height + "px)";
	}
	
	function tb_hover_background_image(hover_color) {
	
		// Determine colours and lengths of the gradient
		var tb_height = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		if ( hover_color !== "" ) {
			if ( ( $('#wpst_hover_top_colour').val() !== "" ) && ( $('#wpst_hover_top_gradient').val() !== "" ) ) {
				var top_color = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_top_colour').val() )+", "+hexToG( $('#wpst_hover_top_colour').val() )+", "+hexToB( $('#wpst_hover_top_colour').val() )+")" : $('#wpst_hover_top_colour').val() ;
				var top_length = $('#wpst_hover_top_gradient').val();
			} else {
				var top_color = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
				var top_length = "0";
			}
			if ( ( $('#wpst_hover_bottom_colour').val() !== "" ) && ( $('#wpst_hover_bottom_gradient').val() !== "" ) ) {
				var bottom_color = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_bottom_colour').val() )+", "+hexToG( $('#wpst_hover_bottom_colour').val() )+", "+hexToB( $('#wpst_hover_bottom_colour').val() )+")" : $('#wpst_hover_bottom_colour').val() ;
				var bottom_length = $('#wpst_hover_bottom_gradient').val();
			} else {
				var bottom_color = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
				var bottom_length = "0";
			}
			
		} else {
			var hover_color = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
			var top_color = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
			var top_length = "0";
			var bottom_color = wpstRgbColors ? wpstHoverEmptyColorRgb : wpstHoverEmptyColor;
			var bottom_length = "0";
		}
		
		// Determine the new "background-image" string from the values above
		return gradient + top_color + " 0px, " + hover_color + " " + top_length + "px, " + hover_color + " " + (parseInt(tb_height) - parseInt(bottom_length))  + "px, " + bottom_color + " " + tb_height + "px)";
	}
	
	
	// UPDATERS
	
	function update_tb_height() {
		
		var tb_height = ( $('#wpst_height').val() != "" ) ? $('#wpst_height').val() : wpstDefaultHeight;
		var padding_top = Math.round( ( tb_height - 28 )/2 );
		if ( padding_top < 0 ) padding_top = 0;
		
		$("#wpadminbar").css( "height", tb_height + "px" );
		$("#wpadminbar").find(".quicklinks").css( "height", tb_height + "px" );
		$("#wpadminbar").find(".ab-top-secondary").css( "height", tb_height + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li").css( "height", tb_height + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "height", ( tb_height - padding_top ) + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "padding-top", padding_top  + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li:visited").css( "height", tb_height + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item:visited").css( "height", tb_height + "px" );
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css( "top", tb_height + "px" );
		$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper .ab-sub-wrapper").css( "top", subwrapper_top );
		$("#wpadminbar").find(".quicklinks > ul > li > a").css( "height", ( tb_height - padding_top ) + "px" );
		$("#wpadminbar").find(".quicklinks > ul > li > a").css( "padding-top", padding_top  + "px" );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "padding-top", padding_top );
		$("#wpbody").css( "margin-top", Math.round($("#tb_height").val() - 28) + "px" );
	}
	
	function update_tb_background() {
		
		// Background
		if ( $('#wpst_background_colour').val() !== "" )
			var main_color = wpstRgbColors ? "rgb("+hexToR($('#wpst_background_colour').val())+", "+hexToG($('#wpst_background_colour').val())+", "+hexToB($('#wpst_background_colour').val())+")" : $('#wpst_background_colour').val();
		else
			var main_color = "";
		
		if ( $('#wpst_hover_background_colour').val() !== "" )
			var hover_color = wpstRgbColors ? "rgb("+hexToR($("#wpst_hover_background_colour").val())+", "+hexToG($("#wpst_hover_background_colour").val())+", "+hexToB($("#wpst_hover_background_colour").val())+")" : $("#wpst_hover_background_colour").val();
		else
			var hover_color = "";
		
		// Gradient
		if ( gradient !== "" ) {
			var normal_image = tb_background_image(main_color);
			var hover_image = tb_hover_background_image(hover_color);
		
		} else {
			var normal_image = "";
			var hover_image = "";
		}
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").css( "background-image", normal_image );
		$("#wpadminbar .quicklinks").css( "background-image", normal_image );
		$("#wpadminbar .ab-top-secondary").css( "background-image", normal_image );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "background-image", normal_image );
		$("#wpadminbar").css( "background-color", main_color );
		$("#wpadminbar .quicklinks").css( "background-color", main_color );
		$("#wpadminbar .ab-top-secondary").css( "background-color", main_color );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "background-color", main_color );
		// $("#wpadminbar").find(".quicklinks > ul > li.hover").css( "background-image", hover_image );
		// $("#wpadminbar").find(".quicklinks > ul > li.hover").css( "background-color", hover_color );
		// #wpadminbar .quicklinks ul.ab-top-menu li.menupop		
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "background-color", hover_color ); },function(){ $(this).css( "background-color", main_color ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "background-image", hover_image ); },function(){ $(this).css( "background-image", normal_image ); });
		$("#wpadminbar.nojq").find(".quicklinks .ab-top-menu > li").focus(function(){ $(this).find(".ab-item").css( "background-color", hover_color ); });
		$("#wpadminbar.nojq").find(".quicklinks .ab-top-menu > li").focus(function(){ $(this).find(".ab-item").css( "background-image", hover_image ); });
		$("#wpadminbar").find(".menupop.focus .ab-label").css( "background-color", hover_color );
		$("#wpadminbar").find(".menupop.focus .ab-label").css( "background-image", hover_image );
		$("#wpadminbar").find(".menupop .ab-label").focus(function(){ $(this).css( "background-color", hover_color ); });
		$("#wpadminbar").find(".menupop .ab-label").focus(function(){ $(this).css( "background-image", hover_image ); });
	}
	
	function update_tb_borders() {
		
		// Width / Style / Colours
		var border_width =  ( ( parseInt($('#wpst_border_width').val()) == '' ) || ( parseInt($('#wpst_border_width').val()) < 0 ) || ( parseInt($('#wpst_border_width').val()) != $('#wpst_border_width').val() ) ) ? '1px ' : $('#wpst_border_width').val() + 'px ';
		
		var border_style = $('#wpst_border_style').val();
		
		if ( $('#wpst_border_left_colour').val() != '' )
			var border_left_colour = wpstRgbColors ? "rgb("+hexToR( $('#wpst_border_left_colour').val() )+", "+hexToG($('#wpst_border_left_colour').val())+", "+hexToB($('#wpst_border_left_colour').val())+")" : $('#wpst_border_left_colour').val();
		else 
			var border_left_colour = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
			// var border_left_colour = wpstRgbColors ? wpstBorderLeftColorRgb : wpstBorderLeftColor;
		
		if ( $('#wpst_border_right_colour').val() != '' )
			var border_right_colour = wpstRgbColors ? "rgb("+hexToR( $('#wpst_border_right_colour').val() )+", "+hexToG($('#wpst_border_right_colour').val())+", "+hexToB($('#wpst_border_right_colour').val())+")" : $('#wpst_border_right_colour').val();
		else 
			var border_right_colour = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
			// var border_right_colour = wpstRgbColors ? wpstBorderRightColorRgb : wpstBorderRightColor;
		
		// Gather all values together
		var border_left =  ( $('#wpst_border_style').val() == 'none' ) ? 'none' : border_width + border_style + ' ' + border_left_colour;
		var border_right =  ( ( $('#wpst_border_style').val() == 'none' ) || ( $('#wpst_border_right_colour').val() == '' ) ) ? 'none' : border_width + border_style + ' ' + border_right_colour;
		
		// Put it where it should go
		$("#wpadminbar").find(".quicklinks > ul > li").css("border-left", "none");
		$("#wpadminbar").find(".quicklinks > ul > li").css("border-right", "none");
		$("#wpadminbar").find(".quicklinks > ul > li > a").css("border-left", border_left);
		$("#wpadminbar").find(".quicklinks > ul > li > .ab-empty-item").css("border-left", border_left);
		$("#wpadminbar").find(".quicklinks > ul > li > a").css("border-right", border_right);
		$("#wpadminbar").find(".quicklinks > ul > li > .ab-empty-item").css("border-right", border_right);
		
		// In case only one color was selected, each end should have a nicely bordered item  :)
		if ( $('#wpst_border_right_colour').val() == '' ) {
			$("#wpadminbar").find(".quicklinks > ul > li:last-child > a").css("border-right", border_left);
			$("#wpadminbar").find(".quicklinks .ab-top-secondary > li:last-child > a").css("border-right", "none");
			$("#wpadminbar").find(".quicklinks .ab-top-secondary > li:first-child > a").css("border-right", border_left);
		}
	}
	
	function update_tb_font_colour() {
	
		if ( $('#wpst_font_colour').val() !== "" )
			var wpst_font_colour = wpstRgbColors ? "rgb("+hexToR( $('#wpst_font_colour').val() )+", "+hexToG( $('#wpst_font_colour').val() )+", "+hexToB( $('#wpst_font_colour').val() )+")" : $('#wpst_font_colour').val() ;
		else
			var wpst_font_colour = wpstRgbColors ? wpstFontEmptyColorRgb : wpstFontEmptyColor;
		
		if ( $('#wpst_hover_font_colour').val() !== "" )
			var wpst_hover_font_colour = wpstRgbColors ? "rgb("+hexToR( $('#wpst_hover_font_colour').val() )+", "+hexToG( $('#wpst_hover_font_colour').val() )+", "+hexToB( $('#wpst_hover_font_colour').val() )+")" : $('#wpst_hover_font_colour').val() ;
		else
			var wpst_hover_font_colour = wpstRgbColors ? wpstFontHoverEmptyColorRgb : wpstFontHoverEmptyColor;
		
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "color", wpst_font_colour );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "color", wpst_font_colour );
		
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "color", wpst_hover_font_colour ); $(this).find(".ab-label").css( "color", wpst_hover_font_colour ); },function(){ $(this).css( "color", wpst_font_colour ); $(this).find(".ab-label").css( "color", wpst_font_colour ); });
	}
	
	function update_tb_font_shadow() {
		
		if ( ( $('#wpst_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_font_h_shadow').val()) == $('#wpst_font_h_shadow').val() )
			&& ( $('#wpst_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_font_v_shadow').val()) == $('#wpst_font_v_shadow').val() ) ) {
			
			// Normal font shadow
			var wpst_font_shadow_blur = ( $('#wpst_font_shadow_blur').val() !== "" ) ? $('#wpst_font_shadow_blur').val()+"px " : "";
			var wpst_font_shadow_colour = wpstRgbColors ? "rgb("+hexToR($('#wpst_font_shadow_colour').val())+", "+hexToG($('#wpst_font_shadow_colour').val())+", "+hexToB($('#wpst_font_shadow_colour').val())+")" : $('#wpst_font_shadow_colour').val();
			
			// Hover font shadow
			var wpst_hover_font_shadow_blur = ( $('#wpst_hover_font_shadow_blur').val() !== "" ) ? $('#wpst_hover_font_shadow_blur').val()+"px " : "";
			var wpst_hover_font_shadow_colour = wpstRgbColors ? "rgb("+hexToR($('#wpst_hover_font_shadow_colour').val())+", "+hexToG($('#wpst_hover_font_shadow_colour').val())+", "+hexToB($('#wpst_hover_font_shadow_colour').val())+")" : $('#wpst_hover_font_shadow_colour').val();
			
			var normal_shadow = $('#wpst_font_h_shadow').val() + "px " + $('#wpst_font_v_shadow').val() + "px " + wpst_font_shadow_blur + wpst_font_shadow_colour;
			var hover_shadow = $('#wpst_hover_font_h_shadow').val() + "px " + $('#wpst_hover_font_v_shadow').val() + "px " + wpst_hover_font_shadow_blur + wpst_hover_font_shadow_colour;
		
		} else {
			var normal_shadow = "";
			var hover_shadow = "";
		}
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css("text-shadow", normal_shadow);
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css("text-shadow", normal_shadow);
	
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "text-shadow", hover_shadow ); },function(){ $(this).css( "text-shadow", normal_shadow ); });
		// $("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).find(".ab-label").css( "text-shadow", hover_shadow ); },function(){ $(this).find(".ab-label").css( "text-shadow", normal_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item .ab-label").hover(function(){ $(this).css( "text-shadow", hover_shadow ); },function(){ $(this).css( "text-shadow", normal_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").focus(function(){ $(this).css( "text-shadow", hover_shadow ); });
		// $("#wpadminbar").find(".ab-top-menu > li > .ab-item").focus(function(){ $(this).find(".ab-label").css( "text-shadow", hover_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item .ab-label").focus(function(){ $(this).css( "text-shadow", hover_shadow ); });
	}
	
	function update_menu_background() {
		
		// Background
		if ( $('#wpst_menu_background_colour').val() !== "" )
			var main_color = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_background_colour').val())+", "+hexToG($('#wpst_menu_background_colour').val())+", "+hexToB($('#wpst_menu_background_colour').val())+")" : $('#wpst_menu_background_colour').val();
		else
			var main_color = wpstRgbColors ? wpstMenuEmptyColorRgb : wpstMenuEmptyColor;
		
		if ( $('#wpst_menu_hover_background_colour').val() !== "" )
			var hover_color = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_background_colour").val())+", "+hexToG($("#wpst_menu_hover_background_colour").val())+", "+hexToB($("#wpst_menu_hover_background_colour").val())+")" : $("#wpst_menu_hover_background_colour").val();
		else
			var hover_color = wpstRgbColors ? wpstMenuHoverEmptyColorRgb : wpstMenuHoverEmptyColor;
		
		if ( $('#wpst_menu_ext_background_colour').val() !== "" )
			var main_color_ext = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_ext_background_colour').val())+", "+hexToG($('#wpst_menu_ext_background_colour').val())+", "+hexToB($('#wpst_menu_ext_background_colour').val())+")" : $('#wpst_menu_ext_background_colour').val();
		else
			var main_color_ext = wpstRgbColors ? wpstMenuExtEmptyColorRgb : wpstMenuExtEmptyColor;
		
		if ( $('#wpst_menu_hover_ext_background_colour').val() !== "" )
			var hover_color_ext = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_ext_background_colour").val())+", "+hexToG($("#wpst_menu_hover_ext_background_colour").val())+", "+hexToB($("#wpst_menu_hover_ext_background_colour").val())+")" : $("#wpst_menu_hover_ext_background_colour").val();
		else
			var hover_color_ext = wpstRgbColors ? wpstMenuExtHoverEmptyColorRgb : wpstMenuExtHoverEmptyColor;
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-sub-wrapper > ul").css( "background-color", main_color );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").css( "background-color", main_color );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary").css( "background-color", main_color_ext );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li").css( "background-color", main_color_ext );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper ul").css( "background-color", main_color_ext );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li").css( "background-color", main_color_ext );
		
		// Hover
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li").hover(function(){ $(this).css( "background-color", hover_color ); },function(){ $(this).css( "background-color", main_color ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li").hover(function(){ $(this).css( "background-color", hover_color_ext ); },function(){ $(this).css( "background-color", main_color_ext ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li").hover(function(){ $(this).css( "background-color", hover_color_ext ); },function(){ $(this).css( "background-color", main_color_ext ); });
	}
	
	function update_menu_font_colour() {
	
		if ( $('#wpst_menu_font_colour').val() !== "" )
			var main_color = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_font_colour').val())+", "+hexToG($('#wpst_menu_font_colour').val())+", "+hexToB($('#wpst_menu_font_colour').val())+")" : $('#wpst_menu_font_colour').val();
		else
			var main_color = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
			
		if ( $('#wpst_menu_hover_font_colour').val() !== "" )
			var hover_color = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_font_colour").val())+", "+hexToG($("#wpst_menu_hover_font_colour").val())+", "+hexToB($("#wpst_menu_hover_font_colour").val())+")" : $("#wpst_menu_hover_font_colour").val();
		else
			var hover_color = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
		
		if ( $('#wpst_menu_ext_font_colour').val() !== "" )
			var main_color_ext = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_ext_font_colour').val())+", "+hexToG($('#wpst_menu_ext_font_colour').val())+", "+hexToB($('#wpst_menu_ext_font_colour').val())+")" : $('#wpst_menu_ext_font_colour').val();
		else
			var main_color_ext = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
			
		if ( $('#wpst_menu_hover_ext_font_colour').val() !== "" )
			var hover_color_ext = wpstRgbColors ? "rgb("+hexToR($("#wpst_menu_hover_ext_font_colour").val())+", "+hexToG($("#wpst_menu_hover_ext_font_colour").val())+", "+hexToB($("#wpst_menu_hover_ext_font_colour").val())+")" : $("#wpst_menu_hover_ext_font_colour").val();
		else
			var hover_color_ext = wpstRgbColors ? wpstMenuFontEmptyColorRgb : wpstMenuFontEmptyColor;
		
		// Put it where it should go
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "color", main_color );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li > .ab-item").css( "color", main_color_ext );
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li > .ab-item").css( "color", main_color_ext );
		
		// Hover
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").hover(function(){ $(this).css( "color", hover_color ); },function(){ $(this).css( "color", main_color ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li > .ab-item").hover(function(){ $(this).css( "color", hover_color_ext ); },function(){ $(this).css( "color", main_color_ext ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul.ab-sub-secondary > li .ab-sub-wrapper li > .ab-item").hover(function(){ $(this).css( "color", hover_color_ext ); },function(){ $(this).css( "color", main_color_ext ); });
	}
	
	function update_menu_font_shadow() {
		
		if ( ( $('#wpst_menu_font_h_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_font_h_shadow').val()) == $('#wpst_menu_font_h_shadow').val() )
			&& ( $('#wpst_menu_font_v_shadow').val() !== "" ) && ( parseInt($('#wpst_menu_font_v_shadow').val()) == $('#wpst_menu_font_v_shadow').val() ) ) {
			
			// Normal font shadow
			var wpst_menu_font_shadow_blur = ( $('#wpst_menu_font_shadow_blur').val() !== "" ) ? $('#wpst_menu_font_shadow_blur').val()+"px " : "";
			var wpst_menu_font_shadow_colour = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_font_shadow_colour').val())+", "+hexToG($('#wpst_menu_font_shadow_colour').val())+", "+hexToB($('#wpst_menu_font_shadow_colour').val())+")" : $('#wpst_menu_font_shadow_colour').val();
			
			// Hover font shadow
			var wpst_menu_hover_font_shadow_blur = ( $('#wpst_menu_hover_font_shadow_blur').val() !== "" ) ? $('#wpst_menu_hover_font_shadow_blur').val()+"px " : "";
			var wpst_menu_hover_font_shadow_colour = wpstRgbColors ? "rgb("+hexToR($('#wpst_menu_hover_font_shadow_colour').val())+", "+hexToG($('#wpst_menu_hover_font_shadow_colour').val())+", "+hexToB($('#wpst_menu_hover_font_shadow_colour').val())+")" : $('#wpst_menu_hover_font_shadow_colour').val();
			
			var normal_shadow = $('#wpst_menu_font_h_shadow').val() + "px " + $('#wpst_menu_font_v_shadow').val() + "px " + wpst_menu_font_shadow_blur + wpst_menu_font_shadow_colour;
			var hover_shadow = $('#wpst_menu_hover_font_h_shadow').val() + "px " + $('#wpst_menu_hover_font_v_shadow').val() + "px " + wpst_menu_hover_font_shadow_blur + wpst_menu_hover_font_shadow_colour;
		
		} else {
			var normal_shadow = "";
			var hover_shadow = "";
		}
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css("text-shadow", normal_shadow);
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css("text-shadow", normal_shadow);
		$("#wpadminbar").find(".ab-top-menu > li > .ab-empty-item").css("text-shadow", normal_shadow);
		$("#wpadminbar").find(".ab-top-menu > li > .ab-empty-item > .ab-label").css("text-shadow", normal_shadow);
	
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "text-shadow", hover_shadow ); },function(){ $(this).css( "text-shadow", normal_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").hover(function(){ $(this).css( "text-shadow", hover_shadow ); },function(){ $(this).css( "text-shadow", normal_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-empty-item").hover(function(){ $(this).css( "text-shadow", hover_shadow ); },function(){ $(this).css( "text-shadow", normal_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").focus(function(){ $(this).css( "text-shadow", hover_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").focus(function(){ $(this).css( "text-shadow", hover_shadow ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-empty-item").focus(function(){ $(this).css( "text-shadow", hover_shadow ); });
	}
	
	function update_tb_shadow() {
		
		var shadow = ($("#wpadminbar").css("box-shadow")) ? "box-shadow" : "-webkit-box-shadow";
		
		var wpst_h_shadow = $('#wpst_h_shadow').val() + "px ";
		var wpst_v_shadow = $('#wpst_v_shadow').val() + "px ";
		var wpst_shadow_blur = ( $('#wpst_shadow_blur').val() !== "" ) ? $('#wpst_shadow_blur').val() + "px " : "0px ";
		var wpst_shadow_spread = ( $('#wpst_shadow_spread').val() !== "" ) ? $('#wpst_shadow_spread').val() + "px " : "0px ";
		if ( $('#wpst_shadow_colour').val() !== '' )
			var wpst_shadow_color = wpstRgbColors ? "rgb("+hexToR($('#wpst_shadow_colour').val())+", "+hexToG($('#wpst_shadow_colour').val())+", "+hexToB($('#wpst_shadow_colour').val())+")" : $('#wpst_shadow_colour').val();
		else
			var wpst_shadow_color = "";
		
		if ( ( $('#wpst_h_shadow').val() !== '' ) && ( $('#wpst_v_shadow').val() !== '' ) ) {
			$("#wpadminbar").css(shadow, wpst_h_shadow + wpst_v_shadow + wpst_shadow_blur + wpst_shadow_spread + wpst_shadow_color);
			$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css(shadow, wpst_h_shadow + wpst_v_shadow + wpst_shadow_blur + wpst_shadow_spread + wpst_shadow_color);
			$("#wpadminbar .menupop .ab-sub-wrapper").css(shadow, wpst_h_shadow + wpst_v_shadow + wpst_shadow_blur + wpst_shadow_spread + wpst_shadow_color);
		
		} else {
			$("#wpadminbar").css(shadow, "");
			$("#wpadminbar").find(".ab-top-menu > .menupop > .ab-sub-wrapper").css(shadow, "");
			$("#wpadminbar .menupop .ab-sub-wrapper").css(shadow, "");
		}
	}
	
	
	// PREVIEWERS
	// Reference: http://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
	
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
		
		var wpst_font = ( $('#wpst_font').val() !== "" ) ? $('#wpst_font').val() : wpstFontEmpty;
		var wpst_menu_font = ( $('#wpst_menu_font').val() !== "" ) ? $('#wpst_menu_font').val() : wpst_font;
		$("#wpadminbar").find("*").css( "font-family", wpst_font );
		$("#wpadminbar").find(".ab-submenu *").css( "font-family", wpst_menu_font );
	});
	
	$('.wpst_font_size').change(function() {
		
		var wpst_font_size = ( $('#wpst_font_size').val() !== "" ) ? $('#wpst_font_size').val() + "px" : wpstFontSizeEmpty;
		var wpst_menu_font_size = ( $('#wpst_menu_font_size').val() !== "" ) ? $('#wpst_menu_font_size').val() + "px" : wpst_font_size;
		$("#wpadminbar").find("*").css( "font-size", wpst_font_size );
		$("#wpadminbar").find(".ab-submenu *").css( "font-size", wpst_menu_font_size );
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
		var wpst_font_style = ( $('#wpst_font_style').val() != '' ) ? $('#wpst_font_style').val() : wpstFontNormal;
		var wpst_hover_font_style = ( $('#wpst_hover_font_style').val() != '' ) ? $('#wpst_hover_font_style').val() : wpst_font_style;
		var wpst_menu_font_style = ( $('#wpst_menu_font_style').val() != '' ) ? $('#wpst_menu_font_style').val() : wpst_font_style;
		var wpst_menu_hover_font_style = ( $('#wpst_menu_hover_font_style').val() != '' ) ? $('#wpst_menu_hover_font_style').val() : wpst_menu_font_style;
		
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "font-style", wpst_font_style );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "font-style", wpst_font_style );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "font-style", wpst_menu_font_style );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item > span").css( "font-style", wpst_menu_font_style );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "font-style", wpst_hover_font_style ); $(this).find( ".ab-label" ).css( "font-style", wpst_hover_font_style ) },function(){ $(this).css( "font-style", wpst_font_style ); $(this).find( ".ab-label" ).css( "font-style", wpst_font_style ); });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").focus(function(){ $(this).css( "font-style", wpst_menu_hover_font_style ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").hover(function(){ $(this).css( "font-style", wpst_menu_hover_font_style ); },function(){ $(this).css( "font-style", wpst_menu_font_style ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").focus(function(){ $(this).css( "font-style", wpst_menu_hover_font_style ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").hover(function(){ $(this).css( "font-style", wpst_menu_hover_font_style ); },function(){ $(this).css( "font-style", wpst_menu_font_style ); });
	});
	
	$('.wpst_font_weight').change(function() {
		
		// Propagate values from Toolbar to Dropdown Menus and from Normal style to Hover
		var wpst_font_weight = ( $('#wpst_font_weight').val() != '' ) ? $('#wpst_font_weight').val() : wpstFontNormal;
		var wpst_hover_font_weight = ( $('#wpst_hover_font_weight').val() != '' ) ? $('#wpst_hover_font_weight').val() : wpst_font_weight;
		var wpst_menu_font_weight = ( $('#wpst_menu_font_weight').val() != '' ) ? $('#wpst_menu_font_weight').val() : wpst_font_weight;
		var wpst_menu_hover_font_weight = ( $('#wpst_menu_hover_font_weight').val() != '' ) ? $('#wpst_menu_hover_font_weight').val() : wpst_menu_font_weight;
		
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "font-weight", wpst_font_weight );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "font-weight", wpst_font_weight );
		$("#wpadminbar").find(".quicklinks .menupop ul li a strong").css( "font-weight", "bold" );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "font-weight", wpst_menu_font_weight );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item > span").css( "font-weight", wpst_menu_font_weight );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "font-weight", wpst_hover_font_weight ); $(this).find(".ab-label").css( "font-weight", wpst_hover_font_weight ); },function(){ $(this).css("font-weight", wpst_font_weight ); $(this).find(".ab-label").css( "font-weight", wpst_font_weight ); });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").focus(function(){ $(this).css( "font-weight", wpst_menu_hover_font_weight ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").hover(function(){ $(this).css( "font-weight", wpst_menu_hover_font_weight ); },function(){ $(this).css( "font-weight", wpst_menu_font_weight ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").focus(function(){ $(this).css( "font-weight", wpst_menu_hover_font_weight ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").hover(function(){ $(this).css( "font-weight", wpst_menu_hover_font_weight ); },function(){ $(this).css( "font-weight", wpst_menu_font_weight ); });
	});
	
	$('.wpst_font_line').change(function() {
		
		// Propagate values from Toolbar to Dropdown Menus and from Normal style to Hover
		var wpst_font_line = ( $('#wpst_font_line').val() != '' ) ? $('#wpst_font_line').val() : wpstFontNone;
		var wpst_hover_font_line = ( $('#wpst_hover_font_line').val() != '' ) ? $('#wpst_hover_font_line').val() : wpst_font_line;
		var wpst_menu_font_line = ( $('#wpst_menu_font_line').val() != '' ) ? $('#wpst_menu_font_line').val() : wpst_font_line;
		var wpst_menu_hover_font_line = ( $('#wpst_menu_hover_font_line').val() != '' ) ? $('#wpst_menu_hover_font_line').val() : wpst_menu_font_line;
		
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "text-decoration", wpst_font_line );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "text-decoration", wpst_font_line );
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "text-decoration", wpst_menu_font_line );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item > span").css( "text-decoration", wpst_menu_font_line );
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){ $(this).css( "text-decoration", wpst_hover_font_line ); $(this).find(".ab-label").css( "text-decoration", wpst_hover_font_line ); },function(){ $(this).css( "text-decoration", wpst_font_line ); $(this).find(".ab-label").css( "text-decoration", wpst_font_line ); });
		
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").focus(function(){ $(this).css( "text-decoration", wpst_menu_hover_font_line ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").hover(function(){ $(this).css( "text-decoration", wpst_menu_hover_font_line ); },function(){ $(this).css( "text-decoration", wpst_menu_font_line ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").focus(function(){ $(this).css( "text-decoration", wpst_menu_hover_font_line ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").hover(function(){ $(this).css( "text-decoration", wpst_menu_hover_font_line ); },function(){ $(this).css( "text-decoration", wpst_menu_font_line ); });
	});
	
	$('.wpst_font_case').change(function() {
		
		// Toolbar normal styling
		switch($('#wpst_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var wpst_text_transform = $('#wpst_font_case').val();
				var wpst_font_variant = wpstFontNormal;
				break;
			case "small-caps":
				var wpst_text_transform = wpstFontNone;
				var wpst_font_variant = "small-caps";
				break;
			case "normal":
			default:
				var wpst_text_transform = wpstFontNone;
				var wpst_font_variant = wpstFontNormal;
		}
		
		// Toolbar hover styling
		switch($('#wpst_hover_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var wpst_hover_text_transform = $('#wpst_hover_font_case').val();
				var wpst_hover_font_variant = wpstFontNormal;
				break;
			case "small-caps":
				var wpst_hover_text_transform = wpstFontNone;
				var wpst_hover_font_variant = "small-caps";
				break;
			case "normal":
				var wpst_hover_text_transform = wpstFontNone;
				var wpst_hover_font_variant = wpstFontNormal;
				break;
			default:
				var wpst_hover_text_transform = wpst_text_transform;
				var wpst_hover_font_variant = wpst_font_variant;
		}
		
		// Dropdown menu styling
		switch($('#wpst_menu_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var wpst_menu_text_transform = $('#wpst_menu_font_case').val();
				var wpst_menu_font_variant = wpstFontNormal;
				break;
			case "small-caps":
				var wpst_menu_text_transform = wpstFontNone;
				var wpst_menu_font_variant = "small-caps";
				break;
			case "normal":
				var wpst_menu_text_transform = wpstFontNone;
				var wpst_menu_font_variant = wpstFontNormal;
				break;
			default:
				var wpst_menu_text_transform = wpst_text_transform;
				var wpst_menu_font_variant = wpst_font_variant;
		}
		
		// Dropdown menu hover styling
		switch($('#wpst_menu_hover_font_case').val())
		{
			case "uppercase":
			case "lowercase":
				var wpst_menu_hover_text_transform = $('#wpst_menu_hover_font_case').val();
				var wpst_menu_hover_font_variant = wpstFontNormal;
				break;
			case "small-caps":
				var wpst_menu_hover_text_transform = wpstFontNone;
				var wpst_menu_hover_font_variant = "small-caps";
				break;
			case "normal":
				var wpst_menu_hover_text_transform = wpstFontNone;
				var wpst_menu_hover_font_variant = wpstFontNormal;
				break;
			default:
				var wpst_menu_hover_text_transform = wpst_menu_text_transform;
				var wpst_menu_hover_font_variant = wpst_menu_font_variant;
		}
		
		// Put it where it should go
		// Normal
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "text-transform", wpst_text_transform );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").css( "font-variant", wpst_font_variant );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "text-transform", wpst_text_transform );
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").css( "font-variant", wpst_font_variant );
		
		// Focus
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").focus(function(){ $(this).css( "text-transform", wpst_hover_text_transform ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").focus(function(){ $(this).css( "font-variant", wpst_hover_font_variant ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").focus(function(){ $(this).css( "text-transform", wpst_hover_text_transform ); });
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item > .ab-label").focus(function(){ $(this).css( "font-variant", wpst_hover_font_variant ); });
		
		// Hover
		$("#wpadminbar").find(".ab-top-menu > li > .ab-item").hover(function(){
			$(this).css( "text-transform", wpst_hover_text_transform );
			$(this).css( "font-variant", wpst_hover_font_variant );
			// $(this).find(".ab-label").css( "text-transform", wpst_hover_text_transform );
			// $(this).find(".ab-label").css( "font-variant", wpst_hover_font_variant );
		},function(){
			$(this).css( "text-transform",wpst_text_transform );
			$(this).css("font-variant",wpst_font_variant);
			// $(this).find(".ab-label").css( "text-transform",wpst_text_transform );
			// $(this).find(".ab-label").css("font-variant",wpst_font_variant);
		});
		
		// Menu Normal
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "text-transform", wpst_menu_text_transform );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item").css( "font-variant", wpst_menu_font_variant );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item > span").css( "text-transform", wpst_menu_text_transform );
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-item > span").css( "font-variant", wpst_menu_font_variant );
		
		// Menu Hover
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").hover(function(){ $(this).css( "text-transform", wpst_menu_hover_text_transform ); },function(){ $(this).css( "text-transform", wpst_menu_text_transform ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > a").hover(function(){ $(this).css( "font-variant", wpst_menu_hover_font_variant ); },function(){ $(this).css( "font-variant", wpst_menu_font_variant ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").hover(function(){ $(this).css( "text-transform", wpst_menu_hover_text_transform ); },function(){ $(this).css( "text-transform", wpst_menu_text_transform ); });
		$("#wpadminbar").find(".ab-sub-wrapper > ul > li > .ab-empty-item").hover(function(){ $(this).css( "font-variant", wpst_menu_hover_font_variant ); },function(){ $(this).css( "font-variant", wpst_menu_font_variant ); });
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
			var new_percent = "100";
			var new_opacity = "1";
		} else {
			var new_percent = $('#wpst_transparency').val();
			var new_opacity = $('#wpst_transparency').val()/100;
		}
		
		$("#wpadminbar").css("filter", new_percent+"%");
		$("#wpadminbar .quicklinks").css("filter", new_percent+"%");
		$("#wpadminbar .ab-top-secondary").css("filter", new_percent+"%");
		
		$("#wpadminbar").css("opacity", new_opacity);
		$("#wpadminbar .quicklinks").css("opacity", new_opacity);
		$("#wpadminbar .ab-top-secondary").css("opacity", new_opacity);
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