<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");

$type = $_POST["type"];
$id =   $_POST["id"];
$date = $_POST["date"];

if(ord($_ShopInfo->getId())==0 || ord($id)==0){
	echo "<script>window.close();</script>";
	exit;
}

if($type=="delete" && ord($date)){
	$sql = "DELETE FROM tblpoint WHERE mem_id = '{$id}' AND date = '{$date}' ";
	pmysql_query($sql,get_db_conn());

	$log_content = "## 적립금 내역 삭제 ## - 아이디 : $id 금액";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}
$sql = "SELECT reserve, name FROM tblmember WHERE id = '{$id}' ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$name = $row->name; 
	//$reserve = $row->reserve;
}
pmysql_free_result($result);

$erp_mem_reserve	= getErpMeberPoint($id);
$reserve	= $erp_mem_reserve[p_err_code]==0?$erp_mem_reserve[p_data]:'0';
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>적립금 내역</title>
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
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}

function ReserveDelete(date) {
	if(confirm("해당 적립내역을 삭제하시겠습니까?")) {
		document.form1.type.value="delete";
		document.form1.date.value=date;
		document.form1.submit();
	}
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title><?=$name?>회원님의 적립금 내역</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p><?=$name?>회원님의 적립금 내역</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<TABLE WIDTH="650" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=type>
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=id value="<?=$id?>">
<input type=hidden name=date>
<TR>
	<TD style="padding-top:3pt; padding-bottom:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><p align="right"><font color="black"><span style="font-size:8pt;">* 현재 총</span><span style="font-size:8pt;" class="font_orange"><b><?=number_format($reserve)?>원</b></span><span style="font-size:8pt;">을 적립하셨습니다.</span></font></td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<TR align=center>
			<th>날짜</th>
			<th>적립금</th>
			<th>적립내역</th>
			<!-- <th>만료일</th>
            <th>포인트합계</th> -->
            <!-- <th>삭제</th> -->
		</TR>
<?php
		$colspan=5;
		$sql = "SELECT COUNT(*) as t_count FROM tblpoint WHERE mem_id = '{$id}' ";
		$paging = new Paging($sql,5,10);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;
		
		$sql = "SELECT * FROM tblpoint WHERE mem_id = '{$id}' ORDER BY pid DESC ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$str_date = substr($row->regdt,0,4)."/".substr($row->regdt,4,2)."/".substr($row->regdt,6,2);
            $expire_date = substr($row->expire_date,0,4)."/".substr($row->expire_date,4,2)."/".substr($row->expire_date,6,2);
			echo "<tr>\n";
			echo "	<TD>{$str_date}</td>\n";
			echo "	<TD><b><span class=\"font_orange\">".number_format($row->point)."원</span></b></TD>\n";
			echo "	<TD>{$row->body}</TD>\n";
            //echo "	<TD>{$expire_date}</TD>\n";
            //echo "	<TD>".number_format($row->tot_point)."</TD>\n";
			//echo "	<TD><A HREF=\"javascript:ReserveDelete('{$row->date}');\"><img src=\"images/btn_del.gif\" width=\"50\" height=\"22\" border=\"0\"></A></td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			echo "<tr><TD align=center class=\"td_con2\" colspan={$colspan}>적립금 내역이 없습니다.</td></tr>";
		}
?>
		</TABLE>
        </div>
		</td>
	</tr>
	<!-- <tr>
		<td height="30" align=center><span style="font-size:8pt;" class="font_orange">*해당 적립내역을 삭제하셔도 실제 적립금은 변경되지 않습니다.</span></td>
	</tr> -->
<?php	
		echo "<tr>\n";
		echo "	<td><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="10" border=0></a></TD>
</TR>
</form>
</TABLE>
</body>
</html>
