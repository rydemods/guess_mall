<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/premiumbrand.class.php");
$pb = new PREMIUMBRAND;
$pb->cube_list();
$pb->section_list('web');
$pb->section_slide_list('web');
$cube_list = $pb->cube_list;
$section_list = $pb->section_list;
$slide_list = $pb->section_slide_list;
$imagepath_b = $Dir.DataDir."shopimages/premiumbrand/";
$imagepath = $Dir.DataDir."shopimages/mainbanner/";
$pb->pb_info();
$pb_info = $pb->pb_info;
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script type="text/javascript">
	$(document).ready(function() {
		/*
			상단 큐브
			클릭 이벤트를 추가했습니다.
			모션이 좀 더 부드럽게 움직이도록 수정했습니다.
		*/
		$(".cube").find(".move_section").on("click", function(_e) {
			var section = $(this).data('link');
			section_scroll(section);
			/*
			var href = $(this).attr("href");
			var temp = $section.index($section.filter(href));
			if (temp >= 0) {
				_e.preventDefault();
				section_scroll(temp);
			}
			*/
		}).end()
		.find(".btn_over").each(function() {

			var $img = $(this).find("img");
			var $over = $img.filter(".over");

			TweenMax.set($over, { scaleX:0 });
			$(this).on("mouseenter mouseleave", function(_e) {

				switch(_e.type) {
					case "mouseenter":
						TweenMax.to($img, 0.2, { scaleX:0, ease:Sine.easeIn, overwrite:1, onComplete:function() {
							$over.css({ zIndex:1 });
						} });
						TweenMax.to($over, 0.2, { scaleX:1, delay:0.2, ease:Sine.easeOut, overwrite:1 });
					break;
					case "mouseleave":
						TweenMax.to($img, 0.2, { scaleX:1, delay:0.2, ease:Sine.easeOut, overwrite:1 });
						TweenMax.to($over, 0.2, { scaleX:0, ease:Sine.easeIn, overwrite:1, onComplete:function() {
							$over.css({ zIndex:-1 });
						} });
					break;
				}

			});

		});

		/* 하단 슬라이드 */
		var slideList = $(".slide-list").bxSlider();

		/* 섹션 스크롤 */
		var searchA = location.search.replace("?", "").replace("/&#38;/gi", "&").replace("/&amp;/gi", "&").split("&");
		var searchObj = {};
		for(var i = 0; i < searchA.length; i++) {
			var temp = searchA[i].split("=");
			searchObj[temp[0]] = temp[1];
		}
		var target = searchObj.section;

		var $section = $(".section");
		var isScroll = false;
		var scrollTotal = $section.length;
		var scrollNum = $section.index($section.filter("#" + target));
		if (scrollNum < 0) scrollNum = 0;

		$("html, body").css({ overflow:"hidden", height:"100%" });
		section_scroll(scrollNum);

		$(document).on("mousewheel DOMMouseScroll", function(_e) {

			_e.preventDefault();
			var delta = (_e.type == "mousewheel") ? _e.originalEvent.wheelDelta : -_e.originalEvent.detail;
			var index = (delta < 0) ? scrollNum + 1 : scrollNum - 1;
			section_scroll(index);

		});

		$(document).on("keydown", function(_e) {

			if (_e.keyCode != 33 && _e.keyCode != 38 && _e.keyCode != 34 && _e.keyCode != 40 && _e.keyCode != 36 && _e.keyCode != 35) return;
			_e.preventDefault();
			switch(_e.keyCode) {
				case 33:
				case 38:
					section_scroll(scrollNum - 1);
				break;
				case 34:
				case 40:
					section_scroll(scrollNum + 1);
				break;
				case 36:
					section_scroll(0);
				break;
				case 35:
					section_scroll(scrollTotal);
				break;
			}

		});

		function section_scroll(_index) {

			if (isScroll) return;
			isScroll = true;

			scrollNum = _index;
			if (scrollNum < 0) scrollNum = 0;
			if (scrollNum > scrollTotal) scrollNum = scrollTotal;

			var scroll = (scrollNum < scrollTotal) ? $(window).height() * scrollNum : $(window).height() * (scrollTotal - 1) + $("footer").outerHeight();
			TweenMax.to("#fullpage", 1, { marginTop:-scroll, ease:Cubic.easeInOut,
				onComplete:function() {
					isScroll = false;
				}
			});

		}

		/* 리사이징 이벤트 */
		$(window).on("load resize", resize_handler);
		resize_handler();

		function resize_handler() {

			$section.css({ height:$(window).height() });
			slideList.reloadSlider(); // 하단 슬라이드

		}

	});
</script>

<div id="contents">
	<main class="premium-brand-main">
		<!-- [D] 동영상 팝업소스 -->
		<div id="light" class="white_content">
		<div id="qq-dial" class="dial" title='qq'>
		  <div id="video" style="overflow:hidden;">
			<div class="dialContent">
			  <iframe width="900" height="506" id="movie_frame" src="" frameborder="0" allowfullscreen> </iframe>
			</div>
		  </div>
		</div>
		<a class="btn_x" href = "javascript:javascript:;" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'"><img src="../static/img/premium/btn_x.png" alt="닫기" /></a>
		</div>
		<div id="fade" class="black_overlay"></div>
		<!-- // [D] 동영상 팝업소스 -->
		<div id="fullpage">
            <!--
                (D) .section에 연결된 배경 이미지의 관리(개발/관리자에서 추가하는 등)를 위해 html에 인라인 스타일 background-image로 변경했습니다.
                .section에 id를 넣어 http://url.page.html?section=id 형식으로 페이지에 접속하면 해당 위치에 바로 접근할 수 있습니다.
                기존 앵커(해시 #id)대신 section변수를 사용하여 접근하도록 했습니다.
                현재 id는 임의로 적용했습니다.
            -->
		<?if($pb_info->use_cube=='Y'){?>
            <div id="section_cube" class="section" style="background-image:url('<?=$imagepath_b.$pb_info->brand_bg?>');">
                <div class="section-inner">
                    <div class="logo">
						<!-- <img src="../static/img/premium/logo_nike.jpg" width="158" height="83" alt=""/> -->
						<img src="<?=$imagepath_b.$pb_info->brand_logo?>" width="158" height="83" alt="로고">
					</div>
                    <div class="cube">
                        <!--
                            (D) a.btn_over 구조를 추가하면 마우스 오버 시 회전하는 모션이 적용됩니다.
                            a요소의 href 값이 .section id에 있을 경우, 해당 위치로 스크롤 됩니다.
                            (id에 없으면 스크롤 되지 않습니다.)
                        -->
					<?if($cube_list){?>
						<ul>
						<?foreach($cube_list as $c_val){?>
							<li>
							<?if($c_val->type2=='i'){//이미지 타입?>
								<?if($c_val->img2){//마우스 오버 이미지가 있을때?>
									<?if($c_val->link_type=='a'){?>
										<a class="btn_over move_section" style="cursor:pointer;" data-link="<?=$c_val->link?>">
									<?}else{?>
										<?if($c_val->link){ ?>
										<a class="btn_over" href="<?=$c_val->link?>">
										<?} ?>
									<?}?>
										<img src="<?=$imagepath_b.$c_val->img?>" alt="" />
										<?if($c_val->img2){?>
										<img class="over" src="<?=$imagepath_b.$c_val->img2?>" alt="" />
										<?}?>
									</a>
								<?}else{?>
									<?if($c_val->link_type=='a'){?>
									<a style="cursor:pointer;" data-link="<?=$c_val->link?>" class="move_section">
									<?}else{?>
										<?if($c_val->link){ ?>
										<a href="<?=$c_val->link?>">
										<?} ?>
									<?}?>
										<img src="<?=$imagepath_b.$c_val->img?>" alt="" />
									</a>
								<?}?>
							<?}else if($c_val->type2=='m'){//동영상 타입?>
								<a  class="view_movie" data-src="<?=$c_val->link?>">
                                    <img src="<?=$imagepath_b.$c_val->img?>" alt="" />
                                </a>
							<?}?>
							</li>
						<?}?>
						</ul>
					<?}?>
						<?/*
                        <ul>
                            <li>
                                <a class="btn_over" href="#section3">
                                    <img src="../static/img/premium/cube1.jpg" alt="" />
                                    <img class="over" src="../static/img/premium/cube1_over.jpg" alt="" />
                                </a>
                            </li>
                            <li>
                                <a class="btn_over" href="#section6">
                                    <img src="../static/img/premium/cube2.jpg" alt="" />
                                    <img class="over" src="../static/img/premium/cube2_over.jpg" alt="" />
                                </a>
                            </li>
                            <li><img src="../static/img/premium/cube3.jpg" alt="" /></li>
                            <li><img src="../static/img/premium/cube4.jpg" alt="" /></li>
                            <li>
                                <a href="javascript:void(0)" onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'">
                                    <img src="../static/img/premium/cube5.jpg" alt="" />
                                </a>
                            </li>
                            <li><img src="../static/img/premium/cube6.jpg" alt="" /></li>
                            <li><img src="../static/img/premium/cube7.jpg" alt="" /></li>
                            <li><img src="../static/img/premium/cube8.jpg" alt="" /></li>
                            <li><img src="../static/img/premium/cube9.jpg" alt="" /></li>
                        </ul>
						*/?>
                    </div>
                </div>
            </div>
		<?}?>

		<?foreach($section_list as $key=>$s_val){?>
            <div id="section<?=$key?>" class="section section_list" style="background-image:url('<?=$imagepath_b.$s_val->img?>');cursor:pointer;" data-link="<?=$s_val->link?>">
                <div class="section-inner">
					<!--
                        (D) 별도 내용이 필요할 경우, 이 곳에 넣어주세요.
                        .section(화면)의 중앙에 위치해 보여집니다.
                    -->
                </div>
            </div>
		<?}?>
		<?if($slide_list){?>
            <div id="section_slide" class="section"> <!--슬라이드 전용 섹션-->
                <div class="slide">
                    <ul class="slide-list">
					<?foreach($slide_list as $slide){?>
						<li style="background-image:url('<?=$imagepath_b.$slide->img?>');" data-link="<?=$slide->link?>" class="p_slide">
						</li>
					<?}?>
						<!--
                        <li style="background-image:url('../static/img/premium/view_1.jpg');"></li>
                        <li style="background-image:url('../static/img/premium/view_2.jpg');"></li>
                        <li style="background-image:url('../static/img/premium/view_3.jpg');"></li>
                        <li style="background-image:url('../static/img/premium/view_4.jpg');"></li>
                        <li style="background-image:url('../static/img/premium/view_5.jpg');"></li>
                        <li style="background-image:url('../static/img/premium/view_6.jpg');"></li>
						-->
                    </ul>
                </div>
            </div>
		<?}?>
			<?/*>
            <div id="section4" class="section" style="background-image:url('../static/img/premium/color.jpg');">
                <div class="section-inner">
                    <!--
                        (D) 별도 내용이 필요할 경우, 이 곳에 넣어주세요.
                        .section(화면)의 중앙에 위치해 보여집니다.
                    -->
                </div>
            </div>
			*/?>
        </div>
	</main>
</div>
<!-- // [D] 퍼블 추가 -->

<script>
function view_movie()
{
	var src = $(this).data('src');
	var url = "http://www.youtube.com/embed/"+src;
	$("#movie_frame").attr("src",url);
	document.getElementById('light').style.display='block';
	document.getElementById('fade').style.display='block';
}

function move_section()
{
	var section = $(this).data('link');
	//var offset = $("#section"+section).offset().top;
	//alert(offset);
	section_scroll(section);
}

function section_link()
{
	var link = $(this).data('link');
	if(link){location.href=link;}
}

function slide_link()
{
	var link = $(this).data('link');
	if(link){location.href= "/front/"+link;}
}



$(document).on("click",".view_movie",view_movie);

$(document).on("click",".move_section",move_section);

$(document).on("click",".section_list",section_link);

$(document).on("click",".p_slide",slide_link);

</script>


<?php
include ($Dir."lib/bottom.php")
?>
