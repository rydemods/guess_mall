<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$preview=$_REQUEST["preview"];

if($preview=="OK") {
?>
<html>
<head>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<? include($Dir."lib/style.php") ?>
</head>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
<?php
} else {
	if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	if ($_data->frame_type!="N") include($Dir.MainDir.$_data->onetop_type.".php");
	else if($_data->align_type=="Y") echo "<center>";
}

$imagepath=$Dir.DataDir."shopimages/etc/";

$sql = "SELECT * FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);
if($row->left_set=="Y") {
	$xsize=$row->left_xsize;
	$imgtype=$row->left_image;

	$arr_menu=array();

	//[0] => 로그인 관련
	$arr_menu[0] = "<table border=0 cellpadding=0 cellspacing=0 width=100%";
	if(file_exists($imagepath."easyloginbg.gif")) {
		$arr_menu[0].= " background=\"".$imagepath."easyloginbg.gif\"";
	}
	$arr_menu[0].= ">\n";
	if(file_exists($imagepath."easylogintitle.gif")) {
		$arr_menu[0].= "<tr><td><img src=\"".$imagepath."easylogintitle.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	if (strlen($_ShopInfo->getMemid())>0) {
		if ($_ShopInfo->getMemreserve()>0) {
			$reserve_message= "현재적립금 : ".number_format($_ShopInfo->getMemreserve())."원";
		}
		$arr_menu[0].= "<tr><td align=center><font color=orange><b>".$_ShopInfo->getMemname()."</b></font>님 환영합니다.<br>".$reserve_message."</td></tr>\n";
		$arr_menu[0].= "<tr><td height=5></td></tr>\n";
		$arr_menu[0].= "<tr>\n";
		if(file_exists($imagepath."easylogoutbutton.gif")) {
			$arr_menu[0].= "<td align=center><A HREF=\"javascript:logout()\"><img src=\"".$imagepath."easylogoutbutton.gif\" border=0 align=absmiddle></A></td>";
		} else {
			$arr_menu[0].= "<td align=center><input type=button value=\"로그아웃\" onclick=\"logout()\"></td>";
		}
		$arr_menu[0].= "</tr>\n";
	} else {
		$arr_menu[0].= "<tr>\n";
		$arr_menu[0].= "	<td>\n";
		$arr_menu[0].= "	<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
		$arr_menu[0].="		<form name=leftloginform method=post action=".$Dir.MainDir."main.php>\n";
		$arr_menu[0].="		<input type=hidden name=type value=login>\n";
		$arr_menu[0].= "	<tr>\n";
		$arr_menu[0].= "		<td align=center>\n";
		if(file_exists($imagepath."easyidimage.gif")) {
			$arr_menu[0].= "		<img src=\"".$imagepath."easyidimage.gif\" border=0 align=absmiddle>";
		} else {
			$arr_menu[0].= "		<font style=\"font-size:9pt\">I&nbsp;&nbsp;D</font>";
		}
		$arr_menu[0].= "		</td>\n";
		$arr_menu[0].= "		<td width=1 rowspan=2></td>\n";
		$arr_menu[0].= "		<td><input type=text name=id maxlength=20 style=\"width:80\"></td>\n";
		$arr_menu[0].= "		<td align=center rowspan=2>";
		if(file_exists($imagepath."easyloginbutton.gif")) {
			$arr_menu[0].= "		<a href=\"javascript:left_login_check()\"><img src=\"".$imagepath."easyloginbutton.gif\" border=0 align=absmiddle></a>";
		} else {
			$arr_menu[0].= "		<input type=button value=\"로그인\" onclick=\"left_login_check()\">";
		}
		$arr_menu[0].= "		</td>";
		$arr_menu[0].= "	</tr>";
		$arr_menu[0].= "	<tr>";
		$arr_menu[0].= "		<td align=center>\n";
		if(file_exists($imagepath."easypwimage.gif")) {
			$arr_menu[0].= "		<img src=\"".$imagepath."easypwimage.gif\" border=0 align=absmiddle>";
		} else {
			$arr_menu[0].= "		<font style=\"font-size:9pt\">PW</font>";
		}
		$arr_menu[0].= "		</td>\n";
		$arr_menu[0].= "		<td><input type=password name=passwd maxlength=20 style=\"width:80\" onkeydown=\"LeftCheckKeyLogin()\"></td>\n";
		$arr_menu[0].= "	</tr>\n";
		$arr_menu[0].= "	</form>\n";
		$arr_menu[0].= "	</table>\n";
		$arr_menu[0].= "	</td>\n";
		$arr_menu[0].= "</tr>\n";
	}
	if(file_exists($imagepath."easyloginbottom.gif")) {
		$arr_menu[0].= "<tr><td><img src=\"".$imagepath."easyloginbottom.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$arr_menu[0].= "</table>\n";

	//[1] => 상품검색 관련
	$arr_menu[1] = "<table border=0 cellpadding=0 cellspacing=0 width=100%";
	if(file_exists($imagepath."easysearchbg.gif")) {
		$arr_menu[1].= " background=\"".$imagepath."easysearchbg.gif\"";
	}
	$arr_menu[1].= ">\n";
	if(file_exists($imagepath."easysearchtitle.gif")) {
		$arr_menu[1].= "<tr><td colspan=2><img src=\"".$imagepath."easysearchtitle.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$arr_menu[1].= "<form name=search_lform method=get action=\"".$Dir.FrontDir."productsearch.php\">\n";
	$arr_menu[1].= "<tr>\n";
	$arr_menu[1].= "	<td align=center><input type=text name=search value=\"\" onkeydown=\"CheckKeyLeftSearch()\" style=\"width:80\"></td>\n";
	if(file_exists($imagepath."easysearchbutton.gif")) {
		$arr_menu[1].= "	<td align=center><A HREF=\"javascript:LeftSearchCheck()\"><img src=\"".$imagepath."easysearchbutton.gif\" border=0 align=absmiddle></A></td>\n";
	} else {
		$arr_menu[1].= "	<td align=center><input type=button value=\"검 색\" onclick=\"LeftSearchCheck()\"></td>\n";
	}
	$arr_menu[1].= "</tr>\n";
	if(file_exists($imagepath."easysearchbottom.gif")) {
		$arr_menu[1].= "<tr><td colspan=2><img src=\"".$imagepath."easysearchbottom.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$arr_menu[1].= "</form>\n";
	$arr_menu[1].= "</table>\n";

	//[2] => 상품 대분류 관련
	$arr_menu[2] = "<table border=0 cellpadding=0 cellspacing=0 width=100%";
	if(file_exists($imagepath."easyproductbg.gif")) {
		$arr_menu[2].= " background=\"".$imagepath."easyproductbg.gif\"";
	}
	$arr_menu[2].= ">\n";
	if(file_exists($imagepath."easyproducttitle.gif")) {
		$arr_menu[2].= "<tr><td><img src=\"".$imagepath."easyproducttitle.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$sql = "SELECT code_a as code, type, code_name FROM tblproductcode ";
	$sql.= "WHERE group_code!='NO' AND (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		if(file_exists($imagepath."easy".$row->code.".gif")) {
			$arr_menu[2].= "<tr><td><A HREF=\"".$Dir.FrontDir."productlist.php?code=".$row->code."\"><img src=\"".$imagepath."easy".$row->code.".gif\" border=0 align=absmiddle></a></td></tr>\n";
		} else {
			$arr_menu[2].= "<tr><td height=22 style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."productlist.php?code=".$row->code."\"><FONT class=\"leftprname\">".$row->code_name."</FONT></A></td></tr>\n";
		}
	}
	pmysql_free_result($result);
	if($_data->estimate_ok=="Y" || $_data->estimate_ok=="O") {
		if(file_exists($imagepath."easyestimate.gif")) {
			$arr_menu[2].= "<tr><td><A HREF=\"javascript:estimate('".$_data->estimate_ok."')\"><img src=\"".$imagepath."easyestimate.gif\" border=0 align=absmiddle></A></td></tr>\n";
		} else {
			$arr_menu[2].= "<tr><td height=22 style=\"padding-left:5\"><A HREF=\"javascript:estimate('".$_data->estimate_ok."')\"><FONT class=\"leftprname\">온라인 견적서</font></A></td></tr>\n";
		}
	}
	if(file_exists($imagepath."easyproductbottom.gif")) {
		$arr_menu[2].= "<tr><td><img src=\"".$imagepath."easyproductbottom.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$arr_menu[2].= "</table>\n";

	//[3] => 게시판 관련
	$arr_menu[3] = "<table border=0 cellpadding=0 cellspacing=0 width=100%";
	if(file_exists($imagepath."easyboardbg.gif")) {
		$arr_menu[3].= " background=\"".$imagepath."easyboardbg.gif\"";
	}
	$arr_menu[3].= ">\n";
	if(file_exists($imagepath."easyboardtitle.gif")) {
		$arr_menu[3].= "<tr><td><img src=\"".$imagepath."easyboardtitle.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$sql = "SELECT board,board_name,use_hidden FROM tblboardadmin ";
	$sql.= "ORDER BY date DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		if($row->use_hidden!="Y") {
			if(file_exists($imagepath."easy".$row->board.".gif")) {
				$arr_menu[3].= "<tr><td><A HREF=\"".$Dir.BoardDir."board.php?board=".$row->board."\"><img src=\"".$imagepath."easy".$row->board.".gif\" border=0 align=absmiddle></A></td></tr>\n";
			} else {
				$arr_menu[3].= "<tr><td height=22 style=\"padding-left:5\"><A HREF=\"".$Dir.BoardDir."board.php?board=".$row->board."\"><FONT class=\"leftcommunity\">".$row->board_name."</FONT></A></td></tr>\n";
			}
		}
	}
	pmysql_free_result($result);
	if ($_data->ETCTYPE["REVIEW"]=="Y") {
		if(file_exists($imagepath."easyreviewall.gif")) {
			$arr_menu[3].= "<tr><td><A HREF=\"".$Dir.FrontDir."reviewall.php\"><img src=\"".$imagepath."easyreviewall.gif\" border=0 align=absmiddle></A></td></tr>\n";
		} else {
			$arr_menu[3].= "<tr><td height=22 style=\"padding-left:5\"><A HREF=\"".$Dir.FrontDir."reviewall.php\"><FONT class=\"leftcommunity\">사용후기 모음</FONT></A></td></tr>\n";
		}
	}
	if(file_exists($imagepath."easyboardbottom.gif")) {
		$arr_menu[3].= "<tr><td colspan=2><img src=\"".$imagepath."easyboardbottom.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$arr_menu[3].= "</table>\n";

	//[4] => 고객상담 관련
	$arr_menu[4] = "<table border=0 cellpadding=0 cellspacing=0 width=100%";
	if(file_exists($imagepath."easycustomerbg.gif")) {
		$arr_menu[4].= " background=\"".$imagepath."easycustomerbg.gif\"";
	}
	$arr_menu[4].= ">\n";
	if(file_exists($imagepath."easycustomertitle.gif")) {
		$arr_menu[4].= "<tr><td><img src=\"".$imagepath."easycustomertitle.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	//고객상담 내용 (전화번호, E-mail문의)
	if(strlen($_data->info_tel)>0) {
		$tmp_tel=explode(",",$_data->info_tel);
		for($i=0;$i<count($tmp_tel);$i++) {
			$arr_menu[4].= "<tr>\n";
			$arr_menu[4].= "	<td class=\"leftcustomer\" style=\"padding-left:15;padding-right:15;\"><img src=\"".$Dir."images/".$_data->icon_type."/telicon.gif\" border=0 align=absmiddle> ".$tmp_tel[$i]."</td>\n";
			$arr_menu[4].= "</tr>\n";
			if($i==2) break;
		}
	}
	$arr_menu[4].= "<tr height=20>\n";
	$arr_menu[4].= "	<td class=\"leftcustomer\" style=\"padding-left:15;padding-right:15\"><img src=\"".$Dir."images/".$_data->icon_type."/mailicon.gif\" border=0 align=absmiddle> <A HREF=\"javascript:sendmail();\">E-mail 문의</A></td>\n";
	$arr_menu[4].= "</tr>\n";
	if(file_exists($imagepath."easycustomerbottom.gif")) {
		$arr_menu[4].= "<tr><td colspan=2><img src=\"".$imagepath."easycustomerbottom.gif\" border=0 align=absmiddle></td></tr>\n";
	}
	$arr_menu[4].= "</table>\n";


	//[5] => 배너관련
	$arr_menu[5] = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	include($Dir."lib/banner.php");
	if(strlen($bannerbody)>0) {
		$arr_menu[5].= "<tr><td align=center style=\"padding-top:10\">".$bannerbody."</td></tr>";
	}
	$arr_menu[5].= "</table>\n";

	//[6] => 자유디자인 관련
	$arr_menu[6] = "<table border=0 cellpadding=0 cellspacing=0 width=100%>\n";
	include($Dir."lib/leftevent.php");
	if(strlen($eventbody)>0) {
		$arr_menu[6].= "<tr><td align=center style=\"padding-top:10\">".$eventbody."</td></tr>";
	}
	$arr_menu[6].= "</table>\n";
}
?>

<SCRIPT LANGUAGE="JavaScript">
<!--
var quickview_path="<?=$Dir.FrontDir?>product.quickview.xml.php";
var quickfun_path="<?=$Dir.FrontDir?>product.quickfun.xml.php";
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

<?if($_data->layoutdata["MOUSEKEY"][3]=="Y"){?>
function funkeyclick() {
    if (navigator.appName=="Netscape" && (e.which==3 || e.which==2)) return;
    else if (navigator.appName=="Microsoft Internet Explorer" && (event.button==2 || event.button==3 || event.keyCode==93)) return;

    if(navigator.appName=="Microsoft Internet Explorer" && (event.ctrlKey && event.keyCode==78)) return false;
}
document.onmousedown=funkeyclick;
document.onkeydown=funkeyclick;
<?}?>
//-->
</SCRIPT>
<?php
if($_data->layoutdata["SHOPBGTYPE"][0]=="B") {			//배경색 설정
	echo "<style>\n";
	if($_data->layoutdata["SHOPBGTYPE"][1]=="Y") {
		echo "#tableposition { background-color: transparent; }\n";
	} else {
		echo "#tableposition { background-color: #FFFFFF; }\n";
	}
	if($_data->layoutdata["BGCOLOR"][0]=="N") {
		echo "BODY {background-color: ".(strlen(substr($_data->layoutdata["BGCOLOR"],1,7))==7?substr($_data->layoutdata["BGCOLOR"],1,7):"#FFFFFF")."}\n";
	} else {
		echo "BODY {background-color: transparent}\n";
	}
	echo "</style>\n";
} else if($_data->layoutdata["SHOPBGTYPE"][0]=="I") {	//백그라운드 설정
	echo "<style>\n";
	if($_data->layoutdata["SHOPBGTYPE"][1]=="N") {
		echo "#tableposition { background-color: #FFFFFF; }\n";
	} else {
		echo "#tableposition { background-color: transparent; }\n";
	}
	if(file_exists($Dir.DataDir."shopimages/etc/background.gif")) {
		echo "BODY {\n";
		echo "background-image: url('".$Dir.DataDir."shopimages/etc/background.gif');\n";
		$background_repeat=array("A"=>"repeat","B"=>"repeat-x","C"=>"repeat-y","D"=>"no-repeat");
		echo "background-repeat: ".$background_repeat[$_data->layoutdata["BACKGROUND"][2]].";\n";
		$background_position=array("A"=>"top left","B"=>"top center","C"=>"top right","D"=>"center left","E"=>"center center","F"=>"center right","G"=>"bottom left","H"=>"bottom center","I"=>"bottom right");
		echo "background-position: ".$background_position[$_data->layoutdata["BACKGROUND"][1]].";\n";
		if($_data->layoutdata["BACKGROUND"][0]=="Y") {
			echo "background-attachment: fixed;\n";
		}
	}
	echo "</style>\n";
}
?>

<table border=0 width="<?=($_data->layoutdata["SHOPWIDTH"]>0?$_data->layoutdata["SHOPWIDTH"]:($xsize+700))?>" cellpadding=0 cellspacing=0 id="tableposition">
<tr>
	<td width=100% valign=top nowrap>
	<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>
	<col width="<?=$xsize?>"></col>
	<col width=""></col>
	<tr>
		<td valign=top <?if(file_exists($imagepath."easymenubg.gif")) echo "background=\"".$imagepath."easymenubg.gif\"";?>>

		<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?php
		$leftbody="";
		$ar_imgtype=explode(",",$imgtype);
		$ar_cnt = count($ar_imgtype);
		for($i=0;$i<$ar_cnt;$i++){
			$leftbody.="		<tr><td>".$arr_menu[$ar_imgtype[$i]]."</td></tr>\n";
		}
		echo $leftbody;
?>
		<tr><td align=center><?=$_data->countpath?></td></tr>
		<tr><td height=10></td></tr>
		</table>

		</td>
		<td width=700 align=center valign=top nowrap>
<?php
if($preview=="OK") {
	echo "</td></tr></table>\n";
	echo "</body>\n";
	echo "</html>\n";
}
