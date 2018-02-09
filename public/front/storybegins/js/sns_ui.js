/******************************
SNS 공유하기
******************************/

"use strict"

$(window).one("load", function() {

	$(".snswrap").each(function() {
		
		var $ui = $(this);
		var $nav = $ui.children("nav");

		$nav.children("ul").width(function() {
						
			var tempWidth = 0;
			$(this).children("li").each(function() {

				tempWidth += $(this).width();

			});
			return tempWidth;

		});

		$ui.on("mouseenter mouseleave focusin focusout", function(_e) {
			
			TweenMax.delayedCall(0.2, close);
			switch(_e.type) {
				case "mouseenter":
				case "focusin":
					TweenMax.killDelayedCallsTo(close);
					TweenMax.to($nav, 0.5, { autoAlpha:1, width:$nav.children("ul").outerWidth(), ease:Quart.easeInOut });
				break;
			}

		});
		
		function close() {

			TweenMax.to($nav, 0.4, { autoAlpha:0, width:$nav.height(), ease:Quart.easeOut });

		}

	});

	$(".sharebox").each(function() {

		var $ui = $(this);
		var $nav = $ui.children("nav");
		var $list = $nav.children("ul");

		$ui.on("mouseenter mouseleave focusin focusout", function(_e) {
			
			TweenMax.delayedCall(0.2, close);
			switch(_e.type) {
				case "mouseenter":
				case "focusin":
					TweenMax.killDelayedCallsTo(close);
					TweenMax.to($nav, 0.3, { autoAlpha:1, ease:Quart.easeInOut });
					TweenMax.to($list, 0.5, { left:-2, ease:Quart.easeInOut, onComplete:open_complete });
				break;
			}

		});

		function open_complete() {

			$nav.css({ overflow:"visible" });

		}
		
		function close() {
			
			$nav.css({ overflow:"hidden" });
			TweenMax.to($nav, 0.4, { autoAlpha:0, ease:Quart.easeOut });
			TweenMax.to($list, 0.4, { left:-166, ease:Quart.easeOut });

		}

	});

});