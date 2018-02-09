$(document).ready(function(){
	// 공통 탭
	$("[data-ui=TabMenu]").each(function() {
		var $ui = $(this);
		var $menu = $ui.find("[data-content=menu]");
		var $content = $ui.find("[data-content=content]");
		$menu.on("click", function(_e) {
			//_e.preventDefault();
			var index = $menu.index(this);
			$menu.removeClass("active").removeAttr("title").eq(index).addClass("active").attr("title", "선택됨");
			$content.removeClass("active").eq(index).addClass("active");
		});
	});
});
	
