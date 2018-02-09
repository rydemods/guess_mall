<?php

if(strlen($Dir)==0) {
	$Dir="../";
}
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."lib/recipe.class.php");
include_once($Dir."conf/config.php");
include_once($Dir."lib/timesale.class.php");
$timesale=new TIMESALE();


if ($_data->frame_type=="N" || strlen($_data->frame_type)==0) {	//투프레임
	$_REQUEST["id"]=(isset($_REQUEST["id"])?$_REQUEST["id"]:"");
	$_REQUEST["passwd"]=(isset($_REQUEST["passwd"])?$_REQUEST["passwd"]:"");
	$_REQUEST["type"]=(isset($_REQUEST["type"])?$_REQUEST["type"]:"");

	if ((strlen($_REQUEST["id"])>0 && strlen($_REQUEST["passwd"])>0) || $_REQUEST["type"]=="logout" || $_REQUEST["type"]=="exit") {
		include($Dir."lib/loginprocess.php");
		exit;
	}
}

if(file_exists($Dir.DataDir."shopimages/etc/logo.gif")) {
	$width = getimagesize($Dir.DataDir."shopimages/etc/logo.gif");
	$logo = "<img src=\"".$Dir.DataDir."shopimages/etc/logo.gif\" border=0 ";
	if($width[0]>200) $logo.="width=175 ";
	if($width[1]>65) $logo.="height=80 ";
	$logo.=">";
} else {
	$logo = "<img src=\"".$Dir."images/".$_data->icon_type."/logo.gif\" border=0>";
}

if ($_data->frame_type=="N") {
	$main_target="target=main";

	$result2 = pmysql_query("SELECT rightmargin FROM tbltempletinfo WHERE icon_type='".$_data->icon_type."'",get_db_conn());
	if ($row2=pmysql_fetch_object($result2)) $rightmargin=$row2->rightmargin;
	else $rightmargin=0;
	pmysql_free_result($result2);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>

<? include($Dir."lib/style.php") ?>
<SCRIPT LANGUAGE="JavaScript">
<!--

function sendmail() {
	window.open("<?=$Dir.FrontDir?>email.php","email_pop","height=100,width=100");
}
function estimate(type) {
	if(type=="Y") {
		window.open("<?=$Dir.FrontDir?>estimate_popup.php","estimate_pop","height=100,width=100,scrollbars=yes");
	} else if(type=="O") {
		if(typeof(top.main)=="object") {
			top.main.location.href="<?=$Dir.FrontDir?>estimate.php";
		} else {
			document.location.href="<?=$Dir.FrontDir?>estimate.php";
		}
	}
}
function privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function order_privercy() {
	window.open("<?=$Dir.FrontDir?>privercy.php","privercy_pop","height=570,width=590,scrollbars=yes");
}
function logout() {
	location.href="<?=$Dir.MainDir?>main.php?type=logout";
}
function sslinfo() {
	window.open("<?=$Dir.FrontDir?>sslinfo.php","sslinfo","width=100,height=100,scrollbars=no");
}
function memberout() {
	if(typeof(top.main)=="object") {
		top.main.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	} else {
		document.location.href="<?=$Dir.FrontDir?>mypage_memberout.php";
	}
}
function notice_view(type,code) {
	if(type=="view") {
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type+"&code="+code,"notice_view","width=450,height=450,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>notice.php?type="+type,"notice_view","width=450,height=450,scrollbars=yes");
	}
}
function information_view(type,code) {
	if(type=="view") {
		window.open("<?=$Dir.FrontDir?>information.php?type="+type+"&code="+code,"information_view","width=600,height=500,scrollbars=yes");
	} else {
		window.open("<?=$Dir.FrontDir?>information.php?type="+type,"information_view","width=600,height=500,scrollbars=yes");
	}
}
function GoPrdtItem(prcode) {
	window.open("<?=$Dir.FrontDir?>productdetail.php?productcode="+prcode,"prdtItemPop","WIDTH=800,HEIGHT=700 left=0,top=0,toolbar=yes,location=yes,directories=yse,status=yes,menubar=yes,scrollbars=yes,resizable=yes");
}
//-->
</SCRIPT>
</head>

<body rightmargin="<?=$rightmargin?>" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" style="overflow-x:hidden;overflow-y:hidden;">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
<?php
}
?>
<script type="text/javascript" src="<?=$Dir?>js/jquery.1.9.1.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery.sudoSlider.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery.color-2.1.2.min.js"></script>
<script type="text/javascript" src="<?=$Dir?>js/custom.js"></script>
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>js/jquery-ui.js"></script>

<style type="text/css">
#slidemenu{background:#12cf3d;position:absolute;width:100px;top:1px;right:1px;}
</style>


<!-- 헤더 -->
<script LANGUAGE="JavaScript">
function bookmark(title,url){
	if(document.all){
		window.external.AddFavorite(url, title);
	}
	// Google Chrome
	else if(window.chrome){
		alert("Ctrl+D키를 누르시면 즐겨찾기에 추가하실 수 있습니다.");
	}
	// Firefox
	else if (window.sidebar) // firefox
	{
	window.sidebar.addPanel(title, url, "");
	}
	// Opera
	else if(window.opera && window.print)
	{ // opera

	var elem = document.createElement('a');
	elem.setAttribute('href',url);
	elem.setAttribute('title',title);
	elem.setAttribute('rel','sidebar');
	elem.click();
	}
}

</script>

<?php
if($_data->align_type=="Y") echo "<center>";
?>


<body>
<!-- wrap start -->
<div id="m_wrap">

<!-- top start -->
   <div class="topWrap">
		<div id="top">
			<div class="left">
			     <ul id="tabMenu3">
				<?
					$page_over=reset(explode(".",end(explode("/",$_SERVER["PHP_SELF"]))));
					if($page_over=="company"){
						$over_01="_over";
					}else if($page_over=="board"){
						if(end(explode("board=",$_SERVER["QUERY_STRING"]))=="recipesix"){
							$over_02="_over";
						}
					}else if($page_over=="recipe"){
						$over_03="_over";
					}
				?>
					<li><a href="<?=$Dir.FrontDir?>company.php"><img src="<?=$Dir?>image/header/top_left_01<?=$over_01?>.gif" alt="에코팩토리" /></a></li>
					<li><a href="<?=$Dir.BoardDir?>board.php?board=recipesix"><img src="<?=$Dir?>image/header/top_left_02<?=$over_02?>.gif" alt="에코팩토리 아카데미" /></a></li>
					<li><a href="<?=$Dir.FrontDir?>recipe.php"><img src="<?=$Dir?>image/header/top_left_03<?=$over_03?>.gif" alt="레시피" /></a></li>
					<li><a href="#none" id="all_cate" title="전체보기"><img src="<?=$Dir?>image/header/top_left_04.gif" id="arr_cate"></a></li>
			    </ul>
			</div>
			<div class="right">
				<!--logout-->
				<ul>
				<?if(strlen($_ShopInfo->getMemid())==0){######## 로그인을 안했다#######?>
					<li><a href="<?=$Dir.FrontDir?>login.php">로그인</a></li>
					<li><A HREF="<?=$Dir.FrontDir?>member_jointype.php" <?=$main_target?>>회원가입</a></li>
					<li><a href="<?=$Dir.FrontDir?>login.php">마이페이지</a></li>
				<?}else{?>
					<li><a href="javascript:logout();">로그아웃</a></li>
					<li class="my_button_li">
                     <div class="my_menu">
						<dl>
							<dt><a href="<?=$Dir.FrontDir?>mypage.php">마이페이지</a></dt>
							<dd><a href="<?=$Dir.FrontDir?>wishlist.php">상품보관함</a></dd>
							<dd><a href="<?=$Dir.FrontDir?>mypage_orderlist.php">구매목록</a></dd>
							<dd><a href="<?=$Dir.FrontDir?>mypage_usermodify.php">회원정보수정</a></dd>
							<dd><a href="<?=$Dir.FrontDir?>mypage_reserve.php">에코머니내역</a></dd>
							<dd><a href="<?=$Dir.FrontDir?>mypage_orderlist.php">현금영수증</a></dd>
						</dl>
                     </div>
					</li>

				<?}?>

					<li><a href="<?=$Dir.FrontDir?>basket.php">장바구니</a></li>
					<li><a href="<?=$Dir.FrontDir?>mypage_orderlist.php">주문/배송조회</a></li>
					<li><a href="<?=$Dir.FrontDir?>cscenter.php">고객센터</a></li>
					<li class="favorite over"><a href="javascript:bookmark('<?=$_data->shoptitle?>','http://<?=$_data->shopurl?>');"><img src="<?=$Dir?>image/header/top_nav_07.gif" alt="즐겨찾기" /></a></li>
				</ul>

				<!--login-->
				<!--
				<ul>
					<li><a href="#">로그아웃</a></li>
					<li><a href="#">정보수정</a></li>
					<li class="my"><a href="mypage.html">마이페이지</a></li>
					<li><a href="cart.html">장바구니</a></li>
					<li><a href="mypage_orderlist.html">주문/배송조회</a></li>
					<li><a href="cscenter.html">고객센터</a></li>
					<li class="favorite over"><a href="#"><img src="<?=$Dir?>image/header/top_nav_07.gif" alt="즐겨찾기" /></a></li>
				</ul>
				-->
			</div>

			<div id="viewallcate">
			<? include "tem_viewmenu001.php" ; ?>
			<p class="close_all_menu"><a href="#none" id="close_allmenu" title="전체보기"><img src="<?=$Dir?>image/header/bt_close.png"></a></p>
			</div>

			<div id="slidemenu">
			    	<!--quick_r start  -->
			<div id="quick_r">
				<div class="scroll_cover"><img src="<?=$Dir?>image/header/scroll_cover.png"  /></div>
					<ul>
						<li><span class="quick_bt"><a href="<?=$Dir.BoardDir?>board.php?board=notice"><img src="<?=$Dir?>image/header/scroll_bt1.png" alt="공지사항" /></a></span></li>
						<li><span class="quick_bt"><a href="<?=$Dir.FrontDir?>mypage.php"><img src="<?=$Dir?>image/header/scroll_bt2.png" alt="마이페이지" /></a></span></li>
						<li><span class="quick_bt"><a href="<?=$Dir.FrontDir?>basket.php"><img src="<?=$Dir?>image/header/scroll_bt3.png" alt="장바구니" /></a></span></li>
						<li><span class="quick_bt"><a href="<?=$Dir.FrontDir?>wishlist.php"><img src="<?=$Dir?>image/header/scroll_bt4.png" alt="상품보관함" /></a></span></li>
						<li><span class="quick_bt"><a href="<?=$Dir.FrontDir?>myrecipe.php"><img src="<?=$Dir?>image/header/scroll_bt5.png" alt="레시피" /></a></span></li>
						<li><span class="quick_bt2"><a href="#"><img src="<?=$Dir?>image/header/scroll_bt6.png" alt="top" /></a></span></li>
						<li><span class="quick_bt2"><a href="http://blog.naver.com/suejwang" target="_blank"><img src="<?=$Dir?>image/header/scroll_bt7.png" alt="blog" /></a></span></li>
						<li><span class="quick_bt2"><a href="http://cafe.naver.com/ecofactorycafe" target="_blank"><img src="<?=$Dir?>image/header/scroll_bt8.png" alt="cafe" /></a></span></li>
					</ul>
				<div></div>
			</div><!--//end quick_r-->
			</div>


			<script type="text/javascript">
			    $(document).ready(function()
			    // 문서가 로드될 때
			    {
			        var currentPosition = parseInt($("#slidemenu").css("top"));
			        // slidemenu div가 top일 때의 px를 int형으로 변환 = 1
			        $(window).scroll(function()
			        // 스크롤바가 움직일 때 마다 콜백 한다
			        {
			            var position = $(window).scrollTop();
			            // 현재 스크롤의 top위치 스크롤 됐을 때만
			            //alert("currentPosition : " +currentPosition + " Position : " + position);
			            $("#slidemenu").stop().animate({"top":position+currentPosition+"px"},1000);
			// slidemenu를 움직이기, animate메소드는 두개의 인자를 가진다
			// animate("속성","값") or 위치, 시간
			// position :1 + currentPosition : 현재 스크롤의 top 위치
			        });
			    });
				
				function search_out(sname){
					sname.value="";
				}
			</script>


		</div><!--//end top-->
	</div><!--//end topWrap-->

	<!-- header start -->
	<div id="header">
		<div class="t_banner">
		<?
		$t_qry="select * from tblmainbannerimg where banner_name='top_left'";

		$t_result=pmysql_query($t_qry);
		$t_data=pmysql_fetch_object($t_result);
		?>
				<?if($t_data->banner_link!=''){?><a href="<?=$t_data->banner_link?>"><?}?><img src="<?=$Dir."data/shopimages/mainbanner/".$t_data->banner_img?>" alt="banner" /></a>
		</div>
		<div class="logo">
				<a href="<?=$Dir.MainDir?>main.php"><?=$logo?><!--<img src="<?=$Dir?>image/header/logo.gif" alt="logo" />--></a>
		</div>
		<div class="search">
				<form name=search_tform method=get action="<?=$Dir.FrontDir?>productsearch.php" <?=$main_target?>>
					<fieldset>
					  <legend>검색</legend>
					  <ul>
					    <li><img src="<?=$Dir?>image/header/search_01.gif" alt="search" /></li>
					  	<li><input id="search" name="search" class="inputTypeText" type="text" style="line-height: 35px;"value="호호바 오일 -> 호호바 로 검색" onclick="javascript:search_out(this);" /></li>
					  	<li><a href="javascript:TopSearchCheck()"><input type="image" src="<?=$Dir?>image/header/search_03.gif" alt="검색" /></a></li>
					  </ul>
					</fieldset>
				</form>
				<ul>
					<li><a href="javascript:alert('준비중입니다.');" class="over"><img src="<?=$Dir?>image/header/header_nav_01.gif" alt="모바일앱" /></a></li>
					<li><a href="<?=$Dir.BoardDir?>board.php?board=event" class="over"><img src="<?=$Dir?>image/header/header_nav_02.gif" alt="이벤트" /></a></li>
					<li><a href="<?=$Dir.FrontDir?>productlist.php?code=011" class="over"><img src="<?=$Dir?>image/header/header_nav_03.gif" alt="이달의할인" /></a></li>
					<li><a href="javascript:alert('준비중입니다.');" class="over"><img src="<?=$Dir?>image/header/header_nav_04.gif" alt="서포터즈" /></a></li>
				</ul>
		</div>
		<div class="menu">
			<div style="text-align:left"><img src="<?=$Dir?>image/header/fresh_lecipe_01.png" alt="image" /></div>
			<ul id="lecipe_wrap">
			<li>
			<div class="lecipe"><a href="/front/recipe.php"><img src="<?=$Dir?>image/header/fresh_lecipe_02.png" alt="신선한 레시피"  class="lecipebt"/></a></div>

<!-- lecipe start -->
				<div class="lecipe_box">
					 <div class="lecipe_box_wrap">




			<ul>
				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=001001000000">화장품</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("001001000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=001002000000">비누</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("001002000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=001003000000">생활용품</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("001003000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=001004000000">헤어케어</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("001004000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=001005000000">베이비</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("001005000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>
		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=001006000000">기타</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("001004000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>

		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=002000000000">기능별</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("002000000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>

		</dl>
		        </li>

				<li>
		<dl>
			<dt><a href="/front/recipe.php?code=003000000000">피부타입별</a></dt>
			<?
			$cate3 = RECIPE::getRecipeCategoryList("003000000000");
			foreach($cate3 as $v3){
			?>
			<dd><a href="/front/recipe.php?code=<?=$v3[code]?>"><?=$v3[code_name]?></a></dd>
			<?}?>

		</dl>
		        </li>


			</ul>


					</div>
			    </div>
<!-- lecipe end -->

	</li>
	</ul>
			<ul id="top_category">
				<li style = 'width:74px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=003"><img src="<?=$Dir?>image/header/menu_01.png" alt="베이스오일" /></a></div>

<!-- category1 start -->
				<div class="sdt_box cat1">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Base Oil</font><br>
					 <font class="sdt_font1">베이스오일</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '003%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

					</div>
			    </div>
<!-- category1 end -->

				</li>
				<li style = 'width:114px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=004"><img src="<?=$Dir?>image/header/menu_02.png" alt="아로마오일,워터류" /></a></div>
<!-- category2 start -->
				<div class="sdt_box cat2">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Aroma Oil,<br>Water</font><br>
					 <font class="sdt_font1">아로마오일,워터류</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '004%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

				 </div>
			  </div>
<!-- category2 end -->
				</li>
				<li style = 'width:76px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=006"><img src="<?=$Dir?>image/header/menu_03.png" alt="화장품 원료" /></a></div>
<!-- category3 start -->
				<div class="sdt_box cat3">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Cosmetics<br>Raw Material</font><br>
					 <font class="sdt_font1">화장품 원료</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '006%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

					</div>
			    </div>
<!-- category3 end -->
				</li>
				<li style = 'width:65px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=002"><img src="<?=$Dir?>image/header/menu_04.png" alt="비누 원료" /></a></div>
<!-- category4 start -->
				<div class="sdt_box cat4">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Soap<br>Raw Material</font><br>
					 <font class="sdt_font1">비누 원료</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '002%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

					</div>
			    </div>
<!-- category4 end -->
				</li>
				<li style = 'width:70px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=034"><img src="<?=$Dir?>image/header/menu_05.png" alt="분말, 허브" /></a></div>
<!-- category5 start -->
				<div class="sdt_box cat6">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Candle<br>Diffuser</font><br>
					 <font class="sdt_font1">캔들, 디퓨저</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '034%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

					</div>
			    </div>
<!-- category5 end -->
				</li>
				<li style = 'width:39px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=009"><img src="<?=$Dir?>image/header/menu_06.png" alt="용기" /></a></div>
<!-- category6 start -->
				<div class="sdt_box cat6">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Container</font><br>
					 <font class="sdt_font1">용기</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '009%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

					</div>
			    </div>
<!-- category6 end -->
				</li>
				<li style = 'width:64px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=010"><img src="<?=$Dir?>image/header/menu_07.png" alt="예쁜 포장" /></a></div>
<!-- category7 start -->
				<div class="sdt_box cat7">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Wrapping</font><br>
					 <font class="sdt_font1">예쁜 포장</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '010%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>
					</div>
			    </div>
<!-- category7 end -->
				</li>
				<li style = 'width:76px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=007"><img src="<?=$Dir?>image/header/menu_08.png" alt="만들기 도구" /></a></div>
<!-- category8 start -->
				<div class="sdt_box cat8">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Tools</font><br>
					 <font class="sdt_font1">만들기 도구</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '007%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>

					</div>
			    </div>
<!-- category8 end -->
				</li>
				<li style = 'width:65px;'>
				<div class="sdt_wrap"><a href="<?=$Dir.FrontDir?>productlist.php?code=001"><img src="<?=$Dir?>image/header/menu_09.png" alt="키트 세트" /></a></div>
<!-- category9 start -->
				<div class="sdt_box cat9">
					 <div class="sdt_box_wrap">
					 <span>
					 <font class="sdt_font2">Kit Set</font><br>
					 <font class="sdt_font1">키트 세트</font>
					</span>
					<span>
					<dl>
					<?
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') and code_a like '001%' and code_b!='000' and code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						$rownum=pmysql_num_rows($result);

						if($rownum<=7){
							$renum="5";
						}else{
							$renum="5";
						}
						$i=0;
						while($cate_data=pmysql_fetch_object($result)){

						if($i==$renum){
							$i=0;
					?>
							</dl></span><span><dl>
					<?
						}
					?>
						<dd><a href="<?=$Dir.FrontDir?>productlist.php?code=<?=$cate_data->code_a.$cate_data->code_b.$cate_data->code_c.$cate_data->code_d?>"><?=$cate_data->code_name?></a></dd>
					<?$i++;}?>
					</dl>
					</span>
					</div>
			    </div>
<!-- category9 end -->
				</li>
			</ul>
		</div>

	</div><!--//end header-->


</div>

<?php
if($_data->align_type=="Y") echo "<center>";
if ($_data->frame_type=="N") {
?>
	</td>
</tr>
</table>
</body>
</html>
<?php
}
?>
<!-- # 헤더 -->
