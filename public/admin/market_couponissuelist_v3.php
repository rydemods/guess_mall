<?php // hspark
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once("../lib/adminlib.php");
	include("access.php");

	if(ord($_ShopInfo->getId())==0){
		echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
		exit;
	}

	$type					= $_POST["type"];
	$ci_no				= $_POST["ci_no"];
	$coupon_code	= $_POST["coupon_code"];
	$uid					= $_POST["uid"];
	$search				= $_POST["search"];

	if($type=="issuedelete" && ord($ci_no) && ord($coupon_code) && ord($uid)) {	//회원에게 발급한 쿠폰 삭제

		$sql = "DELETE FROM tblcouponissue WHERE ci_no = ".$ci_no." AND coupon_code = '{$coupon_code}' AND id = '{$uid}' ";
		//echo $sql;
		pmysql_query($sql,get_db_conn());

		$sql = "UPDATE tblcouponinfo SET issue_no = issue_no - 1 WHERE coupon_code = '{$coupon_code}'";
		//echo $sql;
		pmysql_query($sql,get_db_conn());

		if(!pmysql_errno()) {	
			echo "<script>alert('{$uid} 회원님에게 발급된 쿠폰이 삭제되었습니다.'); parent.location.reload();parent.opener.location.reload();</script>";
			exit;
		} else {		
			echo "<script>alert('발급된 쿠폰 삭제중 오류가 발생하였습니다.');</script>";
			exit;
		}
	}

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>쿠폰 발급 회원 내역</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 36;
	var oHeight = document.all.table_body.clientHeight + 160;

	window.resizeTo(oWidth,oHeight);
}

function IssueCouponDelete(ci_no, code,uid) {
	if(confirm(uid+" 회원님에게 발급한 쿠폰을 삭제하시겠습니까?")) {
        document.exeform.coupon_code.value=code;
		document.exeform.ci_no.value=ci_no;
		document.exeform.uid.value=uid;
		document.exeform.type.value="issuedelete";
		document.exeform.target="hiddenframe";
		document.exeform.submit();
	}
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function id_search() {
	if(document.form1.search.value.length==0) {
		alert("회원 아이디를 입력하세요.");
		document.form1.search.focus();
		return;
	}
	if(document.form1.search.value.length<=1) {
		alert("검색 키워드는 1자 이상 입력하셔야 합니다.");
		document.form1.search.focus();
		return;
	}
	document.form1.block.value = '';
	document.form1.gotopage.value = '';
	document.form1.submit();
}

function search_default() {
	document.form1.search.value='';
	document.form1.block.value = '';
	document.form1.gotopage.value = '';
	document.form1.submit();
}

//-->
</SCRIPT>
</head>

<div class="pop_top_title"><p>쿠폰 발급 회원 내역</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="700" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<tr>
	<td background="images/member_zipsearch_bg.gif">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="18">&nbsp;</td>
		<td>&nbsp;</td>
		<td width="18">&nbsp;</td>
	</tr>
	<tr>
		<td width="18">&nbsp;</td>
		<td>
		<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
		<input type=hidden name=coupon_code value="<?=$coupon_code?>">
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%" style='padding-bottom:5px'><IMG height=9 src="images/point.gif" border=0>&nbsp;<b style='font-size:13px;color:#508900;'>발급 현황 조회</b></td>
		</tr>
		<tr>
			<td width="100%">
			<table cellpadding="0" cellspacing="0" width="100%">
<?php
			list($coupon_name)=pmysql_fetch_array(pmysql_query("select coupon_name from tblcouponinfo where coupon_code = '{$coupon_code}' "));

			$sql = "SELECT COUNT(*) as cnt, COUNT(CASE WHEN (b.used='Y') THEN 1 ELSE NULL END) as cnt2, 
			COUNT(CASE WHEN b.id like '%{$search}%' THEN 1 ELSE NULL END) as cnt3 
			FROM tblcouponinfo a, tblcouponissue b WHERE a.coupon_code = '{$coupon_code}' 
			AND a.vender=0 AND a.coupon_code=b.coupon_code ";
			$result=pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);
			$totalnum=$row->cnt;
			$usenum=$row->cnt2;
			$t_count = $row->cnt;
			if(ord($search)) $t_count = $row->cnt3;
			$paging = new newPaging((int)$t_count,10,10,'GoPage');
			//echo $sql;
			$gotopage = $paging->gotopage;
?>			
			<tr>
				<td>
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
						<li style='margin-top:8px'>
						<table cellpadding="0" cellspacing="0" width="100%">
						<colgroup>
						<col width=''>
						<col width='120'>
						<col width='120'>
						</colgroup>
						<tr>
							<td><img src="images/icon_cuponname.gif" border="0" align=absmiddle>&nbsp;&nbsp;<B><span class="font_orange"><?=$coupon_name?></span></B></td>
							<td><img src="images/icon_cupon_bal.gif" border="0" align=absmiddle>&nbsp;&nbsp;<B><?=$totalnum?></B>개</td>
							<td><img src="images/icon_cupon_use.gif" border="0" align=absmiddle>&nbsp;&nbsp;<?=$usenum?>개</td>
						</tr>
						</table>
						</li>
					</ul>
				</div>				
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td width="100%">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
				<tr>
					<td width="100%" bgcolor="white"><p align="center">아이디 <INPUT class="input_selected" size=30 name=search value="<?=$search?>" style='padding-top:2pt;padding-bottom:6px'> <a href="javascript:id_search();"><img src="images/btn_cate_search.gif" alt=검색 align=absMiddle border=0></a><A href="javascript:search_default();"><IMG src="images/btn_cate_search_clear.gif" align=absMiddle border=0 hspace="2"></A></p></td>
				</tr>
				</table>
				</td>
			</tr>
		<tr>
			<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=></col>
				<col width=135></col>
				<col width=150></col>
				<col width=100></col>
				<col width=100></col>
				<TR>
					<th>No</th>
					<th>아이디</th>
					<th>발급일</th>
					<th>유효기간</th>
					<th>사용여부</th>
					<th>비고</th>
				</TR>
<?php
				$sql = "SELECT * FROM tblcouponissue WHERE coupon_code = '{$coupon_code}' ";
				if(ord($search)) $sql.= "AND id LIKE '%{$search}%' ";
				$sql.= "ORDER BY date DESC";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				//exdebug($sql);
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$cnt++;

					$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
					$regdate = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
					$used="<span class=\"font_orange\">미사용</span>";
					if($row->used=="Y") $used="<span class=\"font_blue\">사용함</span>";
					echo "<TR align=center>\n";
					echo "	<TD height=33 class=\"td_con2\">{$number}</TD>\n";
					echo "	<TD class=\"td_con1\">{$row->id}</TD>\n";
					echo "	<TD class=\"td_con1\">{$regdate}</TD>\n";
					echo "	<TD class=\"td_con1\">{$date}</TD>\n";
					echo "	<TD class=\"td_con1\">{$used}</TD>\n";
					if($row->used=="Y") {
						echo "	<TD class=\"td_con1\">-</TD>\n";
					} else {
						echo "	<TD class=\"td_con1\"><a href=\"javascript:IssueCouponDelete('{$row->ci_no}', '{$row->coupon_code}','{$row->id}');\"><img src=\"images/btn_del7.gif\" border=\"0\"></a></TD>\n";
					}
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td class=td_con2 colspan=6 align=center>회원에게 발급된 쿠폰내역이 없습니다.</td></tr>";
				}
?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
						<ul>
							<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</ul>
					</div>
				</div>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
		<td width="18">&nbsp;</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
<form name=exeform action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=coupon_code>
	<input type=hidden name=ci_no>
	<input type=hidden name=uid>
</form>
</TABLE>
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td height=50 align="center" style='border-top:1px solid #cbcbcb;'><a href="javascript:window.close()"><img src="images/btn_close.gif"  border=0></a></td>
</tr>
</table>
<IFRAME name="hiddenframe" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
</body>
</html>