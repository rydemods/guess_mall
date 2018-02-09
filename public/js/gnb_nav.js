
var GnbHandler = {};
GnbHandler.menuCode = {
	depth1Code : null,
	depth2Code : null
}
GnbHandler.option = {
	overOption : true
}
GnbHandler.start = function() {
	var settings = {
		RootSelector : '#gnb_nav',
		Depth1Selector : '>li',
		Depth1anchor : '>li>a',
		Depth2Selector : 'div',
		Depth2anchor : 'div>ul>li>a'
	};
	var depth1 = null;
	var depth2 = null;
	var current = null;
	var option = true;
	return {
		init : function(options) {
			var _this = this;
			$.extend(settings, options);

			this.hide();
			depth1 = GnbHandler.menuCode.depth1Code;
			depth2 = GnbHandler.menuCode.depth2Code;
			option = GnbHandler.option.overOption;
			if (depth1 != null) {
				this.active();
			}
			if(option===true){
				$(settings.RootSelector).find(settings.Depth1anchor)
					.mouseover(function(){
						_this.hide();
						_this.show(this);
					})
					.focus(function(){
						$(this).mouseover();
					})
					.end()
					.mouseleave(function(){
						_this.hide();
						if (depth1 != null) {
							_this.active();
						}
					});

				$(settings.RootSelector).find(settings.Depth2anchor)
					.mouseover(function(){
						$(this).parent('li').addClass('active');
					})
					.focus(function(){
						$(this).mouseover();
					})
					.focusout(function(){
						$(this).parent('li').removeClass('active');
					})
					.mouseleave(function(){
						$(this).parent('li').removeClass('active');
					});

				$('#util').find('a').last().on('focus',function(){
					_this.hide();
					_this.active();
				});
				$(settings.RootSelector).find('a').last().on('focusout',function(){
					_this.hide();
					_this.active();
				});
			}else if(option===false){

			}
		},
		hide : function() {
			$(settings.RootSelector).find(settings.Depth2Selector)
				.hide()
				.parent('li')
				.removeClass('active');
			$(settings.RootSelector).find(settings.Depth1Selector)
				.removeClass("active")
				.removeClass("current");
		},
		show : function(me) {
			var _this = $(me);
			_this.parent('li').addClass('active').find(settings.Depth2Selector).fadeIn(150);
		},
		active : function() {
			if (depth1 == null && depth2 == null) return;
			var _this = $(settings.RootSelector).find(settings.Depth1Selector).eq(depth1);
			_this.addClass('current').find(settings.Depth2Selector).show();
			_this.find(settings.Depth2Selector).find('li').eq(depth2).addClass('current');
		}
	}
}();

