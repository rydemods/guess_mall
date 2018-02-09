<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-4";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$filepath=$Dir.DataDir."design/";
$timg_name=array(0=>"메인 추천상품 타이틀",1=>"메인 신규상품 타이틀",2=>"메인 인기상품 타이틀",3=>"메인 경매 타이틀",4=>"메인 공동구매 타이틀",5=>"메인 공지사항 타이틀",6=>"메인 특별상품 타이틀",7=>"메인 정보(information)<br />&nbsp;&nbsp;&nbsp; 타이틀",8=>"메인 투표 타이틀",9=>"이용안내 타이틀",10=>"회사소개 타이틀",11=>"이용약관 타이틀",12=>"회원가입 타이틀",13=>"회원로그인 타이틀",14=>"비밀번호찾기 타이틀",15=>"장바구니 타이틀",16=>"마이페이지 타이틀",17=>"위시리스트 타이틀",18=>"쿠폰보유내역 타이틀",19=>"회원탈퇴 타이틀",20=>"주문내역 타이틀",21=>"1:1고객문의 타이틀",22=>"적립금내역 타이틀",23=>"회원정보수정 타이틀",24=>"단골매장 타이틀",25=>"인기상품 타이틀",26=>"추천상품 타이틀",27=>"신규상품 타이틀",28=>"특별상품 타이틀",29=>"상품검색 타이틀",30=>"사용후기 타이틀",31=>"최근인기태그 타이틀",32=>"쇼핑태그검색 타이틀",33=>"주문서작성 타이틀",34=>"주문완료 타이틀",35=>"브랜드맵 타이틀",36=>"공동구매 타이틀",37=>"경매 타이틀");

$itmg_val=array(0=>"main_hot_title.gif",1=>"main_new_title.gif",2=>"main_best_title.gif",3=>"main_auction_title.gif",4=>"main_gonggu_title.gif",5=>"main_notice_title.gif",6=>"main_special_title.gif",7=>"main_info_title.gif",8=>"main_poll_title.gif",9=>"useinfo_title.gif",10=>"company_title.gif",11=>"agreement_title.gif",12=>"memberjoin_title.gif",13=>"login_title.gif",14=>"findpwd_title.gif",15=>"basket_title.gif",16=>"mypage_title.gif",17=>"wishlist_title.gif",18=>"mycoupon_title.gif",19=>"memberout_title.gif",20=>"orderlist_title.gif",21=>"mypersonal_title.gif",22=>"myreserve_title.gif",23=>"membermodify_title.gif",24=>"mycustsect_title.gif",25=>"productbest_title.gif",26=>"producthot_title.gif",27=>"productnew_title.gif",28=>"productspecial_title.gif",29=>"search_title.gif",30=>"reviewall_title.gif",31=>"tag_title.gif",32=>"tagsearch_title.gif",33=>"order_title.gif",34=>"orderend_title.gif",35=>"brandmap_title.gif",36=>"gonggu_title.gif",37=>"auction_title.gif");

$arrimg=array(&$_FILES["image0"],&$_FILES["image1"],&$_FILES["image2"],&$_FILES["image3"],&$_FILES["image4"],&$_FILES["image5"],&$_FILES["image6"],&$_FILES["image7"],&$_FILES["image8"],&$_FILES["image9"],&$_FILES["image10"],&$_FILES["image11"],&$_FILES["image12"],&$_FILES["image13"],&$_FILES["image14"],&$_FILES["image15"],&$_FILES["image16"],&$_FILES["image17"],&$_FILES["image18"],&$_FILES["image19"],&$_FILES["image20"],&$_FILES["image21"],&$_FILES["image22"],&$_FILES["image23"],&$_FILES["image24"],&$_FILES["image25"],&$_FILES["image26"],&$_FILES["image27"],&$_FILES["image28"],&$_FILES["image29"],&$_FILES["image30"],&$_FILES["image31"],&$_FILES["image32"],&$_FILES["image33"],&$_FILES["image34"],&$_FILES["image35"],&$_FILES["image36"],&$_FILES["image37"]);


$type=$_POST["type"];

if($type=="color") {
	$Rcolor=$_POST["Rcolor"];
	$Gcolor=$_POST["Gcolor"];
	$Bcolor=$_POST["Bcolor"];
	$main_title_color=$Rcolor.$Gcolor.$Bcolor;
	if(ord($main_title_color)==0) $main_title_color="E6E6E6";
	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesign(main_title_color) VALUES('{$main_title_color}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesign SET 
		main_title_color= '{$main_title_color}' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){alert(\"메인 오른쪽 메뉴 배경색 수정이 완료되었습니다.\");}</script>";
} elseif($type=="titleimage") {
	if(is_dir($filepath)==false) {
		mkdir($filepath);
		chmod($filepath,0604);
	}

	for($i=0;$i<count($timg_name);$i++) {
		if(ord($arrimg[$i]["name"])) {
			move_uploaded_file($arrimg[$i]["tmp_name"],$filepath.$itmg_val[$i]);
			chmod($filepath.$itmg_val[$i]."",0664);
			echo $filepath.$itmg_val[$i];
		}
	}
	$onload="<script>window.onload=function(){alert(\"타이틀 이미지 등록/수정이 완료되었습니다.\");}</script>\n";
} elseif($type=="delete") {
	$idx=$_POST["idx"];
	if(file_exists($filepath.$itmg_val[$idx])) {
		unlink($filepath.$itmg_val[$idx]);
	}
	$onload="<script>window.onload=function(){alert(\"해당 타이틀 이미지를 삭제하였습니다.\");}</script>";
}

$sql = "SELECT main_title_color FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$main_title_color=$row->main_title_color;
}
if(ord($main_title_color)==0) $main_title_color="E6E6E6";
$Rcolor=substr($main_title_color,0,2);
$Gcolor=substr($main_title_color,2,2);
$Bcolor=substr($main_title_color,4,2);
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function color_view(rgb) {
	document.form1.Rcolor.value=rgb.substring(0,2);
	document.form1.Gcolor.value=rgb.substring(2,4);
	document.form1.Bcolor.value=rgb.substring(4,6);
	document.all.rgb_preview.style.background="#"+rgb;
}

function delete_title(idx) {
	if(confirm("해당 타이틀 이미지를 삭제하시겠습니까?")) {
		document.form3.type.value="delete";
		document.form3.idx.value=idx;
		document.form3.submit();
	}
}

function CheckForm() {
	document.form2.type.value="titleimage";
	document.form2.submit();
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>타이틀 이미지 관리</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">

	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8">
            </td></tr>
			<tr>
				<td>
                    <!-- 페이지 타이틀 -->
					<div class="title_depth3">타이틀 이미지 관리</div>
                </td>
            </tr>
            <tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 각종 타이틀 이미지 및 메인페이지 오른쪽 메뉴 배경색을 지정하실 수 있습니다.</span></div>
				</td>
			</tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">
						메인 오른쪽 메뉴 배경색
					</div>
				</td>
			</tr>
			<tr>
				<td>
						<div class="help_info01_wrap">
							<ul>
								<li>1) 메인 오른쪽 메뉴들의 배경색을 변경합니다.</li>
								<li>2) 배경색 변경 적용 후 <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');"><span class="font_blue">디자인관리 > 웹FTP 및  개별적용 선택 > 개별디자인 적용선택</span></a> 을 해야 적용됩니다.<br>
					<b>&nbsp;&nbsp;&nbsp;</b>메인본문 적용 + 전체페이지 왼쪽메뉴 출력<br>
					<b>&nbsp;&nbsp;&nbsp;</b>메인본문 적용+전체페이지 왼쪽메뉴 미출력<br>
					<b>&nbsp;&nbsp;&nbsp;</b>단, 메인 본문을 개별디자인 한 경우 오른쪽 메뉴는 출력되지 않습니다</li>
							</ul>
						</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<input type=hidden name=type value="color">
				<tr>
					<td width="50%">
					<TABLE cellSpacing=0 cellPadding=0 border=0 width="243" align="center">
					<TR>
						<TD>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD></TD>
							<TD width=10>
							<table cellpadding="1" cellspacing="0" width="100%">
							<tr>
								<td width="5"><input type=text name="Rcolor" value="<?=$Rcolor?>" size=7 maxlength=2 class="input"></td>
								<td width="5"><input type=text name="Gcolor" value="<?=$Gcolor?>" size=7 maxlength=2 class="input"></td>
								<td width="5"><input type=text name="Bcolor" value="<?=$Bcolor?>" size=7 maxlength=2 class="input"></td>
							</tr>
							</table>
							</TD>
							<TD>
							<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
							<TR>
								<TD id="rgb_preview" width="80" bgcolor=<?=$main_title_color?> height=20></TD>
							</TR>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</TD>
					</TR>
					<TR>
					<TD>
						<TABLE cellSpacing="1" cellPadding=0 border=0 width="100%">
						<TR>
							<TD style="CURSOR: hand" bgColor=#ffffff><A href="javascript:color_view('FFFFFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ffccff><A href="javascript:color_view('FFCCFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff99ff><A href="javascript:color_view('FF99FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff66ff><A href="javascript:color_view('FF66FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff33ff><A href="javascript:color_view('FF33FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff00ff><A href="javascript:color_view('FF00FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ffff><A href="javascript:color_view('66FFFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ccff><A href="javascript:color_view('66CCFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6699ff><A href="javascript:color_view('6699FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6666ff><A href="javascript:color_view('6666FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6633ff><A href="javascript:color_view('6633FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6600ff><A href="javascript:color_view('6600FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#eeeeee><A href="javascript:color_view('EEEEEE')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ffffcc><A href="javascript:color_view('FFFFCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ffcccc><A href="javascript:color_view('FFCCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff99cc><A href="javascript:color_view('FF99CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff66cc><A href="javascript:color_view('FF66CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff33cc><A href="javascript:color_view('FF33CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff00cc><A href="javascript:color_view('FF00CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ffcc><A href="javascript:color_view('66FFCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66cccc><A href="javascript:color_view('66CCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6699cc><A href="javascript:color_view('6699CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6666cc><A href="javascript:color_view('6666CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6633cc><A href="javascript:color_view('6633CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#6600cc><A href="javascript:color_view('6600CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#dddddd><A href="javascript:color_view('DDDDDD')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ffff99><A href="javascript:color_view('FFFF99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ffcc99><A href="javascript:color_view('FFCC99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff9999><A href="javascript:color_view('FF9999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff6699><A href="javascript:color_view('FF6699')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff3399><A href="javascript:color_view('FF3399')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff0099><A href="javascript:color_view('FF0099')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ff99><A href="javascript:color_view('66FF99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66cc99><A href="javascript:color_view('66CC99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#669999><A href="javascript:color_view('669999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#666699><A href="javascript:color_view('666699')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#663399><A href="javascript:color_view('663399')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#660099><A href="javascript:color_view('660099')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cccccc><A href="javascript:color_view('CCCCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ffff66><A href="javascript:color_view('FFFF66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ffcc66><A href="javascript:color_view('FFCC66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff9966><A href="javascript:color_view('FF9966')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff6666><A href="javascript:color_view('FF6666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff3366><A href="javascript:color_view('FF3366')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff0066><A href="javascript:color_view('FF0066')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ff66><A href="javascript:color_view('66FF66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66cc66><A href="javascript:color_view('66CC66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#669966><A href="javascript:color_view('669966')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#666666><A href="javascript:color_view('666666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#663366><A href="javascript:color_view('663366')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#660066><A href="javascript:color_view('660066')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#bbbbbb><A href="javascript:color_view('BBBBBB')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ffff33><A href="javascript:color_view('FFFF33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ffcc33><A href="javascript:color_view('FFCC33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff9933><A href="javascript:color_view('FF9933')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff6633><A href="javascript:color_view('FF6633')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff3333><A href="javascript:color_view('FF3333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff0033><A href="javascript:color_view('FF0033')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ff33><A href="javascript:color_view('66FF33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66cc33><A href="javascript:color_view('66CC33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#669933><A href="javascript:color_view('669933')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#666633><A href="javascript:color_view('666633')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#663333><A href="javascript:color_view('663333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#660033><A href="javascript:color_view('660033')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#aaaaaa><A href="javascript:color_view('AAAAAA')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ffff00><A href="javascript:color_view('FFFF00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ffcc00><A href="javascript:color_view('FFCC00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff9900><A href="javascript:color_view('FF9900')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff6600><A href="javascript:color_view('FF6600')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff3300><A href="javascript:color_view('FF3300')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff0000><A href="javascript:color_view('FF0000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66ff00><A href="javascript:color_view('66FF00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#66cc00><A href="javascript:color_view('66CC00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#669900><A href="javascript:color_view('669900')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#666600><A href="javascript:color_view('666600')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#663300><A href="javascript:color_view('663300')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#660000><A href="javascript:color_view('660000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#999999><A href="javascript:color_view('999999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ccffff><A href="javascript:color_view('CCFFFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ccccff><A href="javascript:color_view('CCCCFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc99ff><A href="javascript:color_view('CC99FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc66ff><A href="javascript:color_view('CC66FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc33ff><A href="javascript:color_view('CC33FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc00ff><A href="javascript:color_view('CC00FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ffff><A href="javascript:color_view('33FFFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ccff><A href="javascript:color_view('33CCFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3399ff><A href="javascript:color_view('3399FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3366ff><A href="javascript:color_view('3366FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3333ff><A href="javascript:color_view('3333FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3300ff><A href="javascript:color_view('3300FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#888888><A href="javascript:color_view('888888')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ccffcc><A href="javascript:color_view('CCFFCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cccccc><A href="javascript:color_view('CCCCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc99cc><A href="javascript:color_view('CC99CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc66cc><A href="javascript:color_view('CC66CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc33cc><A href="javascript:color_view('CC33CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc00cc><A href="javascript:color_view('CC00CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ffcc><A href="javascript:color_view('33FFCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33cccc><A href="javascript:color_view('33CCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3399cc><A href="javascript:color_view('3399CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3366cc><A href="javascript:color_view('3366CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#333ccc><A href="javascript:color_view('333CCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#3300cc><A href="javascript:color_view('3300CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#777777><A href="javascript:color_view('777777')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ccff99><A href="javascript:color_view('CCFF99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ccc999><A href="javascript:color_view('CCC999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc9999><A href="javascript:color_view('CC9999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc6699><A href="javascript:color_view('CC6699')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc3399><A href="javascript:color_view('CC3399')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc0099><A href="javascript:color_view('CC0099')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ff99><A href="javascript:color_view('33FF99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33cc99><A href="javascript:color_view('33CC99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#339999><A href="javascript:color_view('339999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#336699><A href="javascript:color_view('336699')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#333399><A href="javascript:color_view('333399')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#330099><A href="javascript:color_view('330099')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#666666><A href="javascript:color_view('666666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ccff66><A href="javascript:color_view('CCFF66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cccc66><A href="javascript:color_view('CCCC66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc9966><A href="javascript:color_view('CC9966')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc6666><A href="javascript:color_view('CC6666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc3366><A href="javascript:color_view('CC3366')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc0066><A href="javascript:color_view('CC0066')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ff66><A href="javascript:color_view('33FF66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33cc66><A href="javascript:color_view('33CC66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#339966><A href="javascript:color_view('339966')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#336666><A href="javascript:color_view('336666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#333366><A href="javascript:color_view('333366')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#330066><A href="javascript:color_view('330066')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#555555><A href="javascript:color_view('555555')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ccff33><A href="javascript:color_view('CCFF33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cccc33><A href="javascript:color_view('CCCC33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc9933><A href="javascript:color_view('CC9933')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc6633><A href="javascript:color_view('CC6633')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc3333><A href="javascript:color_view('CC3333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc0033><A href="javascript:color_view('CC0033')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ff33><A href="javascript:color_view('33FF33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33cc33><A href="javascript:color_view('33CC33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#339933><A href="javascript:color_view('339933')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#336633><A href="javascript:color_view('336633')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#333333><A href="javascript:color_view('333333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#330033><A href="javascript:color_view('330033')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#444444><A href="javascript:color_view('444444')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#ccff00><A href="javascript:color_view('CCFF00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cccc00><A href="javascript:color_view('CCCC00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc9900><A href="javascript:color_view('CC9900')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc6600><A href="javascript:color_view('CC6600')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc3300><A href="javascript:color_view('CC3300')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#cc0303><A href="javascript:color_view('CC0303')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33ff00><A href="javascript:color_view('33FF00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#33cc00><A href="javascript:color_view('33CC00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#339900><A href="javascript:color_view('339900')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#336600><A href="javascript:color_view('336600')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#333300><A href="javascript:color_view('333300')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#330000><A href="javascript:color_view('330000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#333333><A href="javascript:color_view('333333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#99ffff><A href="javascript:color_view('99FFFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#99ccff><A href="javascript:color_view('99CCFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9999ff><A href="javascript:color_view('9999FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9966ff><A href="javascript:color_view('9966FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9933ff><A href="javascript:color_view('9933FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9900ff><A href="javascript:color_view('9900FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ffff><A href="javascript:color_view('00FFFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ccff><A href="javascript:color_view('00CCFF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0099ff><A href="javascript:color_view('0099FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0066ff><A href="javascript:color_view('0066FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0033ff><A href="javascript:color_view('0033FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0000ff><A href="javascript:color_view('0000FF')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#222222><A href="javascript:color_view('222222')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#99ffcc><A href="javascript:color_view('99FFCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#99cccc><A href="javascript:color_view('99CCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9999cc><A href="javascript:color_view('9999CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9966cc><A href="javascript:color_view('9966CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9933cc><A href="javascript:color_view('9933CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#9900cc><A href="javascript:color_view('9900CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ffcc><A href="javascript:color_view('00FFCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00cccc><A href="javascript:color_view('00CCCC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0099cc><A href="javascript:color_view('0099CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0066cc><A href="javascript:color_view('0066CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0033cc><A href="javascript:color_view('0033CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#0000cc><A href="javascript:color_view('0000CC')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#111111><A href="javascript:color_view('111111')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#99ff99><A href="javascript:color_view('99FF99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#99cc99><A href="javascript:color_view('99CC99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#999999><A href="javascript:color_view('999999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#996699><A href="javascript:color_view('996699')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#993399><A href="javascript:color_view('993399')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#990099><A href="javascript:color_view('990099')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ff99><A href="javascript:color_view('00FF99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00cc99><A href="javascript:color_view('00CC99')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#009999><A href="javascript:color_view('009999')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#006699><A href="javascript:color_view('006699')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#003399><A href="javascript:color_view('003399')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#000099><A href="javascript:color_view('000099')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#000000><A href="javascript:color_view('000000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#99ff66><A href="javascript:color_view('99FF66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#99cc66><A href="javascript:color_view('99CC66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#999966><A href="javascript:color_view('999966')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#996666><A href="javascript:color_view('996666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#993366><A href="javascript:color_view('993366')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#990066><A href="javascript:color_view('990066')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ff66><A href="javascript:color_view('00FF66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00cc66><A href="javascript:color_view('00CC66')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#009966><A href="javascript:color_view('009966')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#006666><A href="javascript:color_view('006666')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#003366><A href="javascript:color_view('003366')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#000066><A href="javascript:color_view('000066')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ff0000><A href="javascript:color_view('FF0000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#99ff33><A href="javascript:color_view('99FF33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#99cc33><A href="javascript:color_view('99CC33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#999933><A href="javascript:color_view('999933')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#996633><A href="javascript:color_view('996633')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#993333><A href="javascript:color_view('993333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#990033><A href="javascript:color_view('990033')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ff33><A href="javascript:color_view('00FF33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00cc33><A href="javascript:color_view('00CC33')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#009933><A href="javascript:color_view('009933')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#006633><A href="javascript:color_view('006633')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#003333><A href="javascript:color_view('003333')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#000033><A href="javascript:color_view('000033')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#ee0000><A href="javascript:color_view('EE0000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						<TR>
							<TD style="CURSOR: hand" bgColor=#99ff00><A href="javascript:color_view('99FF00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#99cc00><A href="javascript:color_view('99CC00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#999900><A href="javascript:color_view('999900')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#996600><A href="javascript:color_view('996600')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#993300><A href="javascript:color_view('993300')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#990000><A href="javascript:color_view('990000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00ff00><A href="javascript:color_view('00FF00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#00cc00><A href="javascript:color_view('00CC00')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#009900><A href="javascript:color_view('009900')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#006600><A href="javascript:color_view('006600')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#003300><A href="javascript:color_view('003300')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#000002><A href="javascript:color_view('000002')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
							<TD style="CURSOR: hand" bgColor=#dd0000><A href="javascript:color_view('DD0000')"><IMG height=7 width=15 border=0 src="images/space03.gif"></A></TD>
						</TR>
						</TABLE>
						</TD>
					</TR>
					</TABLE>
					</td>
					<td width="100%" align=center><IMG SRC="images/design_eachtitle_img.gif" border="0" vspace="10"><br><span class=font_orange align="center" >* RGB값을 직접 입력하시거나 색상표에서 색을 선택하여 지정하실 수 있습니다.</td>
				</tr>
                </form>
				</table>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td align="center"><a href="javascript:document.form1.submit();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
                <!-- 소제목 -->
					<div class="title_depth3_sub">
						타이틀 이미지 관리
					</div>
				</td>
			</tr>
			<tr><td>
            			<div class="help_info01_wrap">
							<ul>
								<li>1) 한번에 올릴 수 있는 용량 150KB 으로 제한됨 - 용량이 넘을 경우 한개씩 등록해주세요.</li>
								<li>2) GIF(gif) 파일만 가능, 이미지명은 쇼핑몰에 맞게 자동 변경됩니다.</li>
                                <li>3) 타이틀 등록 후 <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');"><span class="font_blue">디자인관리 > 웹FTP 및 디자인 옵션 설정 > 개별디자인 적용선택</a></span> 을 해야 적용됩니다.<br>
					<b>&nbsp;&nbsp;&nbsp;</b>각종 타이틀 개별디자인 적용 <b>(전체 타이틀 중 변경한 이미지만 변경됨)</b></li>
							</ul>
						</span>
            </td></tr>
            <tr><td height="5"></td></tr>
			<tr>
				<td>
				<div class="table_style01">
				<form name="form2" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=150></col>
				<col></col>
				
				<input type=hidden name=type>
				<?php
						for($i=0;$i<count($timg_name);$i++) {
							echo "<tr>\n";
							echo "	<th><span>{$timg_name[$i]}</span></th>\n";
							echo "	<TD class=\"td_con1\">\n";
							echo "	<div class=\"table_none\">\n";
							echo "	<TABLE cellSpacing=\"0\" cellPadding=\"0\" width=\"100%\" border=\"1\">\n";
							echo "	<tr>\n";
							echo "		<td class=\"td_con1\">
							
							<input type=file name=image{$i} >
							</td>\n";
							echo "		<td>\n";
							if(file_exists($filepath.$itmg_val[$i])) {
								echo $itmg_val[$i]."	<INPUT style=\"CURSOR: hand;width:90px;margin-left:2px;\" onclick=\"delete_title({$i});\" type=\"button\" value=\"삭제하기\" class=\"submit1\">";
							}
							echo "		</td>\n";
							echo "	</tr>\n";
							echo "	</table>\n";
							echo "	</div>\n";
							echo "	</td>\n";
							echo "</tr>\n";
						}
				?>
                
				</TABLE>
				</form>
				</div>
				</td>
			</tr>
			<tr><td height=20 align="center"><a href="#1" onclick="CheckForm()"><img src="images/botteon_save.gif" border="0"></a></td></tr>
			<tr><td height=20 align="center"></td></tr>
			<tr>
				<td>
                <!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
                        <dt>타이틀 변경</span></dt>
                        <dd>- 변경한 타이틀 이미지만 변경되고 나머지 이미지는 사용하던 템플릿의 타이틀 이미지로 계속 유지됩니다.</dd>
                        </dl>
                    </div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<form name="form3" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type=hidden name=type>
<input type=hidden name=idx>
</form>
</table>
<?=$onload?>
<?php 
include("copyright.php");