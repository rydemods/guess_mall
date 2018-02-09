<?php 
session_start();

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$mem_type = $_GET[mem_type];
if(strlen($_ShopInfo->getMemid())>0) {
	header("Location:../index.php");
	exit;
}



$CertificationData = pmysql_fetch_object(pmysql_query("select realname_id, realname_password, realname_check, realname_adult_check, ipin_id, ipin_password, ipin_check, ipin_adult_check from tblshopinfo"));
if($CertificationData->realname_check || $CertificationData->ipin_check){
	if(!$_SESSION[ipin][name]) {
		echo "<script> alert('회원가입을 위해 본인 인증이 필요합니다.'); location.href='member_jointype.php'</script>";
		exit;
	}
	if($_SESSION[ipin][dupinfo]){
		$check_ipin=pmysql_fetch_object(pmysql_query("select count(id) as check_id from tblmember where dupinfo='{$_SESSION[ipin][dupinfo]}'"));
		if($check_ipin->check_id){
			echo "<script>alert('이미 가입된 회원입니다.'); location.href='member_jointype.php'</script>";
			exit;
		}	
	}
}



$leftmenu="Y";
$sql="SELECT body,leftmenu FROM tbldesignnewpage WHERE type='joinagree'";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$body=$row->body;
	$body=str_replace("[DIR]",$Dir,$body);
	$leftmenu=$row->leftmenu;
	$newdesign="Y";
}

pmysql_free_result($result);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 회원가입</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
<link rel="stylesheet" type="text/css" href="/css/style_eco.css" />
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(t) {	
	if(!document.form1.agree || document.form1.agree.checked==false) {
		alert("회원약관에 동의하셔야 회원가입이 가능합니다.");
		if(document.form1.agree) {
			document.form1.agree.focus();
		}
		return;
	} else if(!document.form1.agreep || document.form1.agreep.checked==false) {
		alert("개인보호취급방침에 동의하셔야 회원가입이 가능합니다.");
		if(document.form1.agreep) {
			document.form1.agreep.focus();
		}
		return;
	} else {
		
		document.form1.action="member_join.php?mem_type="+t;
		document.form1.submit();
		/*
		if(t==0){
			document.form1.action="member_join.php";
			document.form1.submit();
		}else if(t==1){
			document.form1.action="member_join_company.php";
			document.form1.submit();
		}
		*/
	}
}
//-->
</SCRIPT>
</HEAD>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>


<form name=form1 action="member_join.php" method=post>
<?php 
if($_data->icon_type=="001" || $_data->icon_type=="002" || $_data->icon_type=="003"){
	echo"<table border=0 cellpadding=0 cellspacing=0 width=100%>";
if ($leftmenu!="N") {
	echo "<tr>\n";
	if ($_data->title_type=="Y" && file_exists($Dir.DataDir."design/memberjoin_title.gif")) {
		echo "<td><img src=\"".$Dir.DataDir."design/memberjoin_title.gif\" border=\"0\" alt=\"회원가입\"></td>\n";
	} else {
		echo "<td>\n";
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0>\n";
		echo "<TR>\n";
		echo "	<TD><IMG SRC={$Dir}images/{$_data->icon_type}/memberjoin_title_head.gif ALT=></TD>\n";
		echo "	<TD width=100% valign=top background={$Dir}images/{$_data->icon_type}/memberjoin_title_bg.gif></TD>\n";
		echo "	<TD width=40><IMG SRC={$Dir}images/{$_data->icon_type}/memberjoin_title_tail.gif ALT=></TD>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "</td>\n";
	}
	echo "</tr>\n";
}
}
$sql="SELECT agreement,privercy FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$agreement=$row->agreement;
$privercy_exp=@explode("=", $row->privercy);
$privercy=$privercy_exp[1];
pmysql_free_result($result);

if(ord($agreement)==0) {
	$buffer = file_get_contents($Dir.AdminDir."agreement.txt");
	$agreement=$buffer;
}

$pattern=array("[SHOP]","[COMPANY]");
$replace=array($_data->shopname, $_data->companyname);
$agreement = str_replace($pattern,$replace,$agreement);

if(ord($privercy)==0) {
	$buffer = file_get_contents($Dir.AdminDir."privercy2.txt");
	$privercy=$buffer;
}

$pattern=array("[SHOP]","[NAME]","[EMAIL]","[TEL]");
$replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:{$_data->privercyemail}\">{$_data->privercyemail}</a>",$_data->info_tel);
$privercy = str_replace($pattern,$replace,$privercy);

if($newdesign=="Y") {	//개별디자인
	$pattern=array("[CONTRACT]","[PRIVERCY]","[CHECK]","[CHECKP]","[OK]","[REJECT]");
	$replace=array($agreement,$privercy,"<input type=checkbox id=\"idx_agree\" name=agree style=\"border:none;\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_agree>","<input type=checkbox id=\"idx_agreep\" name=agreep style=\"border:none;\"> <label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_agreep>","javascript:CheckForm()","javascript:history.go(-1)");
	$body=str_replace($pattern,$replace,$body);
	echo "<tr>\n";
	echo "	<td align=center>{$body}</td>";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "</form>";
	echo "</table>";
}else if ($_data->icon_type=="tem_001"){
?>















<!-- start container -->
<div id="container">
	<!-- start contents -->
	<div class="contents">	
		<div class="title">
			<h2><img src="../image/login/title_login.gif" alt="로그인" /></h2>
			<div class="path">
				<ul>
					<li class="home">홈&nbsp;&gt;&nbsp;</li>
					<li>회원가입 약관동의</li>
				</ul>
			</div>
		</div>
		<div class="joinstep">
			<img src="../image/join/join_step.gif" />
		</div>	
		<div class="benefit">
			<h3><img src="../image/join/benefit_join.jpg" alt="에코팩토리 회원만의 특별한 혜택" /></h3>
		</div>
		<div class="indiv"><!-- Start indiv -->
			<!-- 이용약관 -->
			<div class="join_txt_warp">
			<div class="join_agreement">
				<div class="join_txt_tit">이용약관</div>
				<div class="agreement_txt"><?=$agreement?></div>
				<div class="join_txt_ok">
					<input id="idx_agree" type="checkbox" name="agree" required label="약관 동의" msgR="약관에 동의를 하셔야 합니다"> 이용약관에 동의합니다
				</div>
			</div>
			<div class="join_private">
				<div class="join_txt_tit">개인정보취급방침</div>
				<div class="private_txt"><?=$privercy?></div>
				<div class="join_txt_ok">
					<input id="idx_agreep" type="checkbox" name="agreep" required label="개인정보취급방침 동의" msgR="개인정보취급방침에 동의를 하셔야 합니다"> 개인정보취급방침에 동의합니다</div>
				</div>
			</div>
			<!-- 하단버튼 -->
			<div class="join_bt">
				<a href="javascript:history.go(-1);"><img src="../image/join/bt_join1.gif" border=0></a>
				<a href="javascript:CheckForm('<?=$_GET['mem_type']?>')"><img src="../image/join/bt_join2.gif"></a>
			</div>
		</div><!-- End indiv -->
	</div><!-- //end contents -->
</div><!-- //end container -->













<? 
}else{	
?>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td style="padding-left:5px;padding-right5px">
	<table align="center" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
	<tr>
		<td style="padding:10px;padding-top:0px;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td><img src="<?=$Dir?>images/join_yak_text.gif" border="0"></td>
		</tr>
		<tr>
			<td height="1" bgcolor="#EBEBEB"></td>
		</tr>
		<tr>
			<td><IMG src="<?=$Dir?>images/join_yak_01.gif" border=0></td>
		</tr>
		<tr>
			<td align="center">
			<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td background="<?=$Dir?>images/join_yak_t01.gif"><img src="<?=$Dir?>images/join_yak_t01_left.gif" border="0"></td>
				<td background="<?=$Dir?>images/join_yak_t01.gif"></td>
				<td align="right" background="<?=$Dir?>images/join_yak_t01.gif"><img src="<?=$Dir?>images/join_yak_t01_right.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/join_yak_t02.gif"></td>
				<td>
				<TABLE width="100%" cellSpacing="0" cellPadding="0" border="0" style="TABLE-LAYOUT: fixed">
				<TR>
					<TD style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" bgColor="#ffffff"><DIV style="PADDING:5px;OVERFLOW-Y:auto;OVERFLOW-X:auto;HEIGHT:250px"><?=$agreement?></DIV></TD>
				</TR>
				</TABLE>
				</td>
				<td background="<?=$Dir?>images/join_yak_t04.gif"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/join_yak_t03.gif"><img src="<?=$Dir?>images/join_yak_t03_left.gif" border="0"></td>
				<td background="<?=$Dir?>images/join_yak_t03.gif"></td>
				<td align="right" background="<?=$Dir?>images/join_yak_t03.gif"><img src="<?=$Dir?>images/join_yak_t03_right.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td align="center"><INPUT id="idx_agree" type="checkbox" name="agree" style="border:none;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_agree>위의 회원약관에 동의합니다.</LABEL></td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td><IMG src="<?=$Dir?>images/join_yak_02.gif" border="0"></td>
		</tr>
		<tr>
			<td align="center">
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td background="<?=$Dir?>images/join_yak_t01.gif"><img src="<?=$Dir?>images/join_yak_t01_left.gif" border="0"></td>
				<td background="<?=$Dir?>images/join_yak_t01.gif"></td>
				<td align="right" background="<?=$Dir?>images/join_yak_t01.gif"><img src="<?=$Dir?>images/join_yak_t01_right.gif" border="0"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/join_yak_t02.gif"></td>
				<td>
				<TABLE width="100%" cellSpacing="0" cellPadding="0" border="0" style="TABLE-LAYOUT: fixed">
				<TR>
					<TD style="BORDER-RIGHT: #dfdfdf 1px solid; BORDER-TOP: #dfdfdf 1px solid; BORDER-LEFT: #dfdfdf 1px solid; BORDER-BOTTOM: #dfdfdf 1px solid" bgColor="#ffffff"><DIV style="PADDING-RIGHT: 10px; OVERFLOW-Y: auto; PADDING-LEFT: 10px; OVERFLOW-X: auto; PADDING-BOTTOM: 10px; PADDING-TOP: 10px; HEIGHT: 250px"><?=$privercy?></DIV></TD>
				</TR>
				</TABLE>
				</td>
				<td background="<?=$Dir?>images/join_yak_t04.gif"></td>
			</tr>
			<tr>
				<td background="<?=$Dir?>images/join_yak_t03.gif"><img src="<?=$Dir?>images/join_yak_t03_left.gif" border="0"></td>
				<td background="<?=$Dir?>images/join_yak_t03.gif"></td>
				<td align="right" background="<?=$Dir?>images/join_yak_t03.gif"><img src="<?=$Dir?>images/join_yak_t03_right.gif" border="0"></td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td align="center"><INPUT id="idx_agreep" type="checkbox" name="agreep" style="border:none;"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_agreep>위의 개인정보취급방침에 동의합니다.</LABEL></td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td align="center">
			<A HREF="javascript:CheckForm('<?=$_GET['mem_type']?>')"><img src="<?=$Dir?>images/btn_mjoin.gif" border="0"></a>
			<A HREF="javascript:history.go(-1);"><img src="<?=$Dir?>images/btn_mback.gif" border="0" hspace="5"></a>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height="20"></td>
</tr>
</form>
</table>

<?
}
?>


<?php  include ($Dir."lib/bottom.php") ?>
<iframe name="ifrmHidden" width=1000 height=1000 style="display:none"></iframe>
</BODY>
</HTML>
