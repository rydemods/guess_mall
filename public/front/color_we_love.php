<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/paging.php");
include_once($Dir."lib/shopdata.php");

$member['id']=$_ShopInfo->getMemid();
$member['name']=$_ShopInfo->getMemname();

#컬러 번호에 맞게 이동
$colorNum = $_GET['changeNum'];
if( is_null($colorNum) || $colorNum == 'ALL' ) $colorQry = "";
else $colorQry = " AND cb.category_num = '".$colorNum."'";

$iconPath = $Dir.DataDir."shopimages/cwl/category/";
$contentPath = $Dir.DataDir."shopimages/cwl/board/";

# 카테고리 불러오기
$cwlCateSql = "SELECT num, category_name, icoimage FROM tblcwlcategory WHERE secret = 1 ORDER BY sort_num ASC ";
$cwlCateRes = pmysql_query( $cwlCateSql, get_db_conn() );
while( $cwlCateRow = pmysql_fetch_array( $cwlCateRes ) ){
	$cwlCate[] = $cwlCateRow;
}
pmysql_free_result( $cwlCateRes );


# 내용 불러오기
$cwlSql = "SELECT cb.num, cb.title, cb.image, cb.productcode, cb.hit, cc.infoimage, cc.category_name ";
$cwlSql.= "FROM tblcwlboard cb ";
$cwlSql.= "LEFT JOIN tblcwlcategory cc ON cc.num = cb.category_num ";
$cwlSql.= "WHERE cc.secret = 1 ";
$cwlSql.= $colorQry;
$cwlSql.= "ORDER BY cb.date DESC ";
$paging = new newPaging( $cwlSql, 1, 1);
$cwlSql = $paging->getSql($cwlSql);
//exdebug( $cwlSql );
//exdebug( $paging );
$cwlRes = pmysql_query( $cwlSql, get_db_conn() );
$cwlCnt = 0;
while( $cwlRow = pmysql_fetch_array( $cwlRes ) ){
	$cwl[$cwlCnt] = $cwlRow;
	$cwl[$cwlCnt]['productlink'] = $Dir.FrontDir."productdetail.php?productcode=".$cwlRow['productcode'];
	
	$cwlCnt++;
}
pmysql_free_result( $cwlRes );

$rParam = explode("?",$_SERVER['REQUEST_URI']);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 이용약관</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>
<!--php끝-->

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>

<div class="line_map hhg-head">
	<div class="container">
		<div><em>&gt;</em><a>HAPPY HUNTING GROUND</a><em>&gt;</em><span><a>COLOR WE LOVE</a></span></div>
		<h3 class="hhg-title">HAPPY HUNTING GROUND</h3>
		<p class="hhg-subtitle">원하는 모든걸 얻을 수 있는 오야니 행복사냥터</p>
		<ul class="hhg-menu">
			<li><a href="javascript:storyBegins();" >story begins</a></li>
			<li><a href="<?$Dir.FrontDir?>special_interest.php">special interest</a></li>
			<li><a class="on">color we love</a></li>
			<li><a href="<?$Dir.FrontDir?>instagram.php ">play</a></li>
			<li><a href="<?$Dir.FrontDir?>instagram_tags.php">#tags</a></li>
			<li><a href="<?$Dir.FrontDir?>logo_art.php">logo art project</a></li>
		</ul>
	</div>
</div>

<!-- start contents -->
<div class="containerBody sub_skin">
	
	<div id="hhw-wrapper">
	
		<h4 class="content-title">color we love</h4>
		<div class="color-list-wrap">
			<a href="javascript:cateChage('ALL');">
				<img src="../img/common/love_color_thumb_all.gif" alt="전체">
				<span>ALL</span>
			</a>
<? foreach( $cwlCate as $cateKey=>$cateVal ){ // 카테고리 ?>
			<a href="javascript:cateChage('<?=$cateVal['num']?>');">
				<img src="<?=$iconPath.$cateVal['icoimage']?>" alt="<?=$cateVal['category_name']?>">
				<span><?=$cateVal['category_name']?></span>
			</a>
<? } ?>
		</div>

		<ul class="color-item">
<? foreach( $cwl as $key=>$val ){// 내용 ?>
			<li>
				<div class="pic">
					<a href="<?=$val['productlink']?>"><img src="<?=$contentPath.$val['image']?>" alt=""></a>
					<div class="color-show">
						<img src="<?=$iconPath.$val['infoimage']?>" alt="">
						<span><?=$val['category_name']?></span>
					</div>
				</div>
				<div class="item-info">
					<p class="caption"><?=$val['title']?></p>
					<div class="color-social-ico">
						<a href="javascript:twitter_share('<?=$val['title']?>');" class="facebook">페이스북</a>
						<a href="javascript:facebook_share('<?=$val['title']?>','<?="http://".$_SERVER["SERVER_NAME"]."data/shopimages/cwl/board/".$val["image"]?>');" class="twitter">트위터</a>
						<a href="#" class="instagram">인스타그램</a>
						<button class="wish" name='wish_<?=$val['num']?>' onclick="wishUp(<?=$val['num']?>)" ><?=number_format( $val['hit'] )?></button>
					</div>
				</div>
			</li>
<? } ?>
		</ul>
		<div class="paging paging goods_list">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div><!-- 기존 상품리스트에서 사용된 페이징 사용해주세요 -->
	
	</div>

</div>

<form name="cateChageForm" id='cateChageForm' method='GET' action='<?=$_SERVER['PHP_SELF']?>' >
<input type='hidden' name='changeNum' id='changeNum' value='' />
</form>

<form name="pagingForm" id='pagingForm' method='GET' action='<?=$_SERVER['PHP_SELF']?>' >
<input type='hidden' name='gotopage' id='gotopage' value='' />
<input type='hidden' name='block' id='block' value='' />
<input type='hidden' name='changeNum' id='colorNum' value='<?=$colorNum?$colorNum:"ALL"?>' />
</form>

<input type='hidden' name='rParam' id='rParam' value='<?=$rParam[1]?>' />
<script>

$(document).ready(function(){
	if( $("#rParam").val().indexOf('begin') > 0 ){
		setTimeout("storyBegins();", 1000);
	}
});

function cateChage( cateNum ){
	$('#changeNum').val(cateNum);
	$('#cateChageForm').submit();
}

function GoPage( blcok, pageNum ){
	$('#block').val(blcok);
	$('#gotopage').val(pageNum);
	$('#pagingForm').submit();
}

function wishUp( boardNum ){
	

	$.ajax({
		method : "POST",
		url : "color_we_love_like_ajax.php",
		data : { num: boardNum },
		dataType : "json",
		beforeSend : function(){}
	}).done(function( data ){
		//console.log( data );
		console.log( $("button[name='wish_"+boardNum+"']") );
		if( data.CODE == "S001" ){
			var wish_hit = uncomma( $("button[name='wish_"+boardNum+"']").html() );
			alert( data.msg );
			$("button[name='wish_"+boardNum+"']").html( comma( parseInt( wish_hit ) + 1 ) );
		} else if( data.CODE == "E001" ) {
			//alert( data.msg );
			location.href = "../front/login.php";
		} else {
			alert( data.msg );
		}

	});
}

function storyBegins(){
	storyBeginsURL = "<?=$Dir.FrontDir?>"+"storybegins/";
	window.open(storyBeginsURL,"stPop",'height=' + screen.height + ',width=' + screen.width + "fullscreen=yes,scrollbars=no,resizable=no");
}

//SNS(페이스북, 트위터) sharer 처리
function twitter_share( temp_text ){
	var text = "ORYANY NewYork - " + temp_text;
	//var url = "http://nasign.ajashop.co.kr/front/color_we_love.php";
	var url = "http://" + "<?=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>";
	var snsUrl = "http://twitter.com/home?status="+encodeURIComponent(text)+" "+ url;
	var popup = window.open(snsUrl, "_snsPopupWindow", "width=500, height=500");
	popup.focus();
}

function facebook_share( temp_text, temp_image ){
	var text = "ORYANY NewYork - " + temp_text;
	//var url = "http://nasign.ajashop.co.kr/front/color_we_love.php";
	var url = "http://" + "<?=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>";
	//var image = "http://nasign.ajashop.co.kr/front/storybegins/img/common/logo.png";
	var image = temp_image;
	//var summary = "ORYANY NewYork - STORY BEGINS"
	var snsUrl = "http://www.facebook.com/sharer.php?u="+encodeURIComponent(url)+"&t="+encodeURIComponent(text);

	var popup= window.open(snsUrl, "_snsPopupWindow", "width=500, height=500");
	popup.focus();
}

</script>

<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
