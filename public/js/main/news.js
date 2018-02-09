fn_article('notice','ytn',true);

function fn_article(containerID, buttonID, autoStart){
	var $element = $('#'+containerID).find('.notice-list');
	var $prev = $('#'+buttonID).find('.prev');
	var $next = $('#'+buttonID).find('.next');
	var $play = $('#'+containerID).find('.control > a.play');
	var $stop = $('#'+containerID).find('.control > a.stop');
	var autoPlay = autoStart;
	var auto = null;
	var speed = 3000;
	var timer = null;

	var move = $element.children().outerHeight();
	var first = false;
	var lastChild;

	lastChild = $element.children().eq(-1).clone(true);
	lastChild.prependTo($element);
	$element.children().eq(-1).remove();

	if($element.children().length==1){
		$element.css('top','0px');
	}else{
		$element.css('top','-'+move+'px');
	}

	if(autoPlay){
		timer = setInterval(moveNextSlide, speed);
		$play.addClass('on').text('▶');
		auto = true;
	}else{
		$play.hide();
		$stop.hide();
	}

	$element.find('>li').bind({
		'mouseenter': function(){
			if(auto){
				clearInterval(timer);
			}
		},
		'mouseleave': function(){
			if(auto){
				timer = setInterval(moveNextSlide, speed);
			}
		}
	});

	$play.bind({
		'click': function(e){
			if(auto) return false;

			e.preventDefault();
			timer = setInterval(moveNextSlide, speed);
			$(this).addClass('on').text('▶');
			$stop.removeClass('on').text('▣');
			auto = true;
		}
	});

	$stop.bind({
		'click': function(e){
			if(!auto) return false;

			e.preventDefault();
			clearInterval(timer);
			$(this).addClass('on').text('■');
			$play.removeClass('on').text('▷');
			auto = false;
		}
	});

	$prev.bind({
		'click': function(){
			movePrevSlide();
			return false;	
		},
		'mouseenter': function(){
			if(auto){
				clearInterval(timer);
			}
		},
		'mouseleave': function(){
			if(auto){
				timer = setInterval(moveNextSlide, speed);
			}
		}
	});

	$next.bind({
		'click': function(){
			moveNextSlide();
			return false;
		},
		'mouseenter': function(){
			if(auto){
				clearInterval(timer);
			}
		},
		'mouseleave': function(){
			if(auto){
				timer = setInterval(moveNextSlide, speed);
			}
		}
	});

	function movePrevSlide(){
		$element.each(function(idx){
			if(!first){
				$element.eq(idx).animate({'top': '0px'},'normal',function(){
					lastChild = $(this).children().eq(-1).clone(true);
					lastChild.prependTo($element.eq(idx));
					$(this).children().eq(-1).remove();
					$(this).css('top','-'+move+'px');
				});
				first = true;
				return false;
			}

			$element.eq(idx).animate({'top': '0px'},'normal',function(){
				lastChild = $(this).children().filter(':last-child').clone(true);
				lastChild.prependTo($element.eq(idx));
				$(this).children().filter(':last-child').remove();
				$(this).css('top','-'+move+'px');
			});
		});
	}

	function moveNextSlide(){
		$element.each(function(idx){

			var firstChild = $element.children().filter(':first-child').clone(true);
			firstChild.appendTo($element.eq(idx));
			$element.children().filter(':first-child').remove();
			$element.css('top','0px');

			$element.eq(idx).animate({'top':'-'+move+'px'},'normal');

		});
	}
}