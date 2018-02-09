<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member['id']=$_ShopInfo->getMemid();
$member['name']=$_ShopInfo->getMemname();
$tags		= array('1'=>'oryany','2'=>'폴리백팩','3'=>'몰리백팩','4'=>'박수진가방');
$tab	= $_GET['tab'];
if (!$tab) $tab		= 1;
//var_dump($member);

?>
<!--php끝-->
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<style>
section.instagram_wrap {margin-top:30px; background-color:#FFF;}
section.instagram_wrap h4 {font:1.125em ng; color:#666; font-weight:lighter; margin-top:20px; background-color:#fff; padding-bottom:10px; border-bottom:1px solid #dcdcdc;}
section.instagram_wrap div.instagram_area {text-align:center;  background-color:#ececec;}
section.instagram_wrap a.more {background:#ccc; border:none !important;color:#fff; font:1em/35px ngb; text-decoration:none;}

/*** 인스타그램 추가 S (20150311_김재수) ***/
/* user_insta_picArea */
section.instagram_wrap {background-color:#ececec;}
section.instagram_wrap article.insta_list p.insta_pic_non {text-align:center; padding:50px 0px 50px 0px;display:none;}

section.instagram_wrap article.none {display:none;}
section.instagram_wrap div.tab_menu {width:1100px; margin:0 auto; *zoom:1;}
section.instagram_wrap div.tab_menu a {display:inline-block;border:1px solid #cdcdcd;padding: 20px 50px}
section.instagram_wrap div.tab_menu a.on {background-color:red;color:#fff;}

section.instagram_wrap article.insta_list div.insta_pic_list {width:1100px; margin:0 auto; *zoom:1;}
section.instagram_wrap article.insta_list div.insta_pic_list:after {display: block; clear: both; content: '';}
section.instagram_wrap article.insta_list div.insta_pic_list ul.pic_list li div.insta_row {display:block; width:260px; float:left; position:relative; margin:7px; background:#fff;}
section.instagram_wrap article.insta_list div.insta_pic_list ul.pic_list li div.insta_row a.insta_pic {display:block; width:260px; height:260px; float:left; position:relative;}
section.instagram_wrap article.insta_list div.insta_pic_list ul.pic_list li div.insta_row a.insta_pic img {width:100%;}
section.instagram_wrap article.insta_list div.insta_pic_list ul.pic_list li div.insta_row a.insta_pic span.like_mark {position:absolute; display:inline-block; bottom:0; left:0; background:#000; opacity:0.7; color:#fff; font-size:13px; width:230px;text-align:right;padding:10px 15px;}

section.instagram_wrap article.insta_list div.ment {text-align:center; padding:30px 0px 30px 0px; font:1em ngb; color:#333;}
section.instagram_wrap article.insta_list div.ment span{display:block;padding-top:20px;font-size:12px; color:#666;}

section.instagram_wrap article.insta_list div.btn {text-align:center; padding:3px 0 0 0;}
section.instagram_wrap article.insta_list div.btn a.more {border-top:1px solid #dcdcdc;border-bottom:1px solid #dcdcdc;}
section.instagram_wrap article.insta_list div.btn a.go_first {display:none;}
section.instagram_wrap article.insta_list div.btn a.go_first.on {display:block;}
section.instagram_wrap article.insta_list div.btn div.more_ico {padding:11px 5px 14px 5px; display:none;}
section.instagram_wrap article.insta_list div.btn div.more_ico img {width:16px; height:16px;}

/*instaDetailPopup*/
.dimmed{display:none;position:fixed;top:0;left:0;z-index:100;width:100%;height:100%;background-color:#000;opacity:0.5;filter:alpha(opacity='50');}
div.ly_pop{display:none;overflow:hidden;position:fixed;width:990px;height:640px;top:50%;left:50%;margin:-320px 0 0 -495px;z-index:150}
div.ly_pop .img_wrap{float:left;width:640px;background-color:#ffffff}
div.ly_pop .ly_cont{overflow:hidden;float:right;width:330px;height:600px;padding:20px 10px;background-color:#ffffff}
div.ly_pop .ly_cont h1{height:24px;padding-bottom:20px;}
div.ly_pop .ly_cont h1 span{width:103px;height:24px;font-family:tahoma,'돋움',dotum,sans-serif;font-size:16px;line-height:11px;}
div.ly_pop .ly_cont .info{padding:30px;font-family:tahoma,'돋움',dotum,sans-serif;text-align:center}
div.ly_pop .ly_cont .me{display:inline-block;position:relative;width:100px;height:100px}
div.ly_pop .ly_cont .name{margin-top:18px;font-size:16px;font-weight:bold;line-height:18px;color:#3f729b}
div.ly_pop .ly_cont .date{margin-top:6px;font-size:13px;color:#848a96}
div.ly_pop .ly_cont .tags{margin-top:22px;font-family:'굴림',gulim,tahoma,'돋움',dotum,sans-serif;font-size:12px;line-height:22px;color:#000;word-wrap:break-word}
div.ly_pop .sns_like{height:52px;background-position:0 -165px}
div.ly_pop .sns_like .bn_inner{width:6px;height:52px;background-position:-157px -165px}
div.ly_pop .bn_lyclose{position:absolute;top:23px;right:20px;width:18px;height:19px}

div.ly_pop .bn_like2{display:inline-block;width:117px;height:32px;margin-top:30px;padding-top:20px;text-decoration:none}
div.ly_pop .bn_like2 em{display:inline-block;width:13px;height:14px;text-indent:-9999px}
div.ly_pop .bn_like2 span{margin-left:4px;font-family:tahoma,'돋움',dotum,sans-serif;font-size:13px;line-height:11px;vertical-align:top;color:#000;background:none}

/*** 인스타그램 추가 E (20150311_김재수) ***/

</style>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<script type="text/javascript" src="<?=$Dir?>js/instagramAPI.js"></script>
<script type="text/javascript">
var isFirst = true;  //처음인지

$(document).ready(function() {
	var $loader = $("#load_message");
	$loader.show();
	accessInstagramAPI();
	//goListScroll();
	var list=$("#layer-slide-wrap ul li");
	var $list=list.length;

	$(".next").on("click",function(){
		$("#layer-slide-wrap ul.photo-list").animate({
		left:"-=940px"},500,function(){
			$("#layer-slide-wrap ul.photo-list li").eq(0).appendTo($("ul.photo-list"));
			$("ul").css("left","0px")
				});//innerfunc
	});//next
	$(".prev").on("click",function(){
		$("#layer-slide-wrap ul.photo-list li").eq($list-1).prependTo($("ul.photo-list"));
		$("ul").css("left","-940px")
		$("#layer-slide-wrap ul.photo-list").animate({left:"0px"},500)//innerfunc
	});//prev

	$('.layer-photoList p.close').click(function(){
		$('.layer_photoList_wrap').hide();
		$("#layer-slide-wrap ul.photo-list").html("");
	});

	$(document).on( 'click', '#hhw-wrapper ul.list-hhg-gallery li', function( e ){
		var openIndex = $(this).index();

		//$("#layer-slide-wrap ul.photo-list li").eq($list-1).prependTo($("ul.photo-list"));

		$("#tempPotoList li").each( function( index, obj ){
			//var tempObj = $(this).html();
			$(this).clone().appendTo($("#layer-slide-wrap ul.photo-list"));
		});
		
		$("#layer-slide-wrap ul.photo-list li").each( function( index, obj ){
			if( index <  openIndex ){
				$(this).appendTo($("ul.photo-list"));
			}
		});

		$('.layer_photoList_wrap').show();
	});
	//$('#hhw-wrapper .list-hhg-gallery').
});

instaAPI=new instagramAPI('<?=$tags[$tab]?>','',12);

function accessInstagramAPI() {
	instaAPI.getInstagramList();
}

function goListScroll(){

	var last = instaAPI.isLast();
	var load =  instaAPI.isLoaded();
	var $loader = $("#load_message");
	if(load && !last){
		$loader.show();
		accessInstagramAPI();
	}
}

function storyBegins(){
	storyBeginsURL = "<?=$Dir.FrontDir?>"+"storybegins/";
	window.open(storyBeginsURL,"stPop",'height=' + screen.height + ',width=' + screen.width + "fullscreen=yes,scrollbars=no,resizable=no");
}

</script>
<div class="line_map hhg-head">
	<div class="container">
		<div><em>&gt;</em><a>HAPPY HUNTING GROUND</a><em>&gt;</em><span><a>#TAGS</a></span></div>
		<h3 class="hhg-title">HAPPY HUNTING GROUND</h3>
		<p class="hhg-subtitle">원하는 모든걸 얻을 수 있는 오야니 행복사냥터</p>
		<ul class="hhg-menu">
			<li><a href="javascript:storyBegins();" >story begins</a></li>
			<li><a href="<?$Dir.FrontDir?>special_interest.php">special interest</a></li>
			<li><a href="<?$Dir.FrontDir?>color_we_love.php">color we love</a></li>
			<li><a href="<?$Dir.FrontDir?>instagram.php">play</a></li>
			<li><a class="on">#tags</a></li>
			<li><a href="<?$Dir.FrontDir?>logo_art.php">logo art project</a></li>
		</ul>
	</div>
</div>
<div id='tempPotoList' style="display:none">
</div>
<!-- 레이어팝업 -->
<div class="layer_photoList_wrap" style="display:none"><!-- 임시로 block 넣음. 개발 후 삭제해 주세요 -->
	<div class="layer-photoList">
		<p class="close">닫기</p>
		<div id="layer-slide-wrap">
			<button class="prev">왼쪽</button>
			<button class="next">오른쪽</button>
			<div class="inner">
				<ul class="photo-list">
					
				</ul>
			</div>	
		</div>
	</div>
</div><!-- //레이어팝업 -->

<!-- <section class="instagram_wrap"> -->
	<!-- <h4>INSTAGRAM_TAGS [<?=$tags[$tab]?>]</h4> -->
	<div class='tab_menu' style='display:none;'>
	<a href='?tab=1' <?if ($tab == 1) echo " class='on'";?>><?=$tags[1]?></a>
	<a href='?tab=2' <?if ($tab == 2) echo " class='on'";?>><?=$tags[2]?></a>
	<a href='?tab=3' <?if ($tab == 3) echo " class='on'";?>><?=$tags[3]?></a>
	<a href='?tab=4' <?if ($tab == 4) echo " class='on'";?>><?=$tags[4]?></a>
	</div>
	<!-- start contents -->
	<div class="containerBody sub_skin">	
		<div id="hhw-wrapper">
			<h4 class="content-title">#TAGS</h4>
			<ul class="list-hhg-gallery" >
			</ul>
			<div class="btn">
				<div class="more_ico" id="load_message"><img src="./images/ani_load.gif" alt=""/></div>
				<a href="javascript:goListScroll();" class="more more_list" id="more_list">더보기</a>
				<a href="instagram.php" class="btn_A go_first" id="go_first">처음으로</a>
			</div>
		</div>

	</div>
<!-- </section> -->

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
