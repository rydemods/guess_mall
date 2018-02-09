<?php
$Dir = "../";

include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$imgurl=$Dir.DataDir."shopimages/mainbanner/";

$mainbanner_sql="
select * from tblmainbannerimg where banner_name='maintop_rolling' and banner_hidden='1' ORDER BY banner_sort;";

$mainbanner_res = pmysql_query($mainbanner_sql, get_db_conn());
while($mainbanner_row = pmysql_fetch_array($mainbanner_res)){
	$mainbanner[]=$mainbanner_row;}
//exdebug($mainbanner);


?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<script type="text/javascript" src="../js/jquery-1.10.1.js" ></script>
<script type="text/javascript" src="../js/jquery-ui.js" ></script>
<script type="text/javascript" src="../js/jquery.sudoSlider.js" ></script>
<script type="text/javascript" src="../js/custom.js" ></script>
<script type="text/javascript" src="../css/jquery.slides.min.js"></script>

<link rel="stylesheet" href="../css/nstSlider.css" />
<!--<link rel="stylesheet" href="../css/digiatom.css" />-->
<link rel="stylesheet" href="../css/oryany.css" />
<link rel="stylesheet" href="../css/sub.css" />

<script>
$(function(){  
	var sudoSliderMainTop = $("div.banner_top_slider").sudoSlider({
		effect: "slide",
		continuous:true,
		slideCount:3,
		prevNext:false,
		moveCount:1,
		customLink:'.slidesjs-pagination-item a',
		auto:true,
		pause:4000,
		animationZIndex:80
		
	});		

}); 
</script>
<div id="body_contents">
<?include ($Dir.MainDir.$_data->menu_type.".php");?>
	

<style>

div.main_visual_rolling_wrap {overflow:hidden; position:relative;}
div.main_visual_rolling {width:1100px; margin:auto; /*height:360px;*/ position:relative;}
/*div.main_visual_rolling p.btn {position:absolute; bottom:20px; right:20px;}
div.main_visual_rolling p.btn a.left {display:inline-block;background:url(../img/btn/left_btn.gif) no-repeat; width:50px; height:50px;}
div.main_visual_rolling p.btn a.right {display:inline-block; background:url(../img/btn/right_btn.gif) no-repeat; width:50px; height:50px;}*/
/* 메인비주얼 */
ul.visual_list {display:none; margin-left:-1100px; position:relative;}
div.main_visual_rolling:before {display:block; content:""; width:600px; height: 100%; background:url(../img/common/alpha_w80.png) repeat; position:absolute; top:0px; left:-600px; z-index:1000;}
div.main_visual_rolling:after {display:block; content:""; width:600px; height: 100%; background:url(../img/common/alpha_w80.png) repeat; position:absolute; top:0px; right:-600px; z-index:1000;}

</style>

	<div class="main_visual_rolling_wrap">
			<div class="main_visual_rolling">
				<!--<p class="btn visual_list_btn" style = 'display:none;'>
					<a href="javascript:;" data-target="prev" class="left" style = 'z-index:90;position:relative;'>&nbsp;</a>
					<a href="javascript:;" data-target="next" class="right" style = 'z-index:90;position:relative;'>&nbsp;</a>
				</p>-->
				<ul class="visual_list" style="display:block">
					<li>
						<div  class="banner_top_slider">
							<?for($i=0 ; $i < count($mainbanner) ; $i++){?>
								<img src="<?=$imgurl.$mainbanner[$i][banner_img];?>" alt="" />
							<? } ?>
						</div>
					</li>
				</ul>		
			</div>
			<ul class="slidesjs-pagination">
				<li class="slidesjs-pagination-item">
					<a href="#" data-target="0" class="">1</a>
				</li>
				<li class="slidesjs-pagination-item">
					<a href="#" data-target="1" class="">2</a>
				</li>
				<li class="slidesjs-pagination-item">
					<a href="#" data-target="2" class="">3</a>
				</li>
			</ul>
		</div><!-- //main_visual_rolling_wrap -->
	
<?	include ($Dir."lib/bottom.php");?>
</div>